<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;

Route::get('/', function () {
    return redirect()->route('invoices.index');
});

// ROUTE DE RÉPARATION (Méthode Harick & Matoor)
Route::get('/repair-access', function () {
    $user = \App\Models\User::updateOrCreate(
        ['email' => 'magnagamakelighiclainn@gmail.com'],
        [
            'name' => 'Makelighi Clainn',
            'password' => \Illuminate\Support\Facades\Hash::make('admin2026'),
            'role' => 'admin'
        ]
    );
    return "✅ Accès réparé pour " . $user->email . " ! Essaye de te connecter avec : admin2026";
});

// Authentification
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Chatbot (accessible aux utilisateurs connectés)
Route::post('/chatbot', [ChatbotController::class, 'handle'])->name('chatbot.handle')->middleware('auth');

// Groupe Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/reset', [SettingsController::class, 'resetData'])->name('settings.reset');
    
    Route::resource('clients', ClientController::class);
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/force-reset', [UserController::class, 'forceReset'])->name('users.forceReset');
    
    // Boîte Noire (Audit Log)
    Route::get('/audit-log', [AdminController::class, 'auditLog'])->name('audit-log');
    
    // Corbeille (Soft Deletes)
    Route::get('/trash', [AdminController::class, 'trash'])->name('trash');
    Route::post('/restore', [AdminController::class, 'restore'])->name('restore');
});

Route::get('/invoices/{id}/download', [InvoiceController::class, 'download'])->name('invoices.download');

Route::resource('invoices', InvoiceController::class);
