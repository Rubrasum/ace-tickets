<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 🔐 Public login route for mobile staff
Route::post('/login', [AuthController::class, 'login']);
// Protected routes for authenticated mobile users
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/validate-ticket', [TicketController::class, 'validateQR']);
    Route::get('/event/current', [EventController::class, 'current']);
    Route::post('/scans', [ScanController::class, 'store']);
    Route::get('/user', [UserController::class, 'profile']);
    Route::get('/tickets/{qr}', [TicketController::class, 'showByQR']);
});

