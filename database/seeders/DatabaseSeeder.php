<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\KidsWear\Catalog\Models\AgeGroup;
use App\Modules\KidsWear\Catalog\Models\BomItem;
use App\Modules\KidsWear\Catalog\Models\Category;
use App\Modules\KidsWear\Catalog\Models\Colorway;
use App\Modules\KidsWear\Catalog\Models\Season;
use App\Modules\KidsWear\Catalog\Models\SizeChart;
use App\Modules\KidsWear\Catalog\Models\SizeChartRow;
use App\Modules\KidsWear\Catalog\Models\Style;
use App\Modules\KidsWear\Catalog\Models\TechPack;
use App\Modules\KidsWear\Finance\Services\CostingService;
use App\Modules\KidsWear\Hr\Models\Department;
use App\Modules\KidsWear\Hr\Models\Designation;
use App\Modules\KidsWear\Hr\Models\Employee;
use App\Modules\KidsWear\Hr\Models\Holiday;
use App\Modules\KidsWear\Hr\Models\LeaveRequest;
use App\Modules\KidsWear\Hr\Models\LeaveType;
use App\Modules\KidsWear\Hr\Models\Shift;
use App\Modules\KidsWear\Hr\Services\AttendanceService;
use App\Modules\KidsWear\Inventory\Models\Location;
use App\Modules\KidsWear\Inventory\Models\Material;
use App\Modules\KidsWear\Inventory\Services\StockService;
use App\Modules\KidsWear\Maintenance\Models\BreakdownLog;
use App\Modules\KidsWear\Maintenance\Models\Machine;
use App\Modules\KidsWear\Maintenance\Models\MaintenanceSchedule;
use App\Modules\KidsWear\Orders\Models\Buyer;
use App\Modules\KidsWear\Orders\Models\BuyerOrder;
use App\Modules\KidsWear\Orders\Services\OrderConversionService;
use App\Modules\KidsWear\Procurement\Models\PurchaseOrder;
use App\Modules\KidsWear\Procurement\Models\Supplier;
use App\Modules\KidsWear\Procurement\Services\ReceiptService;
use App\Modules\KidsWear\Production\Models\DailyTarget;
use App\Modules\KidsWear\Production\Models\ProductionLine;
use App\Modules\KidsWear\Production\Services\ProductionService;
use App\Modules\KidsWear\Quality\Models\DefectType;
use App\Modules\KidsWear\Quality\Models\Inspection;
use App\Modules\KidsWear\Support\Company;
use App\Modules\KidsWear\Support\ModuleManager;
use App\Modules\KidsWear\Support\SizeMatrix;
use App\Modules\KidsWear\Support\Status;
use App\Modules\KidsWear\Website\Models\Certification;
use App\Modules\KidsWear\Website\Models\GalleryItem;
use App\Modules\KidsWear\Website\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use ImranDevBd\AttendanceHub\Models\AttendanceLog;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach (ModuleManager::permissions() as $permission) {
            Permission::findOrCreate($permission);
        }
        foreach (config('kidswear.roles') as $name => $grants) {
            $role = Role::findOrCreate($name);
            $role->syncPermissions($grants === '*' ? Permission::all() : $grants);
        }

        $accounts = [
            'super-admin' => ['Rina Chowdhury', 'super@rongdhonu.test'],
            'factory-manager' => ['Faruk Hossain', 'manager@rongdhonu.test'],
            'production-supervisor' => ['Shila Akter', 'supervisor@rongdhonu.test'],
            'quality-controller' => ['Nusrat Jahan', 'qc@rongdhonu.test'],
            'inventory-manager' => ['Kamal Uddin', 'inventory@rongdhonu.test'],
            'sales' => ['Mitu Rahman', 'sales@rongdhonu.test'],
            'hr' => ['Tania Islam', 'hr@rongdhonu.test'],
            'accounts' => ['Jahid Hasan', 'accounts@rongdhonu.test'],
            'viewer' => ['Guest Buyer', 'viewer@rongdhonu.test'],
        ];
        foreach ($accounts as $role => [$name, $email]) {
            $user = User::query()->updateOrCreate(['email' => $email], [
                'name' => $name,
                'password' => Hash::make('password'),
                'locale' => 'en',
                'is_active' => true,
            ]);
            $user->syncRoles([$role]);
        }

        Company::save(Company::defaults());

        $departments = collect([
            ['Cutting', 'CUT'],
            ['Sewing', 'SEW'],
            ['Finishing', 'FIN'],
            ['Quality', 'QC'],
            ['Store', 'STR'],
        ])->map(fn ($row) => Department::query()->create(['name' => $row[0], 'code' => $row[1]]));

        $designations = collect(['Supervisor', 'Operator', 'Helper', 'Quality inspector', 'Store keeper'])->map(
            fn ($name, $index) => Designation::query()->create([
                'name' => $name,
                'department_id' => $departments[$index % $departments->count()]->id,
            ])
        );

        $day = Shift::query()->create([
            'name' => 'General',
            'start_time' => '08:00',
            'end_time' => '17:00',
            'grace_minutes' => 10,
            'break_minutes' => 60,
            'half_day_minutes' => 240,
            'full_day_minutes' => 480,
            'overtime_allowed' => true,
        ]);
        Shift::query()->create([
            'name' => 'Overtime',
            'start_time' => '17:30',
            'end_time' => '20:30',
            'grace_minutes' => 5,
            'break_minutes' => 0,
            'half_day_minutes' => 90,
            'full_day_minutes' => 180,
        ]);

        $names = [
            ['Ayesha Khatun', 'আয়েশা খাতুন'],
            ['Rahim Mia', 'রহিম মিয়া'],
            ['Sadia Akter', 'সাদিয়া আক্তার'],
            ['Imran Ali', 'ইমরান আলী'],
            ['Lamia Noor', 'লামিয়া নূর'],
            ['Jamal Uddin', 'জামাল উদ্দিন'],
            ['Priya Das', 'প্রিয়া দাস'],
            ['Sohel Rana', 'সোহেল রানা'],
            ['Maliha Chowdhury', 'মালিহা চৌধুরী'],
            ['Kabir Ahmed', 'কবির আহমেদ'],
            ['Rokeya Begum', 'রোকেয়া বেগম'],
            ['Naim Islam', 'নাইম ইসলাম'],
        ];
        $employees = collect($names)->map(fn ($name, $index) => Employee::query()->create([
            'name' => $name[0],
            'name_bn' => $name[1],
            'department_id' => $departments[$index % $departments->count()]->id,
            'designation_id' => $designations[$index % $designations->count()]->id,
            'shift_id' => $day->id,
            'phone' => '01700000'.str_pad((string) $index, 3, '0', STR_PAD_LEFT),
            'joined_on' => now()->subMonths(8 + $index)->toDateString(),
            'basic_salary' => 14500 + ($index * 350),
            'status' => Status::ACTIVE,
            'gender' => $index % 2 === 0 ? 'female' : 'male',
        ]));

        $casual = LeaveType::query()->create(['name' => 'Casual', 'days_per_year' => 10, 'paid' => true]);
        LeaveType::query()->create(['name' => 'Sick', 'days_per_year' => 14, 'paid' => true]);
        LeaveRequest::query()->create([
            'employee_id' => $employees[2]->id,
            'leave_type_id' => $casual->id,
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'days' => 2,
            'reason' => 'Family visit to Mymensingh',
            'status' => Status::PENDING,
        ]);

        foreach ([
            ['Language Martyrs\' Day', 'ভাষা শহিদ দিবস', '2026-02-21'],
            ['Independence Day', 'স্বাধীনতা দিবস', '2026-03-26'],
            ['Pohela Boishakh', 'পহেলা বৈশাখ', '2026-04-14'],
            ['May Day', 'মে দিবস', '2026-05-01'],
            ['Victory Day', 'বিজয় দিবস', '2026-12-16'],
        ] as [$en, $bn, $date]) {
            Holiday::query()->create(['name' => $en, 'name_bn' => $bn, 'observed_on' => $date]);
        }

        $categories = collect([
            ['T-shirts', 'টি-শার্ট', 'tees'],
            ['Rompers', 'রম্পার', 'rompers'],
            ['Sets', 'সেট', 'sets'],
            ['Dresses', 'ড্রেস', 'dresses'],
            ['Pants', 'প্যান্ট', 'pants'],
        ])->map(fn ($row, $index) => Category::query()->create([
            'name' => $row[0], 'name_bn' => $row[1], 'sort' => $index, 'is_active' => true,
        ]));

        $ages = collect([
            ['baby', '0–12 months', '০–১২ মাস', 0, 12],
            ['toddler', '1–3 years', '১–৩ বছর', 12, 36],
            ['kids', '4–7 years', '৪–৭ বছর', 48, 84],
            ['junior', '8–12 years', '৮–১২ বছর', 96, 144],
        ])->map(fn ($row, $index) => AgeGroup::query()->create([
            'code' => $row[0], 'label' => $row[1], 'label_bn' => $row[2], 'min_months' => $row[3], 'max_months' => $row[4], 'sort' => $index,
        ]));

        $ss = Season::query()->create(['name' => 'Spring Summer 2026', 'code' => 'SS26', 'year' => 2026, 'starts_on' => '2026-01-01', 'ends_on' => '2026-06-30']);
        Season::query()->create(['name' => 'Autumn Winter 2026', 'code' => 'AW26', 'year' => 2026, 'starts_on' => '2026-07-01', 'ends_on' => '2026-12-31']);

        $suppliers = collect([
            ['Narayanganj Knit', 'NKN', 'fabric'],
            ['Trim House Gazipur', 'THG', 'trim'],
            ['Pack BD', 'PBD', 'packing'],
        ])->map(fn ($row) => Supplier::query()->create([
            'name' => $row[0], 'code' => $row[1], 'category' => $row[2], 'country' => 'Bangladesh', 'lead_time_days' => 12, 'phone' => '01810000000',
        ]));

        $locations = collect([
            ['Main fabric store', 'STR-01', 'store'],
            ['Cutting floor', 'CUT-01', 'cutting'],
            ['Finished goods', 'FG-01', 'fg'],
        ])->map(fn ($row) => Location::query()->create(['name' => $row[0], 'code' => $row[1], 'type' => $row[2]]));

        $materials = collect([
            ['FAB-SJ-180', 'Single jersey 180 GSM', 'fabric', 'kg', 180, 4.20, 80],
            ['FAB-RIB', '1x1 rib', 'fabric', 'kg', 220, 4.80, 30],
            ['TRM-SNAP', 'Plastic snap', 'trim', 'gross', null, 1.10, 20],
            ['TRM-LBL', 'Main label', 'trim', 'pcs', null, 0.04, 500],
            ['TRM-BTN', 'Pearl button', 'accessory', 'gross', null, 2.40, 10],
            ['PKG-POLY', 'Poly bag', 'packing', 'pcs', null, 0.03, 1000],
            ['PKG-CTN', 'Export carton', 'packing', 'pcs', null, 0.85, 100],
        ])->map(fn ($row) => Material::query()->create([
            'sku' => $row[0],
            'name' => $row[1],
            'type' => $row[2],
            'unit' => $row[3],
            'gsm' => $row[4],
            'last_cost' => $row[5],
            'reorder_level' => $row[6],
            'preferred_supplier_id' => $suppliers->firstWhere('category', $row[2] === 'fabric' ? 'fabric' : ($row[2] === 'packing' ? 'packing' : 'trim'))?->id,
        ]));

        $stock = app(StockService::class);
        foreach ($materials as $material) {
            $qty = $material->type === 'fabric' ? 400 : 2000;
            if ($material->sku === 'TRM-SNAP') {
                $qty = 5;
            }
            $stock->receive($material, $locations[0], $qty, 'OPEN', 'Opening balance');
        }

        $styleDefs = [
            ['KW-2601', 'Rainbow stripe tee', 'রেইনবো স্ট্রাইপ টি', 0, 2, 'boys', 'Single jersey', 180, ['2Y' => 24, '4Y' => 28, '6Y' => 32, '8Y' => 36]],
            ['KW-2602', 'Cloud romper', 'ক্লাউড রম্পার', 1, 0, 'baby', 'Interlock', 200, ['0-3M' => 38, '3-6M' => 41, '6-12M' => 44]],
            ['KW-2603', 'Garden party dress', 'গার্ডেন পার্টি ড্রেস', 3, 1, 'girls', 'Cotton poplin', 140, ['2Y' => 34, '4Y' => 38, '6Y' => 42]],
            ['KW-2604', 'Sunday jogger set', 'সানডে জগার সেট', 2, 2, 'unisex', 'French terry', 240, ['3Y' => 30, '5Y' => 34, '7Y' => 38]],
            ['KW-2605', 'Soft day pant', 'সফট ডে প্যান্ট', 4, 2, 'unisex', 'Twill', 180, ['4Y' => 24, '6Y' => 26, '8Y' => 28, '10Y' => 30]],
            ['KW-2606', 'Hooded play set', 'হুডেড প্লে সেট', 2, 3, 'boys', 'Fleece', 280, ['6Y' => 40, '8Y' => 44, '10Y' => 48, '12Y' => 52]],
        ];

        $styles = collect();
        foreach ($styleDefs as $index => $def) {
            $style = Style::query()->create([
                'style_code' => $def[0],
                'name' => $def[1],
                'name_bn' => $def[2],
                'category_id' => $categories[$def[3]]->id,
                'age_group_id' => $ages[$def[4]]->id,
                'season_id' => $ss->id,
                'gender' => $def[5],
                'fabric' => $def[6],
                'gsm' => $def[7],
                'status' => Status::PUBLISHED,
                'published_at' => now()->subDays(10 - $index),
                'excerpt' => $def[1].' for export kids wear.',
                'description' => $def[1].' is cut in Gazipur with a full size run, coverstitch hems and a soft hand-feel for everyday wear.',
                'description_bn' => $def[2].' গাজীপুরে তৈরি, নরম হাতের অনুভূতি ও পরিষ্কার সেলাইসহ।',
                'care_instructions' => 'Machine wash cold. Do not bleach.',
                'meta_title' => $def[1].' | Rongdhonu Kids Wear',
                'meta_description' => 'Export '.$def[1].' from Gazipur, Bangladesh.',
            ]);
            Colorway::query()->create(['style_id' => $style->id, 'name' => 'Coral', 'code' => 'COR', 'hex' => '#e15b3a', 'is_primary' => true]);
            Colorway::query()->create(['style_id' => $style->id, 'name' => 'Leaf', 'code' => 'LEF', 'hex' => '#21584a']);
            $chart = SizeChart::query()->create(['style_id' => $style->id, 'name' => 'Body measurements', 'unit' => 'cm']);
            foreach ($def[8] as $size => $chest) {
                SizeChartRow::query()->create([
                    'size_chart_id' => $chart->id,
                    'size_code' => $size,
                    'measurements' => ['chest' => $chest, 'length' => $chest + 8, 'waist' => $chest - 2],
                ]);
            }
            TechPack::query()->create([
                'style_id' => $style->id,
                'version' => 'A',
                'construction' => 'Single needle shoulder, twin needle hem, coverstitch neck.',
                'stitching' => 'SPI 11–12. Safety stitch on baby styles.',
                'print_placement' => 'Chest print 8 cm below HSP.',
                'approved_at' => now(),
            ]);
            BomItem::query()->create(['style_id' => $style->id, 'material_id' => $materials[0]->id, 'component' => 'fabric', 'name' => $materials[0]->name, 'unit' => 'kg', 'consumption' => 0.18, 'wastage_percent' => 4]);
            BomItem::query()->create(['style_id' => $style->id, 'material_id' => $materials[2]->id, 'component' => 'trim', 'name' => $materials[2]->name, 'unit' => 'gross', 'consumption' => 0.02, 'wastage_percent' => 2]);
            BomItem::query()->create(['style_id' => $style->id, 'material_id' => $materials[5]->id, 'component' => 'packing', 'name' => $materials[5]->name, 'unit' => 'pcs', 'consumption' => 1, 'wastage_percent' => 1]);
            app(CostingService::class)->save($style, [
                'fabric_cost' => 1.15, 'trim_cost' => 0.22, 'cm_cost' => 0.85, 'packing_cost' => 0.08, 'overhead' => 0.12, 'margin_percent' => 12,
            ]);
            $styles->push($style);
        }

        $buyers = collect([
            ['Northwind Kids', 'NWK', 'Sweden', 'USD', 'Anna Berg'],
            ['Little Harbour', 'LHB', 'United Kingdom', 'USD', 'Owen Clarke'],
            ['Costa Mini', 'CMI', 'Spain', 'EUR', 'Lucia Vega'],
            ['Shishu Bazar', 'SHB', 'Bangladesh', 'BDT', 'Farhana Islam'],
        ])->map(fn ($row) => Buyer::query()->create([
            'name' => $row[0], 'code' => $row[1], 'country' => $row[2], 'currency' => $row[3], 'contact_name' => $row[4], 'email' => strtolower($row[1]).'@buyers.test', 'payment_terms' => 'TT 30 days',
        ]));

        $matrix = ['2Y' => 400, '4Y' => 600, '6Y' => 600, '8Y' => 400];
        $order = BuyerOrder::query()->create([
            'buyer_id' => $buyers[0]->id,
            'order_date' => now()->subDays(12)->toDateString(),
            'delivery_date' => now()->addDays(28)->toDateString(),
            'currency' => 'USD',
            'status' => Status::CONFIRMED,
            'incoterm' => 'FOB',
            'destination' => 'Gothenburg',
            'confirmed_at' => now()->subDays(10),
        ]);
        $order->lines()->create([
            'style_id' => $styles[0]->id,
            'colorway_id' => $styles[0]->colorways()->first()->id,
            'size_matrix' => $matrix,
            'quantity' => SizeMatrix::total($matrix),
            'unit_price' => 2.85,
            'description' => 'SS26 program',
        ]);
        $production = app(OrderConversionService::class)->convert($order->fresh());
        $line = ProductionLine::query()->create(['name' => 'Line 03', 'section' => 'sewing', 'target_per_hour' => 95, 'supervisor_employee_id' => $employees[0]->id]);
        ProductionLine::query()->create(['name' => 'Cut table A', 'section' => 'cutting', 'target_per_hour' => 140]);
        $production->update(['line_id' => $line->id, 'status' => Status::SEWING, 'actual_start' => now()->subDays(4)->toDateString()]);
        DailyTarget::query()->create([
            'production_order_id' => $production->id,
            'line_id' => $line->id,
            'target_date' => now()->toDateString(),
            'stage' => Status::SEWING,
            'target_qty' => 760,
            'achieved_qty' => 640,
        ]);
        app(ProductionService::class)->record($production, [
            'line_id' => $line->id,
            'stage' => Status::SEWING,
            'entry_date' => now()->toDateString(),
            'quantity_out' => 640,
            'quantity_reject' => 18,
            'operator_employee_id' => $employees[1]->id,
        ]);

        $draft = BuyerOrder::query()->create([
            'buyer_id' => $buyers[1]->id,
            'order_date' => now()->toDateString(),
            'delivery_date' => now()->addDays(50)->toDateString(),
            'status' => Status::DRAFT,
            'destination' => 'Felixstowe',
            'currency' => 'USD',
        ]);
        $draft->lines()->create([
            'style_id' => $styles[2]->id,
            'size_matrix' => ['2Y' => 200, '4Y' => 300, '6Y' => 200],
            'quantity' => 700,
            'unit_price' => 4.10,
        ]);

        $late = BuyerOrder::query()->create([
            'buyer_id' => $buyers[2]->id,
            'order_date' => now()->subDays(40)->toDateString(),
            'delivery_date' => now()->subDays(2)->toDateString(),
            'status' => Status::IN_PRODUCTION,
            'currency' => 'EUR',
            'destination' => 'Valencia',
        ]);
        $late->lines()->create([
            'style_id' => $styles[4]->id,
            'size_matrix' => ['4Y' => 150, '6Y' => 150],
            'quantity' => 300,
            'unit_price' => 3.40,
        ]);
        $lateProduction = app(OrderConversionService::class)->convert($late->fresh());
        $lateProduction->update(['planned_end' => now()->subDays(2)->toDateString(), 'status' => Status::DELAYED]);

        foreach ([
            ['Open seam', 'seam', 'major'],
            ['Shade variation', 'shade', 'major'],
            ['Measurement deviation', 'measurement', 'major'],
            ['Needle damage', 'safety', 'critical'],
            ['Broken snap', 'trim', 'minor'],
            ['Oil stain', 'stain', 'major'],
        ] as $defect) {
            DefectType::query()->create(['name' => $defect[0], 'category' => $defect[1], 'severity' => $defect[2]]);
        }
        $inspection = Inspection::query()->create([
            'production_order_id' => $production->id,
            'type' => 'inline',
            'inspected_at' => now(),
            'inspector_id' => User::query()->where('email', 'qc@rongdhonu.test')->value('id'),
            'sample_size' => 80,
            'accepted_qty' => 74,
            'rejected_qty' => 6,
            'aql_level' => '2.5',
            'result' => Status::HOLD,
            'notes' => 'Open seam on the shoulder of size 4Y.',
        ]);
        $inspection->defects()->create(['defect_type_id' => DefectType::query()->where('name', 'Open seam')->value('id'), 'quantity' => 4, 'zone' => 'shoulder']);

        $purchase = PurchaseOrder::query()->create([
            'supplier_id' => $suppliers[0]->id,
            'order_date' => now()->subDays(6)->toDateString(),
            'expected_date' => now()->addDays(4)->toDateString(),
            'status' => Status::SENT,
            'currency' => 'BDT',
        ]);
        $purchase->items()->create([
            'material_id' => $materials[0]->id,
            'description' => $materials[0]->name,
            'quantity' => 250,
            'unit' => 'kg',
            'unit_price' => 420,
        ]);
        app(ReceiptService::class)->receiveAll($purchase->fresh(), $locations[0]);

        $machine = Machine::query()->create(['code' => 'SNL-14', 'name' => 'Single needle lockstitch', 'type' => 'Sewing', 'brand' => 'Juki', 'line_id' => $line->id, 'installed_on' => '2022-03-01', 'status' => Status::RUNNING]);
        Machine::query()->create(['code' => 'OVR-04', 'name' => '4-thread overlock', 'type' => 'Sewing', 'brand' => 'Pegasus', 'line_id' => $line->id, 'status' => Status::RUNNING]);
        Machine::query()->create(['code' => 'CUT-02', 'name' => 'Straight knife', 'type' => 'Cutting', 'brand' => 'Eastman', 'status' => Status::IDLE]);
        MaintenanceSchedule::query()->create([
            'machine_id' => $machine->id,
            'task' => 'Oil and needle-bar check',
            'frequency_days' => 14,
            'last_done_on' => now()->subDays(20)->toDateString(),
            'next_due_on' => now()->subDays(6)->toDateString(),
        ]);
        BreakdownLog::query()->create([
            'machine_id' => $machine->id,
            'started_at' => now()->subDays(1)->setTime(10, 15),
            'ended_at' => now()->subDays(1)->setTime(11, 5),
            'reason' => 'Looper timing slipped',
            'downtime_minutes' => 50,
            'action_taken' => 'Retimed and replaced needle',
            'reported_by' => User::query()->where('email', 'supervisor@rongdhonu.test')->value('id'),
        ]);

        $author = User::query()->where('email', 'sales@rongdhonu.test')->first();
        foreach ([
            ['Why infant size runs fail in the packing room', 'প্যাকিং রুমে শিশু সাইজ এলোমেলো হলে', 'A size matrix that lives only in Excel gets copied wrong. The bundle ticket has to carry the size.'],
            ['Shade bands before the lay', 'লেয়ারের আগে শেড ব্যান্ড', 'Kids wear rejects shade more harshly than adult basics. Band the rolls before the marker.'],
            ['What an inline check should stop', 'ইনলাইন চেক কী থামায়', 'Open seam and needle damage should stop the bundle, not wait for final AQL.'],
        ] as [$title, $titleBn, $body]) {
            Post::query()->create([
                'title' => $title,
                'title_bn' => $titleBn,
                'excerpt' => $body,
                'body' => $body."\n\nRongdhonu keeps the tech pack, the lay and the inspection on the same style code.",
                'body_bn' => $titleBn.' — একই স্টাইল কোডে টেক প্যাক, লেয়ার এবং ইনস্পেকশন থাকে।',
                'published_at' => now()->subDays(3),
                'author_id' => $author?->id,
            ]);
        }

        foreach ([
            ['Cutting room', 'Banded jersey ready for the marker', 'floor'],
            ['Line 03', 'Sunday jogger set on the seam', 'floor'],
            ['Finishing', 'Snap attach and thread trim', 'floor'],
            ['Packing', 'Size stickers on the poly bag', 'packing'],
        ] as $index => $item) {
            GalleryItem::query()->create(['title' => $item[0], 'caption' => $item[1], 'image_path' => 'swatch-'.$index, 'category' => $item[2], 'sort' => $index]);
        }

        foreach ([
            ['BSCI', 'BSCI', 'amfori', 'Social management system audited for the export floor.'],
            ['OEKO-TEX', 'OEKO-TEX', 'OEKO-TEX', 'Standard 100 for baby and children skin contact.'],
            ['WRAP', 'WRAP', 'WRAP', 'Workplace conditions across sewing and finishing.'],
            ['GOTS', 'GOTS', 'Global Organic Textile Standard', 'Organic cotton programs when the buyer specifies them.'],
        ] as $cert) {
            Certification::query()->create([
                'name' => $cert[0],
                'code' => $cert[1],
                'issuer' => $cert[2],
                'summary' => $cert[3],
                'summary_bn' => $cert[0].' সনদ রপ্তানি শিশু পোশাকের জন্য।',
                'valid_until' => now()->addYear()->toDateString(),
                'is_public' => true,
            ]);
        }

        foreach ($employees->take(8) as $index => $employee) {
            $in = now()->setTime(8, $index < 3 ? 6 : 32);
            AttendanceLog::query()->create([
                'device_user_id' => $employee->employee_code,
                'employee_id' => $employee->employee_code,
                'punched_at' => $in,
                'punch_hash' => hash('sha256', $employee->employee_code.'|in|'.$in->toDateString()),
                'punch_type' => 'check_in',
                'verify_mode' => 'fingerprint',
            ]);
            $out = now()->setTime(17, 25);
            AttendanceLog::query()->create([
                'device_user_id' => $employee->employee_code,
                'employee_id' => $employee->employee_code,
                'punched_at' => $out,
                'punch_hash' => hash('sha256', $employee->employee_code.'|out|'.$out->toDateString()),
                'punch_type' => 'check_out',
                'verify_mode' => 'fingerprint',
            ]);
            app(AttendanceService::class)->rollup($employee, now()->toDateString());
        }
    }
}
