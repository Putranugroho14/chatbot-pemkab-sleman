<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\DinasAdminController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\EmbedController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Redirect dashboard sesuai role pengguna
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isSuperAdmin()) {
        return redirect()->route('superadmin.dashboard');
    } elseif ($user->isDinasAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('chatbot.index');
})->middleware(['auth'])->name('dashboard');

// Profil Pengguna (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Chatbot Layanan & Simulator (Semua role terautentikasi dapat mengakses sesuai dinas)
Route::middleware('auth')->group(function () {
    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat');
    Route::post('/upload', [ChatbotController::class, 'upload'])->name('chatbot.upload');
    Route::get('/chat-history', [ChatbotController::class, 'history'])->name('chatbot.history');
});

// SUPER ADMIN: Manajemen Sistem, Pengguna, dan Lintas Dinas
Route::middleware(['auth', 'dinas.access:super_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('users', UserManagementController::class);
    Route::get('/agents', [SuperAdminController::class, 'agents'])->name('agents');
    Route::get('/analytics', [SuperAdminController::class, 'analytics'])->name('analytics');
    Route::get('/chat-history', [SuperAdminController::class, 'allChatHistory'])->name('chat-history');
});

// DINAS ADMIN: Manajemen Terisolasi Dinas Sendiri
Route::middleware(['auth', 'dinas.access:dinas_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DinasAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/staff', [DinasAdminController::class, 'staff'])->name('staff');
    Route::post('/staff', [DinasAdminController::class, 'storeStaff'])->name('staff.store');
    Route::post('/knowledge-base/upload', [DinasAdminController::class, 'uploadDocument'])->name('kb.upload');
    Route::get('/chat-history', [DinasAdminController::class, 'chatHistory'])->name('chat-history');
});

// PUBLIC EMBED WIDGET: Dapat disematkan di website dinas mana pun
Route::prefix('embed')->name('embed.')->group(function () {
    Route::get('/{dinas}', [EmbedController::class, 'widget'])->name('widget');
    Route::post('/{dinas}/chat', [EmbedController::class, 'chat'])->name('chat');
});

require __DIR__.'/auth.php';
