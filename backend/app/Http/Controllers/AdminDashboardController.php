<?php

namespace App\Http\Controllers;

use App\Models\Event;
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
            ->select('users.*', 'devices.last_login_at', 'devices.is_active') // include device columns
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
            case 'last_login_at':
                $query->orderBy('devices.last_login_at', $sort_direction);
                break;
            case 'is_active':
                $query->orderBy('devices.is_active', $sort_direction);
                break;
        }

        // Execute query
        $users = $query->get();
        $users = $users->map(function ($user) { // remove the "good stuff"
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')->first() ?? 'None',
                'device_type' => $user->device ? $user->device->type : 'None',
                'last_login_at' => $user->device ? $user->device->last_login_at : null,
                'is_active' => $user->device ? $user->device->is_active : false,
            ];
        });

        // Pass data to Inertia
        return Inertia::render('AdminDashboard/Staff', [
            'users' => $users,
            'filters' => [
                'role' => $role_filter,
                'device' => $device_filter,
            ],
            'sort' => $sort,
            'direction' => ($sort_direction == 'asc'),
            'search' => $search
        ]);
    }
    public function events(Request $request) {
        // Get date filters, that use the start date.
        $from_filter = $request->input('from_date');
        $to_filter = $request->input('to_date');
        $location_filter = $request->input('location');
        $status_filter = $request->input('status');
        // Get possible sort by
        $sort = $request->input('sort', 'starts_at'); // Default sort
        $sort_direction = $request->boolean('direction') ? 'asc' : 'desc'; // Optional ?desc=true
        // search by event name or slug
        $search = $request->input('search');


        $query = Event::query()
            ->when($search, function ($q) use ($search) {
                return $q->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('location', 'LIKE', '%' . $search . '%')
                        ->orWhere('description', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($status_filter, function ($q) use ($status_filter) {
                if ($status_filter === 'active') {
                    return $q->where('is_active', true);
                } elseif ($status_filter === 'inactive') {
                    return $q->where('is_active', false);
                }
                return $q;
            })
            ->when($location_filter, function ($q) use ($location_filter) {
                return $q->where('location', 'LIKE', '%' . $location_filter . '%');
            })
            ->when($from_filter, function ($q) use ($from_filter) {
                return $q->where('starts_at', '>=', $from_filter);
            })
            ->when($to_filter, function ($q) use ($to_filter) {
                return $q->where('ends_at', '<=', $to_filter);
            });
        $query->orderBy($sort, $sort_direction);

        // Execute query
        $events = $query->get();

        // get locations for
        $locations = Event::query()
            ->distinct()
            ->pluck('location')
            ->filter() // Remove any null/empty values
            ->sort()
            ->values(); // Reset array keys

        // Pass data to Inertia
        return Inertia::render('AdminDashboard/Events', [
            'events' => $events,
            'filters' => [
                'from_date' => $from_filter,
                'to_date' => $to_filter,
                'location' =>  $location_filter,
                'status' => $status_filter
            ],
            'sort' => $sort,
            'direction' => ($sort_direction == 'asc'),
            'search' => $search,
            // This will only run when specifically requested, and allows us to get by w/o locations table
            'locations' => fn () => Event::query()
                ->distinct()
                ->pluck('location')
                ->filter() // Remove any null/empty values
                ->sort()
                ->values(),
        ]);
    }
}
