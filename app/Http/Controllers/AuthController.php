<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:admin,kasir,gudang',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Akun Anda tidak aktif. Hubungi administrator.'
                ])->withInput($request->only('email', 'role', 'remember'));
            }

            if ($user->role !== $validated['role']) {
                Auth::logout();

                return back()->withErrors([
                    'role' => 'Role tidak sesuai dengan akun ini.'
                ])->withInput($request->only('email', 'role', 'remember'));
            }

            $request->session()->regenerate();

            return $this->redirectToDashboard($user);
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email', 'role', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }

    private function redirectToDashboard(?User $user = null)
    {
        $user = $user ?: Auth::user();

        return match ($user?->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'kasir' => redirect()->route('kasir.dashboard'),
            'gudang' => redirect()->route('gudang.dashboard'),
            default => redirect()->route('login')->withErrors([
                'email' => 'Role pengguna tidak dikenali.'
            ]),
        };
    }
}
