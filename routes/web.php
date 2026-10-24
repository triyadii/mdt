<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatHistoryController;
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
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsAppBlastController;
use App\Http\Controllers\WhatsAppChatController;
use App\Models\Visitor;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/berita', [FrontController::class, 'berita'])->name('front.berita');
Route::get('/berita/{slug}', [FrontController::class, 'beritaDetail'])->name('front.berita.detail');
Route::get('/portfolio', [FrontController::class, 'portfolio'])->name('front.portfolio');

// SEO 301 Redirects for Legacy / Unused Route Endpoints
Route::get('Katalog', fn () => redirect()->to(route('home').'#services', 301));
Route::get('KatalogDetail', fn () => redirect()->to(route('home').'#services', 301));
Route::get('TentangKami', fn () => redirect()->to(route('home').'#about', 301));
Route::get('Kontak', fn () => redirect()->to(route('home').'#contact', 301));

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
    Route::resource('chat-histories', ChatHistoryController::class)->only(['index', 'show', 'destroy']);

    // WhatsApp Live Chat
    Route::prefix('wa-chat')->name('wa-chat.')->group(function () {
        Route::get('/', [WhatsAppChatController::class, 'index'])->name('index');
        Route::get('/status', [WhatsAppChatController::class, 'status'])->name('status');
        Route::post('/start', [WhatsAppChatController::class, 'startSession'])->name('start');
        Route::get('/chats', [WhatsAppChatController::class, 'chats'])->name('chats');
        Route::get('/chats/{id}', [WhatsAppChatController::class, 'messages'])->name('messages');
        Route::delete('/chats/{id}', [WhatsAppChatController::class, 'destroyChat'])->name('destroy-chat');
        Route::delete('/messages/{id}', [WhatsAppChatController::class, 'destroyMessage'])->name('destroy-message');
        Route::post('/send', [WhatsAppChatController::class, 'send'])->name('send');
    });

    // WhatsApp Blast Promosi
    Route::prefix('wa-blast')->name('wa-blast.')->group(function () {
        Route::get('/', [WhatsAppBlastController::class, 'index'])->name('index');
        Route::get('/create', [WhatsAppBlastController::class, 'create'])->name('create');
        Route::post('/', [WhatsAppBlastController::class, 'store'])->name('store');
        Route::get('/{id}', [WhatsAppBlastController::class, 'show'])->name('show');
        Route::post('/{campaignId}/send/{recipientId}', [WhatsAppBlastController::class, 'sendSingle'])->name('send-single');
        Route::delete('/{id}', [WhatsAppBlastController::class, 'destroy'])->name('destroy');
    });

    // Google Maps UMKM Scraper
    Route::prefix('scraper')->name('scraper.')->group(function () {
        Route::get('/', [ScraperController::class, 'index'])->name('index');
        Route::post('/start', [ScraperController::class, 'start'])->name('start');
        Route::get('/schedules', [ScraperController::class, 'schedules'])->name('schedules');
        Route::post('/schedules', [ScraperController::class, 'storeSchedule'])->name('schedules.store');
        Route::delete('/tasks/{id}', [ScraperController::class, 'destroyTask'])->name('tasks.destroy');
        Route::get('/leads', [ScraperController::class, 'leads'])->name('leads');
        Route::post('/leads/push-to-blast', [ScraperController::class, 'pushToWaBlast'])->name('leads.push-to-blast');
        Route::post('/leads/bulk-delete', [ScraperController::class, 'bulkDeleteLeads'])->name('leads.bulk-delete');
        Route::delete('/leads/{id}', [ScraperController::class, 'destroyLead'])->name('leads.destroy');
        Route::get('/tasks/{id}/status', [ScraperController::class, 'taskStatus'])->name('task-status');
    });

    Route::get('/profile', [UserController::class, 'profile'])->name('profile.index');
    Route::post('/profile/change-password', [UserController::class, 'changePassword'])->name('profile.change-password');
});
