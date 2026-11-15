<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\BackupDatabaseJob;
use App\Modules\KidsWear\Catalog\Models\BomItem;
use App\Modules\KidsWear\Catalog\Models\Colorway;
use App\Modules\KidsWear\Catalog\Models\SizeChart;
use App\Modules\KidsWear\Catalog\Models\SizeChartRow;
use App\Modules\KidsWear\Catalog\Models\Style;
use App\Modules\KidsWear\Catalog\Models\TechPack;
use App\Modules\KidsWear\Catalog\Services\StyleDraftService;
use App\Modules\KidsWear\Finance\Services\CostingService;
use App\Modules\KidsWear\Finance\Services\DocumentService;
use App\Modules\KidsWear\Inventory\Models\Location;
use App\Modules\KidsWear\Inventory\Models\Material;
use App\Modules\KidsWear\Inventory\Services\StockService;
use App\Modules\KidsWear\Orders\Models\BuyerOrder;
use App\Modules\KidsWear\Orders\Services\OrderConversionService;
use App\Modules\KidsWear\Procurement\Models\PurchaseOrder;
use App\Modules\KidsWear\Procurement\Services\OcrIntakeService;
use App\Modules\KidsWear\Procurement\Services\ReceiptService;
use App\Modules\KidsWear\Production\Models\ProductionEntry;
use App\Modules\KidsWear\Production\Models\ProductionOrder;
use App\Modules\KidsWear\Production\Services\BundleService;
use App\Modules\KidsWear\Production\Services\MaterialRequirementService;
use App\Modules\KidsWear\Production\Services\ProductionService;
use App\Modules\KidsWear\Quality\Models\DefectType;
use App\Modules\KidsWear\Quality\Models\Inspection;
use App\Modules\KidsWear\Reports\ReportService;
use App\Modules\KidsWear\Settings\Models\Setting;
use App\Modules\KidsWear\Support\AiAssistant;
use App\Modules\KidsWear\Support\Company;
use App\Modules\KidsWear\Support\CuttingPlan;
use App\Modules\KidsWear\Support\ModuleManager;
use App\Modules\KidsWear\Support\SizeMatrix;
use App\Modules\KidsWear\Support\Status;
use App\Modules\KidsWear\Support\XlsxExporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FactoryController extends Controller
{
    public function board(): View
    {
        $this->allows('production', 'view');
        $orders = ProductionOrder::query()->with(['buyerOrder.buyer', 'line', 'lines'])->whereNotIn('status', [Status::COMPLETED])->latest()->limit(30)->get();

        return view('admin.production.board', [
            'orders' => $orders,
            'stages' => Status::productionStages(),
            'todayOut' => (int) ProductionEntry::query()->whereDate('entry_date', now()->toDateString())->sum('quantity_out'),
        ]);
    }

    public function confirmOrder(BuyerOrder $buyerOrder, OrderConversionService $conversion): RedirectResponse
    {
        $this->allows('orders', 'manage');
        $buyerOrder->update(['status' => Status::CONFIRMED, 'confirmed_at' => now()]);
        $production = $conversion->convert($buyerOrder->fresh());

        return redirect()->route('admin.resources.show', ['production-orders', $production->id])
            ->with('status', 'Buyer order converted to '.$production->number.' with material requirements and bundles.');
    }

    public function addOrderLine(Request $request, BuyerOrder $buyerOrder): RedirectResponse
    {
        $this->allows('orders', 'manage');
        $data = $request->validate([
            'style_id' => ['required', 'exists:styles,id'],
            'colorway_id' => ['nullable', 'exists:colorways,id'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'sizes' => ['required', 'array'],
        ]);
        $matrix = SizeMatrix::normalize($data['sizes']);
        abort_if($matrix === [], 422, 'Enter at least one size quantity.');
        $buyerOrder->lines()->create([
            'style_id' => $data['style_id'],
            'colorway_id' => $data['colorway_id'] ?? null,
            'size_matrix' => $matrix,
            'quantity' => SizeMatrix::total($matrix),
            'unit_price' => $data['unit_price'],
        ]);

        return back()->with('status', 'Size run added to the order.');
    }

    public function addBom(Request $request, Style $style): RedirectResponse
    {
        $this->allows('catalog', 'manage');
        $data = $request->validate([
            'material_id' => ['nullable', 'exists:materials,id'],
            'component' => ['required', 'string'],
            'name' => ['required', 'string'],
            'unit' => ['required', 'string'],
            'consumption' => ['required', 'numeric', 'min:0'],
            'wastage_percent' => ['nullable', 'numeric', 'min:0'],
        ]);
        $style->bomItems()->create($data);

        return back()->with('status', 'BOM line saved.');
    }

    public function addColorway(Request $request, Style $style): RedirectResponse
    {
        $this->allows('catalog', 'manage');
        $style->colorways()->create($request->validate([
            'name' => ['required', 'string'],
            'code' => ['required', 'string'],
            'hex' => ['nullable', 'string', 'max:7'],
        ]));

        return back()->with('status', 'Colorway added.');
    }

    public function addMeasurement(Request $request, Style $style): RedirectResponse
    {
        $this->allows('catalog', 'manage');
        $data = $request->validate([
            'size_code' => ['required', 'string'],
            'chest' => ['nullable', 'numeric'],
            'length' => ['nullable', 'numeric'],
            'waist' => ['nullable', 'numeric'],
        ]);
        $chart = $style->sizeCharts()->first() ?: SizeChart::query()->create(['style_id' => $style->id, 'name' => 'Body measurements']);
        SizeChartRow::query()->updateOrCreate(
            ['size_chart_id' => $chart->id, 'size_code' => $data['size_code']],
            ['measurements' => ['chest' => $data['chest'] ?? null, 'length' => $data['length'] ?? null, 'waist' => $data['waist'] ?? null]],
        );

        return back()->with('status', 'Measurement saved for '.$data['size_code'].'.');
    }

    public function addTechPack(Request $request, Style $style): RedirectResponse
    {
        $this->allows('catalog', 'manage');
        TechPack::query()->create([
            'style_id' => $style->id,
            ...$request->validate([
                'version' => ['required', 'string'],
                'construction' => ['nullable', 'string'],
                'stitching' => ['nullable', 'string'],
                'print_placement' => ['nullable', 'string'],
            ]),
        ]);

        return back()->with('status', 'Tech pack version stored.');
    }

    public function costStyle(Request $request, Style $style, CostingService $costing): RedirectResponse
    {
        $this->allows('finance', 'manage');
        $sheet = $costing->save($style, $request->validate([
            'fabric_cost' => ['nullable', 'numeric'],
            'trim_cost' => ['nullable', 'numeric'],
            'cm_cost' => ['nullable', 'numeric'],
            'washing_cost' => ['nullable', 'numeric'],
            'print_cost' => ['nullable', 'numeric'],
            'packing_cost' => ['nullable', 'numeric'],
            'overhead' => ['nullable', 'numeric'],
            'margin_percent' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]));

        return back()->with('status', 'FOB calculated at '.$sheet->fob_price.' '.$sheet->currency.'.');
    }

    public function issueMaterials(ProductionOrder $productionOrder, StockService $stock): RedirectResponse
    {
        $this->allows('inventory', 'manage');
        $stock->issueToOrder($productionOrder);

        return back()->with('status', 'Material issued against the BOM requirement.');
    }

    public function recalculate(ProductionOrder $productionOrder, MaterialRequirementService $materials, BundleService $bundles): RedirectResponse
    {
        $this->allows('production', 'manage');
        $materials->calculate($productionOrder);
        $bundles->generate($productionOrder);

        return back()->with('status', 'Requirements recalculated. Existing bundles were kept.');
    }

    public function recordOutput(Request $request, ProductionOrder $productionOrder, ProductionService $production): RedirectResponse
    {
        $this->allows('production', 'manage');
        $production->record($productionOrder, $request->validate([
            'bundle_id' => ['nullable', 'exists:bundles,id'],
            'line_id' => ['nullable', 'exists:production_lines,id'],
            'stage' => ['required', 'in:cutting,sewing,finishing,packing'],
            'entry_date' => ['required', 'date'],
            'quantity_out' => ['required', 'integer', 'min:0'],
            'quantity_reject' => ['nullable', 'integer', 'min:0'],
            'operator_employee_id' => ['nullable', 'exists:employees,id'],
            'remarks' => ['nullable', 'string'],
        ]));

        return back()->with('status', 'Stage output recorded.');
    }

    public function receivePurchase(Request $request, PurchaseOrder $purchaseOrder, ReceiptService $receipts): RedirectResponse
    {
        $this->allows('procurement', 'manage');
        $location = Location::query()->findOrFail($request->validate([
            'location_id' => ['required', 'exists:locations,id'],
        ])['location_id']);
        $receipt = $receipts->receiveAll($purchaseOrder, $location);

        return back()->with('status', $receipt->number.' posted into '.$location->name.'.');
    }

    public function addPurchaseItem(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->allows('procurement', 'manage');
        $data = $request->validate([
            'material_id' => ['required', 'exists:materials,id'],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);
        $material = Material::query()->findOrFail($data['material_id']);
        $purchaseOrder->items()->create([
            'material_id' => $material->id,
            'description' => $material->name,
            'quantity' => $data['quantity'],
            'unit' => $material->unit,
            'unit_price' => $data['unit_price'],
        ]);

        return back()->with('status', 'Purchase line added.');
    }

    public function inspectionDefects(Request $request, Inspection $inspection, AiAssistant $ai): RedirectResponse
    {
        $this->allows('quality', 'manage');
        $data = $request->validate([
            'defects' => ['array'],
            'defects.*' => ['integer', 'min:0'],
        ]);
        $inspection->defects()->delete();
        $lines = [];
        foreach ($data['defects'] ?? [] as $typeId => $qty) {
            if ((int) $qty < 1) {
                continue;
            }
            $type = DefectType::query()->find($typeId);
            if (! $type) {
                continue;
            }
            $inspection->defects()->create(['defect_type_id' => $type->id, 'quantity' => (int) $qty]);
            $lines[] = $type->name.' x'.$qty;
        }
        $fallback = $lines === []
            ? 'No defects were marked. Keep the inline check at the same operation tomorrow.'
            : 'Focus the next inline check on: '.implode(', ', $lines).'. Stop the bundle if a safety defect repeats.';
        $inspection->update([
            'ai_notes' => $ai->suggest('quality', 'Kids wear inspection defects: '.implode(', ', $lines ?: ['none']).'. Give one corrective action for the sewing line.', $fallback),
        ]);

        return back()->with('status', 'Defect checklist saved.');
    }

    public function ocrForm(): View
    {
        $this->allows('orders', 'manage');

        return view('admin.ocr.index', [
            'intakes' => \App\Modules\KidsWear\Procurement\Models\OcrIntake::query()->latest()->limit(10)->get(),
        ]);
    }

    public function ocrStore(Request $request, OcrIntakeService $ocr): RedirectResponse
    {
        $this->allows('orders', 'manage');
        $request->validate(['document' => ['required', 'file', 'max:10240']]);
        $intake = $ocr->intake($request->file('document'));
        $message = $intake->buyer_order_id
            ? 'Draft buyer order #'.$intake->buyer_order_id.' was created from the document.'
            : 'Document stored. Add a matching style code and size quantities, then upload again to draft an order.';

        return back()->with('status', $message);
    }

    public function aiStyles(StyleDraftService $drafts): View
    {
        $this->allows('catalog', 'manage');

        return view('admin.ai.styles', [
            'styles' => Style::query()->latest()->limit(8)->get(),
        ]);
    }

    public function aiStylesStore(Request $request, StyleDraftService $drafts, AiAssistant $ai): RedirectResponse
    {
        $this->allows('catalog', 'manage');
        $data = $request->validate([
            'keyword' => ['required', 'string', 'max:180'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);
        $style = $drafts->fromKeyword($data['keyword'], $data['category_id'] ?? null);
        $plan = CuttingPlan::suggest(['2Y' => 200, '3Y' => 200, '4Y' => 100, '5Y' => 100]);
        $style->update([
            'specs' => array_merge($style->specs ?? [], [
                'cutting_hint' => $ai->suggest(
                    'cutting',
                    'Suggest a cutting marker note for kids sizes with ratio '.json_encode($plan['ratio']).' and '.$plan['lays'].' lays.',
                    'Marker ratio '.SizeMatrix::label($plan['ratio']).' across '.$plan['lays'].' lays.',
                ),
            ]),
        ]);

        return redirect()->route('admin.resources.show', ['styles', $style->id])->with('status', 'Style draft '.$style->style_code.' is ready.');
    }

    public function reports(ReportService $reports): View
    {
        $this->allows('reports', 'view');

        return view('admin.reports.index', ['names' => $reports->names()]);
    }

    public function reportShow(string $report, ReportService $reports): View
    {
        $this->allows('reports', 'view');

        return view('admin.reports.show', [
            'key' => $report,
            'title' => $reports->names()[$report] ?? $report,
            'table' => $reports->build($report),
        ]);
    }

    public function reportXlsx(string $report, ReportService $reports): StreamedResponse
    {
        $this->allows('reports', 'view');
        $table = $reports->build($report);

        return XlsxExporter::download($report.'.xlsx', $table['headers'], $table['rows']);
    }

    public function reportPdf(string $report, ReportService $reports): Response
    {
        $this->allows('reports', 'view');
        $table = $reports->build($report);

        return \ImranDev\UnicodePdf\Facades\UnicodePdf::loadView('pdf.report', [
            'title' => $reports->names()[$report] ?? $report,
            'table' => $table,
            'company' => Company::profile(),
        ])->preset('bengali')->name($report.'.pdf');
    }

    public function invoicePdf(int $invoice, DocumentService $documents): Response
    {
        $this->allows('finance', 'view');

        return $documents->pdf(\App\Modules\KidsWear\Finance\Models\Invoice::query()->findOrFail($invoice));
    }

    public function makeInvoice(BuyerOrder $buyerOrder, DocumentService $documents): RedirectResponse
    {
        $this->allows('finance', 'manage');
        $invoice = $documents->commercialInvoice($buyerOrder);

        return redirect()->route('admin.resources.show', ['invoices', $invoice->id])->with('status', $invoice->number.' issued.');
    }

    public function makePackingList(ProductionOrder $productionOrder, DocumentService $documents): RedirectResponse
    {
        $this->allows('finance', 'manage');
        $invoice = $documents->packingList($productionOrder);

        return redirect()->route('admin.resources.show', ['invoices', $invoice->id])->with('status', 'Packing list '.$invoice->number.' generated.');
    }

    public function bundleTicket(string $code): Response
    {
        $this->allows('production', 'view');
        $bundle = \App\Modules\KidsWear\Production\Models\Bundle::query()->where('code', $code)->firstOrFail();

        return \ImranDev\UnicodePdf\Facades\UnicodePdf::loadView('pdf.bundle', [
            'bundle' => $bundle->load('style', 'colorway', 'productionOrder'),
            'company' => Company::profile(),
        ])->preset('bengali')->name($bundle->code.'.pdf');
    }

    public function settings(): View
    {
        abort_unless(auth()->user()?->can('settings.view'), 403);
        $suggestions = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('drutogoti_suggestions')) {
            $suggestions = \Drutogoti\Models\Suggestion::query()->latest('last_seen_at')->limit(8)->get();
        }

        return view('admin.settings.index', [
            'company' => Company::profile(),
            'modules' => config('kidswear.modules'),
            'flags' => ModuleManager::flags(),
            'backup' => Setting::getValue('last_backup'),
            'suggestions' => $suggestions,
        ]);
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->can('settings.manage'), 403);
        Company::save($request->validate([
            'name' => ['required', 'string'],
            'name_bn' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'address_bn' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'tagline' => ['nullable', 'string'],
            'tagline_bn' => ['nullable', 'string'],
        ]));
        $flags = [];
        foreach (array_keys(config('kidswear.modules')) as $module) {
            $flags[$module] = $request->boolean('modules.'.$module);
        }
        $flags['settings'] = true;
        ModuleManager::save($flags);

        return back()->with('status', 'Factory settings saved.');
    }

    public function backup(): RedirectResponse
    {
        abort_unless(auth()->user()?->can('settings.manage'), 403);
        BackupDatabaseJob::dispatch();

        return back()->with('status', 'Backup job queued. With the database queue, run php artisan queue:work once.');
    }

    private function allows(string $module, string $ability): void
    {
        abort_unless(module_enabled($module) && auth()->user()?->can($module.'.'.$ability), 403);
    }
}
