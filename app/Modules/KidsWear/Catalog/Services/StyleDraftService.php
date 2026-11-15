<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Catalog\Services;

use App\Modules\KidsWear\Catalog\Models\Category;
use App\Modules\KidsWear\Catalog\Models\Style;
use ImranDevBd\AiCatalog\Facades\AiCatalog;
use Throwable;

class StyleDraftService
{
    public function fromKeyword(string $keyword, ?int $categoryId = null): Style
    {
        try {
            $pending = AiCatalog::fromKeyword($keyword)
                ->locale('en')
                ->locales(['bn'])
                ->tone('playful')
                ->forProduct(null);

            $generation = $pending->generate();
            $listing = $generation->listing();
            $style = Style::query()->create([
                'name' => $listing->title ?: $keyword,
                'name_bn' => data_get($listing->translations, 'bn.title'),
                'excerpt' => $listing->shortDescription,
                'description' => $listing->longDescription,
                'description_bn' => data_get($listing->translations, 'bn.long_description'),
                'meta_title' => $listing->metaTitle,
                'meta_description' => $listing->metaDescription,
                'care_instructions' => $listing->careInstructions,
                'specs' => $listing->specs,
                'tags' => $listing->tags,
                'faq' => $listing->faq,
                'json_ld' => $listing->jsonLd,
                'translations' => $listing->translations,
                'image_alt' => $listing->altText,
                'category_id' => $listing->categoryId ?: $categoryId,
                'status' => 'draft',
            ]);

            return $style;
        } catch (Throwable $exception) {
            return $this->localDraft($keyword, $categoryId, $exception->getMessage());
        }
    }

    public function localDraft(string $keyword, ?int $categoryId, ?string $reason = null): Style
    {
        $category = $categoryId ? Category::query()->find($categoryId) : Category::query()->first();
        $name = ucwords(trim($keyword));

        return Style::query()->create([
            'name' => $name,
            'name_bn' => $name,
            'category_id' => $category?->id,
            'excerpt' => 'Sampling draft prepared for '.$name.'.',
            'description' => trim("Kids wear style developed from the brief \"{$name}\". "
                ."Garment is planned for soft cotton, snap or rib finishes, and a full infant-to-junior size run. "
                .($reason ? "AI Catalog was not used ({$reason}). A merchandiser can enrich this tech pack." : '')),
            'care_instructions' => 'Machine wash cold. Do not bleach. Tumble dry low.',
            'specs' => [
                'fabric' => '100% cotton jersey',
                'audience' => 'children',
                'market' => 'export',
            ],
            'tags' => ['kids', 'export', 'cotton'],
            'status' => 'draft',
            'meta_title' => $name.' | Rongdhonu Kids Wear',
            'meta_description' => 'Export kids wear style '.$name.' from Gazipur, Bangladesh.',
        ]);
    }
}
