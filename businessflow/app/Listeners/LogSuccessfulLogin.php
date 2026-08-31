<?php

namespace App\Listeners;

use App\Models\LoginLog;
use App\Support\IpGeolocation;
use App\Support\UserAgentParser;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

/**
 * Records every successful login (password form + the demo "See Demo"
 * auto-login, both of which fire this stock event) for the platform
 * admin's Login Activity page — who logged in, from where, and on what
 * device. Auto-discovered by Laravel; no manual registration needed.
 */
class LogSuccessfulLogin
{
    public function __construct(private readonly Request $request)
    {
    }

    public function handle(Login $event): void
    {
        $ua = UserAgentParser::parse($this->request->userAgent());
        $ip = (string) $this->request->ip();
        $geo = IpGeolocation::lookup($ip);

        LoginLog::create([
            'user_id' => $event->user->id,
            'user_name' => $event->user->name,
            'user_email' => $event->user->email,
            'business_name' => $this->businessNameFor($event->user),
            'ip_address' => $ip,
            'country' => $geo['country'],
            'city' => $geo['city'],
            'device_type' => $ua['device_type'],
            'platform' => $ua['platform'],
            'browser' => $ua['browser'],
            'user_agent' => $this->request->userAgent(),
            'logged_in_at' => now(),
        ]);
    }

    private function businessNameFor(\App\Models\User $user): ?string
    {
        if ($company = $user->ownedCompany) {
            return $company->name.' ('.__('Company').')';
        }

        if ($branch = $user->managedBranches()->first()) {
            return $branch->name.' ('.__('Branch').')';
        }

        return $user->businesses()->oldest('business_user.created_at')->first()?->name;
    }
}
