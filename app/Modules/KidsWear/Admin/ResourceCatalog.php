<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Admin;

use App\Modules\KidsWear\Catalog\Models\AgeGroup;
use App\Modules\KidsWear\Catalog\Models\Category;
use App\Modules\KidsWear\Catalog\Models\Season;
use App\Modules\KidsWear\Catalog\Models\Style;
use App\Modules\KidsWear\Finance\Models\Invoice;
use App\Modules\KidsWear\Hr\Models\Department;
use App\Modules\KidsWear\Hr\Models\Designation;
use App\Modules\KidsWear\Hr\Models\Employee;
use App\Modules\KidsWear\Hr\Models\LeaveRequest;
use App\Modules\KidsWear\Hr\Models\LeaveType;
use App\Modules\KidsWear\Hr\Models\Shift;
use App\Modules\KidsWear\Inventory\Models\Location;
use App\Modules\KidsWear\Inventory\Models\Material;
use App\Modules\KidsWear\Maintenance\Models\BreakdownLog;
use App\Modules\KidsWear\Maintenance\Models\Machine;
use App\Modules\KidsWear\Maintenance\Models\MaintenanceSchedule;
use App\Modules\KidsWear\Orders\Models\Buyer;
use App\Modules\KidsWear\Orders\Models\BuyerOrder;
use App\Modules\KidsWear\Procurement\Models\PurchaseOrder;
use App\Modules\KidsWear\Procurement\Models\Supplier;
use App\Modules\KidsWear\Production\Models\ProductionLine;
use App\Modules\KidsWear\Production\Models\ProductionOrder;
use App\Modules\KidsWear\Quality\Models\DefectType;
use App\Modules\KidsWear\Quality\Models\Inspection;
use App\Modules\KidsWear\Website\Models\Certification;
use App\Modules\KidsWear\Website\Models\Inquiry;
use App\Modules\KidsWear\Website\Models\Post;
use ImranDevBD\LaravelAdminPanel\Navigation\NavigationGroup;
use ImranDevBD\LaravelAdminPanel\Navigation\NavigationItem;

final class ResourceCatalog
{
    /** @return array<string, array<string, mixed>> */
    public static function definitions(): array
    {
        return [
            'departments' => self::def(Department::class, 'hr', 'People', 'Departments', 'users', 'name', ['name', 'code'], [
                self::text('name', 'Name', true),
                self::text('code', 'Code', true),
                self::area('description', 'Description'),
            ]),
            'designations' => self::def(Designation::class, 'hr', 'People', 'Designations', 'users', 'name', ['name'], [
                self::text('name', 'Name', true),
                self::select('department_id', 'Department', Department::class),
            ], ['department']),
            'shifts' => self::def(Shift::class, 'hr', 'People', 'Shifts', 'bell', 'name', ['name', 'start_time', 'end_time'], [
                self::text('name', 'Name', true),
                self::text('start_time', 'Start (HH:MM)', true),
                self::text('end_time', 'End (HH:MM)', true),
                self::number('grace_minutes', 'Grace minutes'),
                self::number('break_minutes', 'Break minutes'),
                self::number('half_day_minutes', 'Half day minutes'),
                self::number('full_day_minutes', 'Full day minutes'),
                self::check('overtime_allowed', 'Overtime allowed'),
            ]),
            'employees' => self::def(Employee::class, 'hr', 'People', 'Employees', 'users', 'name', ['employee_code', 'name', 'status'], [
                self::text('name', 'Name', true),
                self::text('name_bn', 'Name (Bengali)'),
                self::text('employee_code', 'Employee code'),
                self::select('department_id', 'Department', Department::class),
                self::select('designation_id', 'Designation', Designation::class),
                self::select('shift_id', 'Shift', Shift::class),
                self::text('phone', 'Phone'),
                self::text('email', 'Email'),
                self::select('gender', 'Gender', null, ['female' => 'Female', 'male' => 'Male']),
                self::date('joined_on', 'Joined'),
                self::number('basic_salary', 'Basic salary'),
                self::select('status', 'Status', null, ['active' => 'Active', 'probation' => 'Probation', 'left' => 'Left']),
                self::text('emergency_contact', 'Emergency contact'),
            ], ['department', 'designation', 'shift']),
            'leave-requests' => self::def(LeaveRequest::class, 'hr', 'People', 'Leave', 'bell', 'id', ['status'], [
                self::select('employee_id', 'Employee', Employee::class, null, 'name', true),
                self::select('leave_type_id', 'Type', LeaveType::class, null, 'name', true),
                self::date('start_date', 'Start', true),
                self::date('end_date', 'End', true),
                self::number('days', 'Days', true),
                self::area('reason', 'Reason'),
                self::select('status', 'Status', null, ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
            ], ['employee', 'leaveType']),
            'categories' => self::def(Category::class, 'catalog', 'Product', 'Categories', 'folder', 'name', ['name', 'slug'], [
                self::text('name', 'Name', true),
                self::text('name_bn', 'Name (Bengali)'),
                self::area('description', 'Description'),
                self::number('sort', 'Sort'),
                self::check('is_active', 'Active'),
            ]),
            'age-groups' => self::def(AgeGroup::class, 'catalog', 'Product', 'Age groups', 'users', 'label', ['code', 'label'], [
                self::text('code', 'Code', true),
                self::text('label', 'Label', true),
                self::text('label_bn', 'Label (Bengali)'),
                self::number('min_months', 'Min months'),
                self::number('max_months', 'Max months'),
                self::number('sort', 'Sort'),
            ]),
            'seasons' => self::def(Season::class, 'catalog', 'Product', 'Seasons', 'sparkles', 'name', ['name', 'code', 'year'], [
                self::text('name', 'Name', true),
                self::text('code', 'Code', true),
                self::number('year', 'Year', true),
                self::date('starts_on', 'Starts'),
                self::date('ends_on', 'Ends'),
            ]),
            'styles' => self::def(Style::class, 'catalog', 'Product', 'Styles', 'package', 'name', ['style_code', 'name', 'status'], [
                self::text('style_code', 'Style code'),
                self::text('name', 'Name', true),
                self::text('name_bn', 'Name (Bengali)'),
                self::select('category_id', 'Category', Category::class),
                self::select('season_id', 'Season', Season::class),
                self::select('age_group_id', 'Age group', AgeGroup::class, null, 'label'),
                self::select('gender', 'Gender', null, ['girls' => 'Girls', 'boys' => 'Boys', 'unisex' => 'Unisex', 'baby' => 'Baby']),
                self::text('fabric', 'Fabric'),
                self::number('gsm', 'GSM'),
                self::text('excerpt', 'Excerpt'),
                self::area('description', 'Description'),
                self::area('description_bn', 'Description (Bengali)'),
                self::area('care_instructions', 'Care'),
                self::select('status', 'Status', null, ['draft' => 'Draft', 'sampling' => 'Sampling', 'approved' => 'Approved', 'published' => 'Published']),
                self::text('meta_title', 'Meta title'),
                self::area('meta_description', 'Meta description'),
            ], ['category', 'ageGroup', 'season']),
            'buyers' => self::def(Buyer::class, 'orders', 'Orders', 'Buyers', 'users', 'name', ['code', 'name', 'country'], [
                self::text('name', 'Name', true),
                self::text('code', 'Code', true),
                self::text('country', 'Country', true),
                self::text('contact_name', 'Contact'),
                self::text('email', 'Email'),
                self::text('phone', 'Phone'),
                self::area('address', 'Address'),
                self::text('currency', 'Currency'),
                self::text('payment_terms', 'Payment terms'),
            ]),
            'buyer-orders' => self::def(BuyerOrder::class, 'orders', 'Orders', 'Buyer orders', 'package', 'number', ['number', 'status', 'delivery_date'], [
                self::select('buyer_id', 'Buyer', Buyer::class, null, 'name', true),
                self::date('order_date', 'Order date', true),
                self::date('delivery_date', 'Delivery', true),
                self::text('currency', 'Currency'),
                self::select('status', 'Status', null, ['draft' => 'Draft', 'confirmed' => 'Confirmed', 'in_production' => 'In production', 'shipped' => 'Shipped', 'closed' => 'Closed', 'cancelled' => 'Cancelled']),
                self::text('incoterm', 'Incoterm'),
                self::text('destination', 'Destination'),
                self::area('remarks', 'Remarks'),
            ], ['buyer']),
            'production-lines' => self::def(ProductionLine::class, 'production', 'Floor', 'Lines', 'layout-dashboard', 'name', ['name', 'section'], [
                self::text('name', 'Name', true),
                self::select('section', 'Section', null, ['cutting' => 'Cutting', 'sewing' => 'Sewing', 'finishing' => 'Finishing', 'packing' => 'Packing'], true),
                self::number('target_per_hour', 'Target / hour'),
                self::select('supervisor_employee_id', 'Supervisor', Employee::class),
                self::check('is_active', 'Active'),
            ], ['supervisor']),
            'production-orders' => self::def(ProductionOrder::class, 'production', 'Floor', 'Production orders', 'package', 'number', ['number', 'status', 'planned_end'], [
                self::select('buyer_order_id', 'Buyer order', BuyerOrder::class, null, 'number'),
                self::select('line_id', 'Line', ProductionLine::class),
                self::select('status', 'Status', null, ['planned' => 'Planned', 'cutting' => 'Cutting', 'sewing' => 'Sewing', 'finishing' => 'Finishing', 'packing' => 'Packing', 'completed' => 'Completed', 'delayed' => 'Delayed']),
                self::date('planned_start', 'Planned start'),
                self::date('planned_end', 'Planned end'),
                self::select('priority', 'Priority', null, ['normal' => 'Normal', 'rush' => 'Rush']),
                self::area('remarks', 'Remarks'),
            ], ['buyerOrder', 'line']),
            'locations' => self::def(Location::class, 'inventory', 'Stores', 'Locations', 'folder', 'name', ['code', 'name', 'type'], [
                self::text('name', 'Name', true),
                self::text('code', 'Code', true),
                self::select('type', 'Type', null, ['store' => 'Store', 'cutting' => 'Cutting', 'sewing' => 'Sewing', 'finishing' => 'Finishing', 'fg' => 'Finished goods']),
            ]),
            'materials' => self::def(Material::class, 'inventory', 'Stores', 'Materials', 'package', 'name', ['sku', 'name', 'type'], [
                self::text('sku', 'SKU', true),
                self::text('name', 'Name', true),
                self::select('type', 'Type', null, ['fabric' => 'Fabric', 'trim' => 'Trim', 'accessory' => 'Accessory', 'packing' => 'Packing'], true),
                self::text('unit', 'Unit', true),
                self::text('color', 'Color'),
                self::number('gsm', 'GSM'),
                self::number('reorder_level', 'Reorder level'),
                self::number('last_cost', 'Last cost'),
                self::select('preferred_supplier_id', 'Supplier', Supplier::class),
            ], ['supplier']),
            'defect-types' => self::def(DefectType::class, 'quality', 'Quality', 'Defect types', 'alert-circle', 'name', ['name', 'category', 'severity'], [
                self::text('name', 'Name', true),
                self::select('category', 'Category', null, ['seam' => 'Seam', 'shade' => 'Shade', 'measurement' => 'Measurement', 'safety' => 'Safety', 'stain' => 'Stain', 'trim' => 'Trim'], true),
                self::select('severity', 'Severity', null, ['critical' => 'Critical', 'major' => 'Major', 'minor' => 'Minor']),
                self::text('description', 'Description'),
            ]),
            'inspections' => self::def(Inspection::class, 'quality', 'Quality', 'Inspections', 'shield', 'id', ['type', 'result', 'inspected_at'], [
                self::select('production_order_id', 'Production order', ProductionOrder::class, null, 'number', true),
                self::select('type', 'Type', null, ['inline' => 'In-line', 'endline' => 'End-line', 'final' => 'Final'], true),
                self::date('inspected_at', 'Inspected', true),
                self::number('sample_size', 'Sample size', true),
                self::number('accepted_qty', 'Accepted'),
                self::number('rejected_qty', 'Rejected'),
                self::text('aql_level', 'AQL'),
                self::select('result', 'Result', null, ['pass' => 'Pass', 'fail' => 'Fail', 'hold' => 'Hold'], true),
                self::area('notes', 'Notes'),
                self::area('ai_notes', 'AI notes'),
            ], ['productionOrder']),
            'suppliers' => self::def(Supplier::class, 'procurement', 'Buying', 'Suppliers', 'users', 'name', ['code', 'name', 'category'], [
                self::text('name', 'Name', true),
                self::text('code', 'Code', true),
                self::select('category', 'Category', null, ['fabric' => 'Fabric', 'trim' => 'Trim', 'accessory' => 'Accessory', 'packing' => 'Packing']),
                self::text('contact_name', 'Contact'),
                self::text('email', 'Email'),
                self::text('phone', 'Phone'),
                self::area('address', 'Address'),
                self::text('country', 'Country'),
                self::number('lead_time_days', 'Lead time (days)'),
                self::number('rating', 'Rating'),
            ]),
            'purchase-orders' => self::def(PurchaseOrder::class, 'procurement', 'Buying', 'Purchase orders', 'package', 'number', ['number', 'status', 'expected_date'], [
                self::select('supplier_id', 'Supplier', Supplier::class, null, 'name', true),
                self::date('order_date', 'Order date', true),
                self::date('expected_date', 'Expected'),
                self::select('status', 'Status', null, ['draft' => 'Draft', 'sent' => 'Sent', 'partial' => 'Partial', 'received' => 'Received', 'cancelled' => 'Cancelled']),
                self::text('currency', 'Currency'),
                self::area('notes', 'Notes'),
            ], ['supplier']),
            'invoices' => self::def(Invoice::class, 'finance', 'Money', 'Invoices', 'package', 'number', ['number', 'type', 'status', 'total'], [
                self::select('buyer_id', 'Buyer', Buyer::class, null, 'name', true),
                self::select('buyer_order_id', 'Buyer order', BuyerOrder::class, null, 'number'),
                self::select('type', 'Type', null, ['commercial' => 'Commercial', 'proforma' => 'Proforma', 'packing_list' => 'Packing list']),
                self::date('invoice_date', 'Date', true),
                self::text('currency', 'Currency'),
                self::number('freight', 'Freight'),
                self::select('status', 'Status', null, ['draft' => 'Draft', 'issued' => 'Issued', 'paid' => 'Paid']),
                self::area('notes', 'Notes'),
            ], ['buyer']),
            'machines' => self::def(Machine::class, 'maintenance', 'Plant', 'Machines', 'settings', 'name', ['code', 'name', 'status'], [
                self::text('code', 'Code', true),
                self::text('name', 'Name', true),
                self::text('type', 'Type', true),
                self::text('brand', 'Brand'),
                self::select('line_id', 'Line', ProductionLine::class),
                self::date('installed_on', 'Installed'),
                self::select('status', 'Status', null, ['running' => 'Running', 'idle' => 'Idle', 'breakdown' => 'Breakdown']),
            ], ['line']),
            'maintenance' => self::def(MaintenanceSchedule::class, 'maintenance', 'Plant', 'Maintenance', 'settings', 'task', ['task', 'next_due_on'], [
                self::select('machine_id', 'Machine', Machine::class, null, 'name', true),
                self::text('task', 'Task', true),
                self::number('frequency_days', 'Every (days)'),
                self::date('last_done_on', 'Last done'),
                self::date('next_due_on', 'Next due'),
                self::area('notes', 'Notes'),
            ], ['machine']),
            'breakdowns' => self::def(BreakdownLog::class, 'maintenance', 'Plant', 'Breakdowns', 'alert-circle', 'reason', ['reason', 'downtime_minutes'], [
                self::select('machine_id', 'Machine', Machine::class, null, 'name', true),
                self::date('started_at', 'Started', true),
                self::date('ended_at', 'Ended'),
                self::text('reason', 'Reason', true),
                self::number('downtime_minutes', 'Downtime minutes'),
                self::area('action_taken', 'Action'),
            ], ['machine']),
            'posts' => self::def(Post::class, 'website', 'Website', 'Journal', 'folder', 'title', ['title', 'published_at'], [
                self::text('title', 'Title', true),
                self::text('title_bn', 'Title (Bengali)'),
                self::text('excerpt', 'Excerpt'),
                self::area('body', 'Body', true),
                self::area('body_bn', 'Body (Bengali)'),
                self::date('published_at', 'Publish at'),
            ]),
            'inquiries' => self::def(Inquiry::class, 'website', 'Website', 'Inquiries', 'bell', 'name', ['type', 'name', 'status'], [
                self::select('type', 'Type', null, ['contact' => 'Contact', 'buyer' => 'Buyer']),
                self::text('name', 'Name', true),
                self::text('company', 'Company'),
                self::text('email', 'Email', true),
                self::text('phone', 'Phone'),
                self::text('country', 'Country'),
                self::area('message', 'Message', true),
                self::select('status', 'Status', null, ['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed']),
            ]),
            'certifications' => self::def(Certification::class, 'website', 'Website', 'Certifications', 'shield', 'name', ['code', 'name'], [
                self::text('name', 'Name', true),
                self::text('code', 'Code', true),
                self::text('issuer', 'Issuer', true),
                self::date('valid_until', 'Valid until'),
                self::area('summary', 'Summary'),
                self::area('summary_bn', 'Summary (Bengali)'),
                self::check('is_public', 'Show on website'),
            ]),
        ];
    }

    /** @return array<string, mixed> */
    public static function find(string $key): array
    {
        $definitions = self::definitions();
        abort_unless(isset($definitions[$key]), 404);

        return $definitions[$key] + ['key' => $key];
    }

    /** @return list<NavigationGroup> */
    public static function navigation(): array
    {
        $grouped = [];
        foreach (self::definitions() as $key => $definition) {
            $grouped[$definition['group']][] = NavigationItem::make($definition['label'])
                ->icon($definition['icon'])
                ->url(url('/admin/resources/'.$key))
                ->visible(fn () => module_enabled($definition['module']) && (auth()->user()?->can($definition['module'].'.view') ?? false))
                ->active(fn () => request()->is('admin/resources/'.$key) || request()->is('admin/resources/'.$key.'/*'));
        }

        $groups = [
            NavigationGroup::make('Overview')->items([
                NavigationItem::make('Dashboard')->icon('layout-dashboard')->url(url('/admin'))
                    ->active(fn () => request()->is('admin') || request()->is('admin/dashboard')),
                NavigationItem::make('Attendance')->icon('users')->url(url('/admin/attendance'))
                    ->visible(fn () => module_enabled('hr') && (auth()->user()?->can('hr.view') ?? false)),
                NavigationItem::make('Floor board')->icon('layout-dashboard')->url(url('/admin/production/board'))
                    ->visible(fn () => module_enabled('production') && (auth()->user()?->can('production.view') ?? false)),
            ]),
        ];

        foreach ($grouped as $label => $items) {
            $groups[] = NavigationGroup::make($label)->items($items);
        }

        $groups[] = NavigationGroup::make('Tools')->items([
            NavigationItem::make('OCR intake')->icon('search')->url(url('/admin/ocr'))
                ->visible(fn () => module_enabled('orders') && (auth()->user()?->can('orders.manage') ?? false)),
            NavigationItem::make('AI style desk')->icon('sparkles')->url(url('/admin/ai/styles'))
                ->visible(fn () => module_enabled('catalog') && (auth()->user()?->can('catalog.manage') ?? false)),
            NavigationItem::make('Reports')->icon('folder')->url(url('/admin/reports'))
                ->visible(fn () => module_enabled('reports') && (auth()->user()?->can('reports.view') ?? false)),
            NavigationItem::make('Settings')->icon('settings')->url(url('/admin/settings'))
                ->visible(fn () => auth()->user()?->can('settings.view') ?? false),
        ]);

        return $groups;
    }

    /**
     * @param  array<string, mixed>  $field
     * @return array{text: string, tone: ?string}
     */
    public static function present(\Illuminate\Database\Eloquent\Model $record, array $field): array
    {
        $name = $field['name'];
        $raw = $record->getAttribute($name);

        if (($field['type'] ?? '') === 'select' && filled($field['model'] ?? null)) {
            $relation = str($name)->replaceEnd('_id', '')->camel()->toString();
            $related = method_exists($record, $relation) ? $record->{$relation} : null;
            $text = $related ? (string) data_get($related, $field['option_label'] ?? 'name', '—') : '—';

            return ['text' => $text !== '' ? $text : '—', 'tone' => null];
        }

        if (($field['type'] ?? '') === 'select' && is_array($field['options'] ?? null)) {
            $key = is_scalar($raw) ? (string) $raw : '';
            $text = $field['options'][$key] ?? ($key !== '' ? str($key)->headline()->toString() : '—');

            return ['text' => $text, 'tone' => self::tone($key)];
        }

        if (($field['type'] ?? '') === 'checkbox') {
            return ['text' => $raw ? 'Yes' : 'No', 'tone' => $raw ? 'ok' : null];
        }

        if (($field['type'] ?? '') === 'date' && filled($raw)) {
            return ['text' => \Illuminate\Support\Carbon::parse($raw)->timezone('Asia/Dhaka')->format('d M Y'), 'tone' => null];
        }

        if (is_array($raw)) {
            return ['text' => $raw === [] ? '—' : json_encode($raw, JSON_UNESCAPED_UNICODE), 'tone' => null];
        }

        $text = filled($raw) ? (string) $raw : '—';

        return ['text' => $text, 'tone' => self::tone(is_scalar($raw) ? (string) $raw : null)];
    }

    private static function tone(?string $value): ?string
    {
        return match ($value) {
            'delayed', 'fail', 'rejected', 'cancelled', 'breakdown', 'left' => 'bad',
            'rush', 'hold', 'partial', 'probation', 'pending' => 'warn',
            'completed', 'pass', 'paid', 'received', 'active', 'running', 'approved', 'confirmed', 'sewing', 'cutting', 'finishing', 'packing' => 'ok',
            default => null,
        };
    }

    /** @param  list<string>  $columns @param  list<array<string, mixed>>  $fields @param  list<string>  $with */
    private static function def(string $model, string $module, string $group, string $label, string $icon, string $title, array $columns, array $fields, array $with = []): array
    {
        return [
            'model' => $model,
            'module' => $module,
            'group' => $group,
            'label' => $label,
            'icon' => $icon,
            'title' => $title,
            'columns' => $columns,
            'fields' => $fields,
            'with' => $with,
        ];
    }

    /** @return array<string, mixed> */
    private static function text(string $name, string $label, bool $required = false): array
    {
        return ['name' => $name, 'label' => $label, 'type' => 'text', 'required' => $required];
    }

    /** @return array<string, mixed> */
    private static function area(string $name, string $label, bool $required = false): array
    {
        return ['name' => $name, 'label' => $label, 'type' => 'textarea', 'required' => $required];
    }

    /** @return array<string, mixed> */
    private static function number(string $name, string $label, bool $required = false): array
    {
        return ['name' => $name, 'label' => $label, 'type' => 'number', 'required' => $required];
    }

    /** @return array<string, mixed> */
    private static function date(string $name, string $label, bool $required = false): array
    {
        return ['name' => $name, 'label' => $label, 'type' => 'date', 'required' => $required];
    }

    /** @return array<string, mixed> */
    private static function check(string $name, string $label): array
    {
        return ['name' => $name, 'label' => $label, 'type' => 'checkbox'];
    }

    /**
     * @param  class-string|null  $model
     * @param  array<string, string>|null  $options
     * @return array<string, mixed>
     */
    private static function select(string $name, string $label, ?string $model, ?array $options = null, string|bool $optionLabel = 'name', bool $required = false): array
    {
        if (is_bool($optionLabel)) {
            $required = $optionLabel;
            $optionLabel = 'name';
        }

        return [
            'name' => $name,
            'label' => $label,
            'type' => 'select',
            'model' => $model,
            'options' => $options,
            'option_label' => $optionLabel,
            'required' => $required,
        ];
    }
}
