<?php

use App\Http\Controllers\AdminDashboardController;
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
    'admin' // only admin role allowed
])->group(function () {
    // Admin Dashboard
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Normally these would be resources but I'm not setting up CRUD in backend for this. We're seeding and viewing
    Route::get('/admin/staff', [AdminDashboardController::class, 'staff'])->name('admin.dashboard.staff');
    Route::get('/admin/events', [EventController::class, 'index']);
    Route::get('/admin/tickets', [TicketController::class, 'index']);
    Route::get('/admin/orders', [OrderController::class, 'index']);
    Route::get('/admin/scans', [ScanController::class, 'index']);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'counter' // must be admin or counter role
])->group(function () {

    // Anticipated Counter Staff Routes (Inertia / Web UI)
    Route::get('/counter', [CounterController::class, 'index'] )->name('counter.dashboard');
    Route::get('/counter/order', [CounterController::class, 'createOrder'] );
    Route::get('/counter/order/{id}', [CounterController::class, 'browse'] );
});
