<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Catalog\Models;

use App\Modules\KidsWear\Support\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Style extends Model implements HasMedia
{
    use SoftDeletes, RecordsActivity, HasUniversalSlug, InteractsWithMedia;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'specs' => 'array',
            'tags' => 'array',
            'faq' => 'array',
            'size_guide' => 'array',
            'json_ld' => 'array',
            'translations' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->usingLanguage('en')
            ->doNotGenerateSlugsOnUpdate();
    }

    protected static function booted(): void
    {
        static::creating(function (Style $style): void {
            if (! $style->style_code) {
                $style->style_code = 'KW-'.now()->format('y').'-'.str_pad((string) (static::withTrashed()->count() + 1), 3, '0', STR_PAD_LEFT);
            }
            $style->sku ??= $style->style_code;
            $style->status ??= 'draft';
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\Category::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\Season::class);
    }

    public function ageGroup(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\AgeGroup::class);
    }

    public function colorways(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Catalog\Models\Colorway::class);
    }

    public function sizeCharts(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Catalog\Models\SizeChart::class);
    }

    public function techPacks(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Catalog\Models\TechPack::class);
    }

    public function bomItems(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Catalog\Models\BomItem::class);
    }

    public function costings(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Finance\Models\StyleCosting::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos');
        $this->addMediaCollection('tech-packs');
    }

    public function localizedName(): string
    {
        return app()->getLocale() === 'bn' && $this->name_bn ? $this->name_bn : $this->name;
    }

    public function localizedDescription(): ?string
    {
        return app()->getLocale() === 'bn' && $this->description_bn ? $this->description_bn : $this->description;
    }
}
