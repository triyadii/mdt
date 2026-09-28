<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\FrontController;

Route::get('/', [FrontController::class, 'index'])->name('home');
Route::get('/berita', [FrontController::class, 'berita'])->name('front.berita');
Route::get('/berita/{slug}', [FrontController::class, 'beritaDetail'])->name('front.berita.detail');
Route::get('Katalog', function () {
    return view('katalog');
});
Route::get('KatalogDetail', function () {
    return view('katalogDetail');
});
Route::get('TentangKami', function () {
    return view('tentangKami');
});
Route::get('Portfolio', function () {
    return view('portfolio');
});
Route::get('Kontak', function () {
    return view('kontak');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $visitors = \App\Models\Visitor::orderBy('created_at', 'desc')->paginate(10);
        return view('dashboard', compact('visitors'));
    })->name('dashboard');

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);

    Route::get('/company-profiles', [\App\Http\Controllers\CompanyProfileController::class, 'index'])->name('company-profiles.index');
    Route::post('/company-profiles', [\App\Http\Controllers\CompanyProfileController::class, 'store'])->name('company-profiles.store');
    Route::put('/company-profiles/{uuid}', [\App\Http\Controllers\CompanyProfileController::class, 'update'])->name('company-profiles.update');

    Route::resource('project-types', \App\Http\Controllers\ProjectTypeController::class);
    Route::resource('portfolios', \App\Http\Controllers\PortfolioController::class);
    Route::resource('testimonials', \App\Http\Controllers\TestimonialController::class);
    Route::resource('faqs', \App\Http\Controllers\FaqController::class);
    Route::resource('partners', \App\Http\Controllers\PartnerController::class);
    Route::resource('jasas', \App\Http\Controllers\JasaController::class);
    Route::resource('keunggulans', \App\Http\Controllers\KeunggulanController::class);
    Route::resource('permohonans', \App\Http\Controllers\PermohonanController::class);
    Route::resource('beritas', \App\Http\Controllers\BeritaController::class);
    Route::resource('abouts', \App\Http\Controllers\AboutController::class)->only(['index', 'store', 'update']);

    Route::get('/profile', [UserController::class, 'profile'])->name('profile.index');
    Route::post('/profile/change-password', [UserController::class, 'changePassword'])->name('profile.change-password');
});
