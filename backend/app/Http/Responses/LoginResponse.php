<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{

    public function toResponse($request): RedirectResponse
    {
        $user = $request->user();
        \Log::info('Custom LoginResponse triggered for user', ['user' => $request->user()?->id]);
        
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('ticket counter')) {
            return redirect()->route('counter.dashboard');
        }

        // These users have no access to features on the backend app, so send home.
        if ($user->hasRole('ticket scanner')) {
            return redirect()->route('home');
        }

        // Default
        return redirect()->route('home');
    }

}
