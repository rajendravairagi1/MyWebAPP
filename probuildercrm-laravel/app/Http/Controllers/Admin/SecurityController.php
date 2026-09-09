<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\Totp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SecurityController extends Controller
{
    public function index(Request $request)
    {
        $enabled = (bool) SiteSetting::get('admin_2fa_enabled');
        $pendingSecret = $request->session()->get('admin_2fa_pending_secret');

        $qr = null;
        if ($pendingSecret) {
            $qr = [
                'secret' => $pendingSecret,
                'otpauth' => Totp::otpauthUrl($pendingSecret, 'admin', config('site.name', 'ProBuilderCRM')),
            ];
        }

        return view('admin.security.index', [
            'enabled' => $enabled,
            'qr' => $qr,
            'freshBackupCodes' => $request->session()->pull('fresh_backup_codes'),
        ]);
    }

    public function start(Request $request)
    {
        $secret = Totp::generateSecret();
        $request->session()->put('admin_2fa_pending_secret', $secret);

        return redirect()->route('admin.security.index');
    }

    public function confirm(Request $request)
    {
        $secret = $request->session()->get('admin_2fa_pending_secret');

        if (! $secret) {
            return redirect()->route('admin.security.index');
        }

        $request->validate(['code' => 'required|string']);

        if (! Totp::verify($secret, (string) $request->input('code'))) {
            return back()->withErrors(['code' => 'Incorrect code - check the time on your phone and try the current 6-digit code.']);
        }

        SiteSetting::set('admin_2fa_secret', $secret);
        SiteSetting::set('admin_2fa_enabled', '1');

        $plainCodes = $this->generateBackupCodes();

        $request->session()->forget('admin_2fa_pending_secret');
        $request->session()->put('fresh_backup_codes', $plainCodes);

        return redirect()->route('admin.security.index')->with('status', 'Two-factor authentication is on.');
    }

    public function cancel(Request $request)
    {
        $request->session()->forget('admin_2fa_pending_secret');

        return redirect()->route('admin.security.index');
    }

    public function disable(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        if (! hash_equals((string) config('admin.password'), (string) $request->input('password'))) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        SiteSetting::set('admin_2fa_enabled', '');
        SiteSetting::set('admin_2fa_secret', '');
        SiteSetting::set('admin_2fa_backup_codes', '[]');

        return redirect()->route('admin.security.index')->with('status', 'Two-factor authentication is off.');
    }

    public function regenerateBackupCodes(Request $request)
    {
        if (! SiteSetting::get('admin_2fa_enabled')) {
            return redirect()->route('admin.security.index');
        }

        $plainCodes = $this->generateBackupCodes();
        $request->session()->put('fresh_backup_codes', $plainCodes);

        return redirect()->route('admin.security.index')->with('status', 'New backup codes generated - your old ones no longer work.');
    }

    /**
     * 10 single-use recovery codes for if the phone with the authenticator
     * app is lost - each stored only as a hash, shown in plain text this
     * one time. Format avoids 0/O/1/I so a handwritten copy is unambiguous.
     */
    private function generateBackupCodes(): array
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        $plainCodes = collect(range(1, 10))->map(function () use ($alphabet) {
            $raw = collect(range(1, 10))->map(fn () => $alphabet[random_int(0, strlen($alphabet) - 1)])->implode('');

            return substr($raw, 0, 5).'-'.substr($raw, 5, 5);
        })->all();

        $hashed = array_map(fn ($code) => Hash::make(str_replace('-', '', $code)), $plainCodes);
        SiteSetting::set('admin_2fa_backup_codes', json_encode($hashed));

        return $plainCodes;
    }
}
