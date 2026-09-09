<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('admin_authenticated')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $adminPassword = config('admin.password');

        if (! $adminPassword || ! hash_equals($adminPassword, $request->input('password'))) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $request->session()->regenerate();

        if (SiteSetting::get('admin_2fa_enabled')) {
            $request->session()->put('admin_password_verified', true);

            return redirect()->route('admin.login.verify');
        }

        $request->session()->put('admin_authenticated', true);

        return redirect()->route('admin.dashboard');
    }

    public function showVerify(Request $request)
    {
        if (! $request->session()->get('admin_password_verified')) {
            return redirect()->route('admin.login');
        }

        return view('admin.login-verify');
    }

    public function verify(Request $request)
    {
        if (! $request->session()->get('admin_password_verified')) {
            return redirect()->route('admin.login');
        }

        $request->validate(['code' => 'required|string']);

        $secret = SiteSetting::get('admin_2fa_secret');
        $code = trim((string) $request->input('code'));

        $ok = $secret && Totp::verify($secret, $code);

        if (! $ok) {
            $ok = $this->consumeBackupCode($code);
        }

        if (! $ok) {
            return back()->withErrors(['code' => 'Incorrect code.']);
        }

        $request->session()->forget('admin_password_verified');
        $request->session()->regenerate();
        $request->session()->put('admin_authenticated', true);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_authenticated', 'admin_password_verified']);
        $request->session()->regenerate();

        return redirect()->route('admin.login');
    }

    private function consumeBackupCode(string $code): bool
    {
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code) ?? '');

        if ($code === '') {
            return false;
        }

        $hashes = json_decode((string) SiteSetting::get('admin_2fa_backup_codes', '[]'), true) ?: [];

        foreach ($hashes as $i => $hash) {
            if (Hash::check($code, $hash)) {
                unset($hashes[$i]);
                SiteSetting::set('admin_2fa_backup_codes', json_encode(array_values($hashes)));

                return true;
            }
        }

        return false;
    }
}
