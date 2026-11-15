<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Website\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use ImranDev\UniversalSlug\SlugOptions;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

class Post extends Model
{
    use SoftDeletes, HasUniversalSlug;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->usingLanguage('en')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'author_id');
    }

    public function localizedTitle(): string
    {
        return app()->getLocale() === 'bn' && $this->title_bn ? $this->title_bn : $this->title;
    }

    public function localizedBody(): string
    {
        return app()->getLocale() === 'bn' && $this->body_bn ? $this->body_bn : $this->body;
    }
}
