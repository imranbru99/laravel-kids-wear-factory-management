<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/en'));
Route::get('/sitemap.xml', [WebsiteController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', fn () => response("User-agent: *\nAllow: /\nSitemap: ".url('/sitemap.xml')."\n", 200, ['Content-Type' => 'text/plain']))->name('robots');
Route::get('/track/{code}', [WebsiteController::class, 'track'])->name('track');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('{locale}')
    ->where(['locale' => 'en|bn'])
    ->middleware('locale')
    ->group(function (): void {
        Route::get('/', [WebsiteController::class, 'home'])->name('home');
        Route::get('/about', fn (string $locale) => app(WebsiteController::class)->page($locale, 'about'))->name('about');
        Route::get('/process', fn (string $locale) => app(WebsiteController::class)->page($locale, 'process'))->name('process');
        Route::get('/contact', fn (string $locale) => app(WebsiteController::class)->page($locale, 'contact'))->name('contact');
        Route::get('/certifications', [WebsiteController::class, 'certifications'])->name('certifications');
        Route::get('/styles', [WebsiteController::class, 'catalog'])->name('catalog');
        Route::get('/styles/{style:slug}', [WebsiteController::class, 'style'])->name('styles.show');
        Route::get('/gallery', [WebsiteController::class, 'gallery'])->name('gallery');
        Route::get('/journal', [WebsiteController::class, 'blog'])->name('blog');
        Route::get('/journal/{post:slug}', [WebsiteController::class, 'post'])->name('blog.show');
        Route::post('/inquiry', [WebsiteController::class, 'inquire'])->middleware('throttle:8,1')->name('inquiry.store');
    });
