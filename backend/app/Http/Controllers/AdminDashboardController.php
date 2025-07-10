<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index() {
        return Inertia::render('AdminDashboard/Index');
    }
    public function staff(Request $request) {
        // Get possible filters
        $role_filter = $request->input('role');
        $device_filter = $request->input('device');

        return Inertia::render('AdminDashboard/Staff');
    }
}
