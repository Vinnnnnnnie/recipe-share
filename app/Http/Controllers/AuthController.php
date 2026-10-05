<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Container\Attributes\Auth as AttributesAuth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Facade;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    public function showRegister() {
        return Inertia::render('Auth/Register');
    }

    public function showLogin() {
        return Inertia::render('Auth/Login');
    }

    public function register(Request $request) {
        $files = Storage::disk('users')->allFiles('');
        if (!$request->image) {
            $image_path = $files[array_rand($files)];
            $request->merge(['image_path' => $image_path]);
        } else {
            $image_path = $request->image->store("users", 'public');
            $image_path = str_replace('users/', '', $image_path);
        }

        $request->merge(['image_path' => $image_path]);
        $validated = $request->validate([
            'username' => 'required|string|unique:users|max:32',
            'first_name' => 'required|string|max:32',
            'last_name' => 'required|string|max:32',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'image_path' => 'required|string',
        ]);
        $validated['password'] = Hash::make($request->password);
        $user = User::create($validated);

        FacadesAuth::login($user);
        return redirect()->route('recipes.index');
    }

    public function login(Request $request) {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (FacadesAuth::attempt($validated)) {
            $request->session()->regenerate();
            Inertia::share('user', auth()->user());

            return redirect()->route(route: 'recipes.index');
        }

        throw ValidationException::withMessages([
            'credentials' => 'Sorry, those credentials did not match our records.',
        ]);
    }

    public function logout(Request $request) {
        FacadesAuth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('show.login');
    }

}
