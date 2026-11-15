<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Procurement\Services;

use App\Modules\KidsWear\Catalog\Models\Style;
use App\Modules\KidsWear\Orders\Models\Buyer;
use App\Modules\KidsWear\Orders\Models\BuyerOrder;
use App\Modules\KidsWear\Procurement\Models\OcrIntake;
use App\Modules\KidsWear\Support\SizeMatrix;
use Illuminate\Http\UploadedFile;
use ImranDevBd\AiOcr\Facades\AiOcr;
use Throwable;

class OcrIntakeService
{
    public function intake(UploadedFile|string $file, ?string $originalName = null): OcrIntake
    {
        $path = $file instanceof UploadedFile
            ? $file->store('ocr', 'local')
            : $file;
        $absolute = $file instanceof UploadedFile
            ? storage_path('app/private/'.$path)
            : $file;
        if ($file instanceof UploadedFile && ! is_file($absolute)) {
            $absolute = storage_path('app/'.$path);
        }

        $text = '';
        $warning = null;
        try {
            $text = AiOcr::file($absolute)->manualOnly()->safe()->text();
        } catch (Throwable $exception) {
            $warning = $exception->getMessage();
            if (is_file($absolute)) {
                $text = (string) @file_get_contents($absolute);
            }
        }

        $parsed = $this->parse($text);
        if ($warning) {
            $parsed['warning'] = $warning;
        }

        $intake = OcrIntake::query()->create([
            'original_name' => $originalName ?: ($file instanceof UploadedFile ? $file->getClientOriginalName() : basename($absolute)),
            'path' => $path,
            'document_type' => 'buyer_order',
            'extracted_text' => $text,
            'parsed' => $parsed,
            'status' => 'reviewed',
        ]);

        $order = $this->draftOrder($intake, $parsed);
        if ($order) {
            $intake->update(['buyer_order_id' => $order->id, 'status' => 'draft_order']);
        }

        return $intake->fresh('buyerOrder');
    }

    /** @return array<string, mixed> */
    public function parse(string $text): array
    {
        $fields = [];
        foreach (preg_split("/\r\n|\n|\r/", $text) ?: [] as $line) {
            if (preg_match('/^\s*([A-Za-z][A-Za-z ]{1,30})\s*[:：]\s*(.+)$/', trim($line), $match)) {
                $fields[strtolower(trim($match[1]))] = trim($match[2]);
            }
        }

        $matrix = [];
        foreach (SizeMatrix::sizes() as $size) {
            $quoted = preg_quote($size, '/');
            if (preg_match('/\b'.$quoted.'\b\s*[:=x]?\s*(\d{1,6})/i', $text, $match)) {
                $matrix[$size] = (int) $match[1];
            }
        }

        $style = null;
        if (! empty($fields['style'])) {
            $style = Style::query()->where('style_code', $fields['style'])->orWhere('name', $fields['style'])->first();
        }

        return [
            'buyer' => $fields['buyer'] ?? null,
            'style' => $fields['style'] ?? $style?->style_code,
            'style_id' => $style?->id,
            'delivery' => $fields['delivery'] ?? $fields['delivery date'] ?? null,
            'price' => isset($fields['price']) ? (float) preg_replace('/[^0-9.]/', '', $fields['price']) : null,
            'size_matrix' => SizeMatrix::normalize($matrix),
            'quantity' => SizeMatrix::total($matrix),
        ];
    }

    /** @param  array<string, mixed>  $parsed */
    private function draftOrder(OcrIntake $intake, array $parsed): ?BuyerOrder
    {
        if (empty($parsed['style_id']) || empty($parsed['size_matrix'])) {
            return null;
        }

        $buyer = null;
        if (! empty($parsed['buyer'])) {
            $buyer = Buyer::query()->where('name', $parsed['buyer'])->orWhere('code', $parsed['buyer'])->first();
        }
        $buyer ??= Buyer::query()->first();
        if (! $buyer) {
            return null;
        }

        $order = BuyerOrder::query()->create([
            'buyer_id' => $buyer->id,
            'order_date' => now()->toDateString(),
            'delivery_date' => $parsed['delivery'] ?: now()->addDays(45)->toDateString(),
            'status' => 'draft',
            'source' => 'ocr',
            'ocr_intake_id' => $intake->id,
            'remarks' => 'Drafted from OCR intake '.$intake->original_name,
            'currency' => $buyer->currency ?: 'USD',
        ]);

        $order->lines()->create([
            'style_id' => $parsed['style_id'],
            'size_matrix' => $parsed['size_matrix'],
            'quantity' => $parsed['quantity'],
            'unit_price' => $parsed['price'] ?? 0,
            'description' => 'OCR line',
        ]);

        return $order;
    }
}
