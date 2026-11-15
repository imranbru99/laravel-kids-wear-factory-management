<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\KidsWear\Catalog\Models\BomItem;
use App\Modules\KidsWear\Catalog\Models\Style;
use App\Modules\KidsWear\Hr\Models\Employee;
use App\Modules\KidsWear\Hr\Models\Shift;
use App\Modules\KidsWear\Hr\Services\AttendanceService;
use App\Modules\KidsWear\Inventory\Models\Material;
use App\Modules\KidsWear\Orders\Models\Buyer;
use App\Modules\KidsWear\Orders\Models\BuyerOrder;
use App\Modules\KidsWear\Orders\Services\OrderConversionService;
use App\Modules\KidsWear\Production\Models\Bundle;
use App\Modules\KidsWear\Production\Models\MaterialRequirement;
use App\Modules\KidsWear\Settings\Models\Setting;
use App\Modules\KidsWear\Support\Status;
use App\Modules\KidsWear\Website\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ImranDevBd\AttendanceHub\Models\AttendanceLog;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FactoryFloorTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_and_catalog_publish_a_style_by_slug(): void
    {
        $style = Style::query()->create([
            'name' => 'Cloud romper',
            'status' => Status::PUBLISHED,
            'published_at' => now(),
            'excerpt' => 'Soft interlock for the first year.',
        ]);

        $this->get('/')->assertRedirect('/en');
        $this->get('/en')->assertOk()->assertSee('Cloud romper');
        $this->get('/en/styles')->assertOk()->assertSee('Cloud romper');
        $this->get('/en/styles/'.$style->slug)->assertOk()->assertSee($style->style_code);
        $this->get('/bn')->assertOk();
        $this->get('/sitemap.xml')->assertOk();
    }

    public function test_buyer_inquiry_requires_a_company(): void
    {
        $this->from('/en/contact')->post('/en/inquiry', [
            'type' => 'buyer',
            'name' => 'Anna Berg',
            'email' => 'anna@northwind.test',
            'message' => 'Need 2,000 tees.',
        ])->assertSessionHasErrors('company');

        $this->assertSame(0, Inquiry::query()->count());

        $this->from('/en/contact')->post('/en/inquiry', [
            'type' => 'contact',
            'name' => 'Office',
            'email' => 'office@northwind.test',
            'message' => 'Please call the merchandiser.',
        ])->assertRedirect('/en/contact');

        $this->assertSame(1, Inquiry::query()->count());
    }

    public function test_confirming_an_order_builds_material_need_and_bundles(): void
    {
        $style = Style::query()->create(['name' => 'Rainbow stripe tee', 'status' => Status::PUBLISHED]);
        $material = Material::query()->create([
            'sku' => 'FAB-SJ-180',
            'name' => 'Single jersey 180 GSM',
            'type' => 'fabric',
            'unit' => 'kg',
        ]);
        BomItem::query()->create([
            'style_id' => $style->id,
            'material_id' => $material->id,
            'component' => 'fabric',
            'name' => $material->name,
            'unit' => 'kg',
            'consumption' => 0.2,
            'wastage_percent' => 5,
        ]);
        $buyer = Buyer::query()->create(['name' => 'Northwind Kids', 'code' => 'NWK', 'country' => 'Sweden']);
        $order = BuyerOrder::query()->create([
            'buyer_id' => $buyer->id,
            'order_date' => now()->toDateString(),
            'delivery_date' => now()->addMonth()->toDateString(),
            'status' => Status::CONFIRMED,
        ]);
        $order->lines()->create([
            'style_id' => $style->id,
            'size_matrix' => ['2Y' => 40, '4Y' => 20],
            'quantity' => 60,
            'unit_price' => 2.85,
        ]);

        $production = app(OrderConversionService::class)->convert($order->fresh());

        $need = MaterialRequirement::query()->where('production_order_id', $production->id)->first();
        $this->assertEquals(12.6, (float) $need->required_qty);
        $this->assertEquals(12.6, (float) $need->shortage_qty);
        $this->assertSame(3, Bundle::query()->where('production_order_id', $production->id)->count());
        $this->assertSame(Status::IN_PRODUCTION, $order->fresh()->status);
    }

    public function test_a_late_punch_rolls_up_as_late(): void
    {
        $shift = Shift::query()->create([
            'name' => 'General',
            'start_time' => '08:00',
            'end_time' => '17:00',
            'grace_minutes' => 10,
            'break_minutes' => 60,
            'half_day_minutes' => 240,
            'full_day_minutes' => 480,
            'overtime_allowed' => true,
        ]);
        $employee = Employee::query()->create([
            'name' => 'Ayesha Khatun',
            'shift_id' => $shift->id,
            'status' => Status::ACTIVE,
        ]);
        $day = now()->toDateString();
        foreach (['08:35' => 'check_in', '17:20' => 'check_out'] as $time => $type) {
            AttendanceLog::query()->create([
                'device_user_id' => $employee->employee_code,
                'employee_id' => $employee->employee_code,
                'punched_at' => $day.' '.$time.':00',
                'punch_hash' => hash('sha256', $employee->employee_code.$type),
                'punch_type' => $type,
                'verify_mode' => 'fingerprint',
            ]);
        }

        $summary = app(AttendanceService::class)->rollup($employee, $day);

        $this->assertSame(Status::LATE, $summary->status);
        $this->assertGreaterThanOrEqual(20, $summary->late_minutes);
    }

    public function test_a_viewer_cannot_create_a_style_and_a_disabled_module_is_hidden(): void
    {
        Permission::findOrCreate('catalog.view');
        Permission::findOrCreate('catalog.manage');
        $viewer = User::factory()->create(['is_active' => true]);
        $viewer->assignRole(Role::findOrCreate('viewer'));

        $this->actingAs($viewer)
            ->post(route('admin.resources.store', 'styles'), ['name' => 'Secret tee'])
            ->assertForbidden();

        $this->assertSame(0, Style::query()->count());

        Setting::putValue('modules', ['catalog' => false]);
        $manager = User::factory()->create(['is_active' => true]);
        $manager->assignRole(Role::findOrCreate('super-admin'));

        $this->actingAs($manager)
            ->get(route('admin.resources.index', 'styles'))
            ->assertNotFound();
    }

    public function test_an_active_user_can_open_the_factory_desk(): void
    {
        $user = User::factory()->create([
            'email' => 'super@rongdhonu.test',
            'password' => 'password',
            'is_active' => true,
        ]);
        $user->assignRole(Role::findOrCreate('super-admin'));

        $this->post('/login', [
            'email' => 'super@rongdhonu.test',
            'password' => 'password',
        ])->assertRedirect('/admin');

        $this->get('/admin')->assertOk()->assertSee('Factory floor');
    }
}
