<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_santri' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'nama_santri' => $request->nama_santri,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_SANTRI,
            'status' => User::STATUS_ACTIVE,
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Redirect berdasarkan role
        switch ($user->role) {
            case User::ROLE_ADMIN:
                return redirect()->route('admin.dashboard');
            case User::ROLE_BENDAHARA:
                return redirect()->route('bendahara.dashboard');
            case User::ROLE_SANTRI:
                return redirect()->route('santri.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }
}
