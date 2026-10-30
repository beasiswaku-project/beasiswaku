<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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
     * Register a new mahasiswa account and sign them in.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create($request->safe()->except('terms'));

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route($user->role->homeRoute());
    }
}
