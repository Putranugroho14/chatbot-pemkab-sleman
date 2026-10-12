<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Semua route chatbot sekarang dilindungi oleh middleware auth (wajib login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chatbot routes - protected
    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat');
    Route::post('/upload', [ChatbotController::class, 'upload'])->name('chatbot.upload');
    Route::get('/chat-history', [ChatbotController::class, 'history'])->name('chatbot.history');
});

require __DIR__.'/auth.php';
