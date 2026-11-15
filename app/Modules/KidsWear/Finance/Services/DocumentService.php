<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Finance\Services;

use App\Modules\KidsWear\Finance\Models\Invoice;
use App\Modules\KidsWear\Orders\Models\BuyerOrder;
use App\Modules\KidsWear\Production\Models\ProductionOrder;
use App\Modules\KidsWear\Support\Company;
use App\Modules\KidsWear\Support\SizeMatrix;
use App\Modules\KidsWear\Support\Status;
use ImranDev\UnicodePdf\Facades\UnicodePdf;
use Symfony\Component\HttpFoundation\Response;

class DocumentService
{
    public function commercialInvoice(BuyerOrder $order): Invoice
    {
        $order->loadMissing('buyer', 'lines.style', 'lines.colorway');
        $invoice = Invoice::query()->create([
            'buyer_id' => $order->buyer_id,
            'buyer_order_id' => $order->id,
            'type' => 'commercial',
            'invoice_date' => now()->toDateString(),
            'currency' => $order->currency,
            'status' => Status::ISSUED,
        ]);

        $subtotal = 0;
        foreach ($order->lines as $line) {
            $amount = round($line->quantity * (float) $line->unit_price, 2);
            $subtotal += $amount;
            $invoice->items()->create([
                'style_id' => $line->style_id,
                'description' => $line->style->style_code.' '.$line->style->name.($line->colorway ? ' / '.$line->colorway->name : ''),
                'size_matrix' => $line->size_matrix,
                'quantity' => $line->quantity,
                'unit_price' => $line->unit_price,
                'amount' => $amount,
            ]);
        }

        $invoice->update(['subtotal' => $subtotal, 'total' => $subtotal]);

        return $invoice->load('items', 'buyer');
    }

    public function packingList(ProductionOrder $order): Invoice
    {
        $order->loadMissing('buyerOrder.buyer', 'lines.style', 'lines.colorway', 'bundles');
        $buyer = $order->buyerOrder?->buyer;
        abort_unless($buyer, 422, 'This production order is not linked to a buyer.');

        $invoice = Invoice::query()->create([
            'buyer_id' => $buyer->id,
            'buyer_order_id' => $order->buyer_order_id,
            'type' => 'packing_list',
            'invoice_date' => now()->toDateString(),
            'currency' => $order->buyerOrder->currency,
            'status' => Status::ISSUED,
            'notes' => 'Cartons follow bundle size '.config('kidswear.factory.bundle_size', 20).'.',
        ]);

        foreach ($order->lines as $line) {
            $invoice->items()->create([
                'style_id' => $line->style_id,
                'description' => $line->style->style_code.' '.$line->style->name,
                'size_matrix' => $line->size_matrix,
                'quantity' => $line->quantity,
                'unit_price' => 0,
                'amount' => 0,
            ]);
        }

        return $invoice->load('items', 'buyer');
    }

    public function pdf(Invoice $invoice): Response
    {
        $invoice->loadMissing('buyer', 'items.style', 'buyerOrder');
        $view = $invoice->type === 'packing_list' ? 'pdf.packing-list' : 'pdf.invoice';

        return UnicodePdf::loadView($view, [
            'invoice' => $invoice,
            'company' => Company::profile(),
            'matrixLabel' => fn (array $matrix) => SizeMatrix::label($matrix),
        ])->preset('bengali')->name($invoice->number.'.pdf');
    }
}
