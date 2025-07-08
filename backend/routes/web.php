<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('EventsHome');
});

// Anticipated Public Routes (Ineria/ Web)
Route::get('/tickets', [TicketController::class, 'browse'] );
Route::get('/cart', [CartController::class, 'show'] );
Route::post('/cart', [CartController::class, 'update'] );
Route::delete('/cart/{id}', [CartController::class, 'delete'] );
Route::post('/checkout', [CartController::class, 'submit'] );
Route::get('/checkout/success', [CartController::class, 'success'] );
Route::get('/checkout/cancel', [CartController::class, 'cancel'] );
Route::get('/order/{id}}', [OrderController::class, 'view'] );




Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Login and registration all handled by jetstream right now.

    // Anticipated Counter Staff Routes (Inertia / Web UI)
    Route::get('/counter', [CounterController::class, 'index'] );
    Route::get('/counter/order', [CounterController::class, 'createOrder'] );
    Route::get('/counter/order/{id}', [CounterController::class, 'browse'] );

    // Admin Dashboard
    Route::get('/admin', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');// Admin Dashboard

    // Normally these would be resources but I'm not setting up CRUD in backend for this. We're seeding and viewing
    Route::get('/admin/events', [EventController::class, 'index']);
    Route::get('/admin/tickets', [TicketController::class, 'index']);
    Route::get('/admin/orders', [OrderController::class, 'index']);
    Route::get('/admin/scans', [ScanController::class, 'index']);
    Route::get('/admin/staff', [UserController::class, 'indexStaff']); // view specific role.

});
