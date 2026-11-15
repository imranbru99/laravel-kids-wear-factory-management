<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('locale', 5)->default('en')->after('phone');
            $table->boolean('is_active')->default(true)->after('locale');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general')->index();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('start_time', 5);
            $table->string('end_time', 5);
            $table->unsignedSmallInteger('grace_minutes')->default(10);
            $table->unsignedSmallInteger('break_minutes')->default(60);
            $table->unsignedSmallInteger('half_day_minutes')->default(240);
            $table->unsignedSmallInteger('full_day_minutes')->default(480);
            $table->boolean('overtime_allowed')->default(true);
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('gender', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('joined_on')->nullable();
            $table->string('employment_type')->default('permanent');
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->string('status')->default('active')->index();
            $table->string('emergency_contact')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedSmallInteger('days_per_year')->default(0);
            $table->boolean('paid')->default(true);
            $table->timestamps();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days', 5, 1);
            $table->text('reason')->nullable();
            $table->string('status')->default('pending')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->date('observed_on')->unique();
            $table->boolean('paid')->default(true);
            $table->timestamps();
        });

        Schema::create('attendance_daily', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('work_date');
            $table->string('status')->index();
            $table->unsignedInteger('late_minutes')->default(0);
            $table->unsignedInteger('early_leave_minutes')->default(0);
            $table->unsignedInteger('worked_minutes')->default(0);
            $table->unsignedInteger('overtime_minutes')->default(0);
            $table->string('shift_name')->nullable();
            $table->string('source')->default('calculator');
            $table->timestamps();
            $table->unique(['employee_id', 'work_date']);
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('age_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('label');
            $table->string('label_bn')->nullable();
            $table->unsignedSmallInteger('min_months')->default(0);
            $table->unsignedSmallInteger('max_months')->default(0);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20);
            $table->unsignedSmallInteger('year');
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->timestamps();
        });

        Schema::create('styles', function (Blueprint $table) {
            $table->id();
            $table->string('style_code')->unique();
            $table->string('sku')->nullable()->index();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('slug')->unique();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subcategory_id')->nullable();
            $table->foreignId('season_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('age_group_id')->nullable()->constrained()->nullOnDelete();
            $table->string('gender', 20)->default('unisex');
            $table->string('fabric')->nullable();
            $table->unsignedSmallInteger('gsm')->nullable();
            $table->string('excerpt')->nullable();
            $table->text('description')->nullable();
            $table->text('description_bn')->nullable();
            $table->text('care_instructions')->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('image_alt')->nullable();
            $table->string('image_path')->nullable();
            $table->string('locale', 5)->default('en');
            $table->string('gtin')->nullable();
            $table->string('mpn')->nullable();
            $table->json('specs')->nullable();
            $table->json('tags')->nullable();
            $table->json('faq')->nullable();
            $table->json('size_guide')->nullable();
            $table->json('json_ld')->nullable();
            $table->json('translations')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('colorways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 30);
            $table->string('hex', 7)->nullable();
            $table->string('pantone')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('size_charts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->string('name')->default('Body measurements');
            $table->string('unit', 5)->default('cm');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('size_chart_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('size_chart_id')->constrained()->cascadeOnDelete();
            $table->string('size_code', 20);
            $table->json('measurements');
            $table->timestamps();
        });

        Schema::create('tech_packs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->string('version', 20)->default('A');
            $table->text('construction')->nullable();
            $table->text('stitching')->nullable();
            $table->text('print_placement')->nullable();
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('type')->default('store');
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('category')->default('fabric');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('country')->default('Bangladesh');
            $table->unsignedSmallInteger('lead_time_days')->default(7);
            $table->unsignedTinyInteger('rating')->default(3);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('type')->index();
            $table->string('unit', 20)->default('kg');
            $table->string('color')->nullable();
            $table->unsignedSmallInteger('gsm')->nullable();
            $table->decimal('width_cm', 8, 2)->nullable();
            $table->decimal('reorder_level', 12, 3)->default(0);
            $table->decimal('last_cost', 12, 4)->default(0);
            $table->foreignId('preferred_supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();
            $table->string('component');
            $table->string('name');
            $table->string('unit', 20);
            $table->decimal('consumption', 12, 4);
            $table->decimal('wastage_percent', 5, 2)->default(0);
            $table->string('color')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->string('lot_no')->default('OPEN');
            $table->decimal('on_hand', 14, 3)->default(0);
            $table->decimal('reserved', 14, 3)->default(0);
            $table->timestamps();
            $table->unique(['material_id', 'location_id', 'lot_no']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->decimal('quantity', 14, 3);
            $table->nullableMorphs('reference');
            $table->string('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('finished_goods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->foreignId('colorway_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->string('size_code', 20);
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();
            $table->unique(['style_id', 'colorway_id', 'location_id', 'size_code']);
        });

        Schema::create('buyers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('country');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('payment_terms')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('buyer_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('buyer_id')->constrained()->cascadeOnDelete();
            $table->date('order_date');
            $table->date('delivery_date');
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('draft')->index();
            $table->string('incoterm', 10)->nullable();
            $table->string('destination')->nullable();
            $table->text('remarks')->nullable();
            $table->string('source')->default('manual');
            $table->unsignedBigInteger('ocr_intake_id')->nullable()->index();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('buyer_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->foreignId('colorway_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description')->nullable();
            $table->json('size_matrix');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('production_lines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('section');
            $table->unsignedInteger('target_per_hour')->default(80);
            $table->foreignId('supervisor_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('buyer_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('line_id')->nullable()->constrained('production_lines')->nullOnDelete();
            $table->string('status')->default('planned')->index();
            $table->date('planned_start')->nullable();
            $table->date('planned_end')->nullable();
            $table->date('actual_start')->nullable();
            $table->date('actual_end')->nullable();
            $table->string('priority')->default('normal');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('production_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->foreignId('colorway_id')->nullable()->constrained()->nullOnDelete();
            $table->json('size_matrix');
            $table->unsignedInteger('quantity');
            $table->timestamps();
        });

        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->foreignId('colorway_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('size_code', 20);
            $table->unsignedInteger('quantity');
            $table->string('current_stage')->default('cutting');
            $table->string('status')->default('open')->index();
            $table->timestamps();
        });

        Schema::create('production_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bundle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('line_id')->nullable()->constrained('production_lines')->nullOnDelete();
            $table->string('stage');
            $table->date('entry_date');
            $table->unsignedInteger('quantity_in')->default(0);
            $table->unsignedInteger('quantity_out')->default(0);
            $table->unsignedInteger('quantity_reject')->default(0);
            $table->foreignId('operator_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('daily_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('line_id')->nullable()->constrained('production_lines')->nullOnDelete();
            $table->date('target_date');
            $table->string('stage');
            $table->unsignedInteger('target_qty');
            $table->unsignedInteger('achieved_qty')->default(0);
            $table->timestamps();
        });

        Schema::create('material_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('unit', 20);
            $table->decimal('required_qty', 14, 3);
            $table->decimal('available_qty', 14, 3)->default(0);
            $table->decimal('shortage_qty', 14, 3)->default(0);
            $table->decimal('issued_qty', 14, 3)->default(0);
            $table->timestamps();
        });

        Schema::create('defect_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('severity')->default('major');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bundle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->timestamp('inspected_at');
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('sample_size')->default(0);
            $table->unsignedInteger('accepted_qty')->default(0);
            $table->unsignedInteger('rejected_qty')->default(0);
            $table->string('aql_level')->default('2.5');
            $table->string('result')->index();
            $table->text('notes')->nullable();
            $table->text('ai_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inspection_defects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('defect_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('zone')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('currency', 3)->default('BDT');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->decimal('quantity', 14, 3);
            $table->string('unit', 20);
            $table->decimal('unit_price', 12, 4)->default(0);
            $table->decimal('received_qty', 14, 3)->default(0);
            $table->timestamps();
        });

        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->timestamp('received_at');
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 14, 3);
            $table->string('lot_no')->default('OPEN');
            $table->timestamps();
        });

        Schema::create('ocr_intakes', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('path')->nullable();
            $table->string('document_type')->default('buyer_order');
            $table->longText('extracted_text')->nullable();
            $table->json('parsed')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('buyer_order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('style_costings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->constrained()->cascadeOnDelete();
            $table->decimal('fabric_cost', 12, 4)->default(0);
            $table->decimal('trim_cost', 12, 4)->default(0);
            $table->decimal('cm_cost', 12, 4)->default(0);
            $table->decimal('washing_cost', 12, 4)->default(0);
            $table->decimal('print_cost', 12, 4)->default(0);
            $table->decimal('packing_cost', 12, 4)->default(0);
            $table->decimal('overhead', 12, 4)->default(0);
            $table->decimal('margin_percent', 5, 2)->default(10);
            $table->decimal('fob_price', 12, 4)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('buyer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('commercial');
            $table->date('invoice_date');
            $table->string('currency', 3)->default('USD');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('freight', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('style_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->json('size_matrix')->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 4)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type');
            $table->string('brand')->nullable();
            $table->foreignId('line_id')->nullable()->constrained('production_lines')->nullOnDelete();
            $table->date('installed_on')->nullable();
            $table->string('status')->default('running')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->string('task');
            $table->unsignedSmallInteger('frequency_days')->default(30);
            $table->date('last_done_on')->nullable();
            $table->date('next_due_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('breakdown_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->string('reason');
            $table->unsignedInteger('downtime_minutes')->default(0);
            $table->text('action_taken')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->string('slug')->unique();
            $table->string('excerpt')->nullable();
            $table->text('body');
            $table->text('body_bn')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('caption')->nullable();
            $table->string('image_path');
            $table->string('category')->default('floor');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('contact');
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->text('message');
            $table->foreignId('style_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('new')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 30);
            $table->string('issuer');
            $table->date('valid_until')->nullable();
            $table->text('summary')->nullable();
            $table->text('summary_bn')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $tables = [
            'certifications', 'inquiries', 'gallery_items', 'posts', 'breakdown_logs', 'maintenance_schedules', 'machines',
            'invoice_items', 'invoices', 'style_costings', 'ocr_intakes', 'goods_receipt_items', 'goods_receipts',
            'purchase_order_items', 'purchase_orders', 'inspection_defects', 'inspections', 'defect_types',
            'material_requirements', 'daily_targets', 'production_entries', 'bundles', 'production_order_lines',
            'production_orders', 'production_lines', 'buyer_order_lines', 'buyer_orders', 'buyers', 'finished_goods',
            'stock_movements', 'stocks', 'bom_items', 'materials', 'suppliers', 'locations', 'tech_packs',
            'size_chart_rows', 'size_charts', 'colorways', 'styles', 'seasons', 'age_groups', 'categories',
            'attendance_daily', 'holidays', 'leave_requests', 'leave_types', 'employees', 'shifts', 'designations',
            'departments', 'settings', 'notifications',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'locale', 'is_active']);
        });
    }
};
