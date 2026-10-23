<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\JasaController;
use App\Http\Controllers\KeunggulanController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PermohonanController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\UserController;
use App\Models\Visitor;
use Illuminate\Support\Facades\Route;

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
Route::get('/portfolio', [FrontController::class, 'portfolio'])->name('front.portfolio');
Route::get('Kontak', function () {
    return view('kontak');
});

Route::post('/api/chatbot/message', [ChatbotController::class, 'sendMessage'])->name('chatbot.message');
Route::post('/api/telegram/webhook', [TelegramWebhookController::class, 'handle'])->name('telegram.webhook');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $visitors = Visitor::orderBy('created_at', 'desc')->paginate(10);

        return view('dashboard', compact('visitors'));
    })->name('dashboard');

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);

    Route::get('/company-profiles', [CompanyProfileController::class, 'index'])->name('company-profiles.index');
    Route::post('/company-profiles', [CompanyProfileController::class, 'store'])->name('company-profiles.store');
    Route::put('/company-profiles/{uuid}', [CompanyProfileController::class, 'update'])->name('company-profiles.update');

    Route::resource('project-types', ProjectTypeController::class);
    Route::resource('portfolios', PortfolioController::class);
    Route::resource('testimonials', TestimonialController::class);
    Route::resource('faqs', FaqController::class);
    Route::resource('partners', PartnerController::class);
    Route::resource('jasas', JasaController::class);
    Route::resource('keunggulans', KeunggulanController::class);
    Route::resource('permohonans', PermohonanController::class);
    Route::resource('beritas', BeritaController::class);
    Route::resource('abouts', AboutController::class)->only(['index', 'store', 'update']);

    Route::get('/profile', [UserController::class, 'profile'])->name('profile.index');
    Route::post('/profile/change-password', [UserController::class, 'changePassword'])->name('profile.change-password');
});
