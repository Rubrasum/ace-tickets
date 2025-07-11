<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        // Get possible sort by
        $sort = $request->input('sort', 'name'); // Default sort
        $sort_direction = $request->boolean('direction') ? 'asc' : 'desc'; // Optional ?desc=true
        // search
        $search = $request->input('search');


        $query = User::query()
            // Join for sorting
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', \App\Models\User::class);
            })
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->leftJoin('devices', 'users.device_id', '=', 'devices.id')
            ->whereIn('roles.name', ['ticket scanner', 'ticket counter']) // limits to staff roles
            ->when($role_filter, function ($q) use ($role_filter) {
                return $q->where('roles.name', $role_filter);
            })
            ->when($device_filter, function ($q) use ($device_filter) {
                return $q->where('devices.type', $device_filter);
            })
            ->when($search, function ($q) use ($search) {
                return $q->where(function ($query) use ($search) {
                    $query->where('users.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('users.email', 'LIKE', '%' . $search . '%');
                });
            })
            ->select('users.*') // avoid conflicts
            ->with(['roles', 'device']);

        // Apply unified sorting
        switch ($sort) {
            case 'name':
            case 'email':
                $query->orderBy("users.$sort", $sort_direction);
                break;
            case 'role':
                $query->orderBy('roles.name', $sort_direction);
                break;
            case 'device':
            case 'device_type':
                $query->orderBy('devices.type', $sort_direction);
                break;
        }

        // Execute query
        $users = $query->get();

        // Pass data to Inertia
        return Inertia::render('AdminDashboard/Staff', [
            'users' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->pluck('name')->first() ?? 'None',
                    'device_type' => $user->device ? $user->device->type : 'None',
                ];
            }),
            'filters' => [
                'role' => $role_filter,
                'device' => $device_filter,
            ],
            'sort' => $sort,
            'direction' => ($sort_direction == 'asc'),
            'search' => $search
        ]);
    }
}
