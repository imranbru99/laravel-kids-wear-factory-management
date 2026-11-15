<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach (['en', 'bn'] as $locale)
    @foreach (['home', 'about', 'process', 'certifications', 'catalog', 'gallery', 'blog', 'contact'] as $name)
        <url><loc>{{ route($name, $locale) }}</loc></url>
    @endforeach
    @foreach ($styles as $style)
        <url><loc>{{ route('styles.show', [$locale, $style]) }}</loc></url>
    @endforeach
    @foreach ($posts as $post)
        <url><loc>{{ route('blog.show', [$locale, $post]) }}</loc></url>
    @endforeach
@endforeach
</urlset>
