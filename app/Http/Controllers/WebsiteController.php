<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Modules\KidsWear\Catalog\Models\AgeGroup;
use App\Modules\KidsWear\Catalog\Models\Category;
use App\Modules\KidsWear\Catalog\Models\Season;
use App\Modules\KidsWear\Catalog\Models\Style;
use App\Modules\KidsWear\Production\Models\Bundle;
use App\Modules\KidsWear\Support\Company;
use App\Modules\KidsWear\Website\Models\Certification;
use App\Modules\KidsWear\Website\Models\GalleryItem;
use App\Modules\KidsWear\Website\Models\Inquiry;
use App\Modules\KidsWear\Website\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    public function home(): View
    {
        return view('site.home', [
            'styles' => Style::query()->where('status', 'published')->with(['category', 'ageGroup', 'season', 'colorways'])->latest('published_at')->limit(6)->get(),
            'posts' => Post::query()->whereNotNull('published_at')->latest('published_at')->limit(3)->get(),
            'certs' => Certification::query()->where('is_public', true)->get(),
        ]);
    }

    public function page(string $locale, string $page): View
    {
        abort_unless(in_array($page, ['about', 'process', 'contact'], true), 404);

        return view('site.'.$page, [
            'certs' => Certification::query()->where('is_public', true)->get(),
        ]);
    }

    public function certifications(): View
    {
        return view('site.certifications', [
            'certs' => Certification::query()->where('is_public', true)->orderBy('name')->get(),
        ]);
    }

    public function catalog(Request $request): View
    {
        $styles = Style::query()
            ->where('status', 'published')
            ->with(['category', 'ageGroup', 'season', 'colorways'])
            ->when($request->integer('category'), fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->integer('age'), fn ($q, $id) => $q->where('age_group_id', $id))
            ->when($request->integer('season'), fn ($q, $id) => $q->where('season_id', $id))
            ->when($request->filled('q'), fn ($q) => $q->where(function ($inner) use ($request): void {
                $term = '%'.$request->string('q').'%';
                $inner->where('name', 'like', $term)->orWhere('name_bn', 'like', $term)->orWhere('style_code', 'like', $term);
            }))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('site.catalog', [
            'styles' => $styles,
            'categories' => Category::query()->where('is_active', true)->orderBy('sort')->get(),
            'ages' => AgeGroup::query()->orderBy('sort')->get(),
            'seasons' => Season::query()->orderByDesc('year')->get(),
        ]);
    }

    public function style(string $locale, Style $style): View
    {
        abort_unless($style->status === 'published', 404);
        $style->load(['category', 'ageGroup', 'season', 'colorways', 'sizeCharts.rows', 'techPacks', 'costings']);

        return view('site.style', ['style' => $style]);
    }

    public function gallery(): View
    {
        return view('site.gallery', [
            'items' => GalleryItem::query()->orderBy('sort')->get(),
        ]);
    }

    public function blog(): View
    {
        return view('site.blog', [
            'posts' => Post::query()->whereNotNull('published_at')->latest('published_at')->paginate(6),
        ]);
    }

    public function post(string $locale, Post $post): View
    {
        abort_unless($post->published_at, 404);

        return view('site.post', ['post' => $post]);
    }

    public function inquire(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:contact,buyer'],
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'max:80'],
            'message' => ['required', 'string', 'max:2000'],
            'style_id' => ['nullable', 'exists:styles,id'],
        ]);
        if ($data['type'] === 'buyer') {
            $request->validate(['company' => ['required', 'string', 'max:160']]);
        }
        Inquiry::query()->create($data);

        return back()->with('status', __('kidswear.inquiry_sent'));
    }

    public function track(string $code): View
    {
        $bundle = Bundle::query()->with(['style', 'productionOrder'])->where('code', $code)->firstOrFail();

        return view('site.track', ['bundle' => $bundle]);
    }

    public function sitemap(): \Illuminate\Http\Response
    {
        $styles = Style::query()->where('status', 'published')->get();
        $posts = Post::query()->whereNotNull('published_at')->get();

        return response()->view('site.sitemap', compact('styles', 'posts'))->header('Content-Type', 'application/xml');
    }

    public function switchLocale(string $locale): RedirectResponse
    {
        $target = $locale === 'bn' ? 'en' : 'bn';
        $path = request()->headers->get('referer') ?: url('/'.$target);
        $path = preg_replace('#/(en|bn)(/|$)#', '/'.$target.'$2', $path, 1) ?: url('/'.$target);

        return redirect($path);
    }
}
