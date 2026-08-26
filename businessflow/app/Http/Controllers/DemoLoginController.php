<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * "See a live demo" from the public landing page — logs the visitor
 * straight into the pre-provisioned demo account (see
 * App\Http\Controllers\Admin\AdminController::store, is_demo flag) with
 * no password prompt. There's nothing sensitive in a demo account by
 * design, and its data gets wiped from the admin panel between prospects.
 */
class DemoLoginController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $demoUser = User::where('email', config('platform.demo_email'))->first();

        abort_unless($demoUser, 404, 'Demo account not set up yet.');

        Auth::login($demoUser);

        return redirect()->route('dashboard');
    }
}
