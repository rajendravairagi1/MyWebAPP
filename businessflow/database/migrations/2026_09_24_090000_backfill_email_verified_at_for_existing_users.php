<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * This ships in the same deploy as User implementing MustVerifyEmail
 * (see AppServiceProvider) — every route in the app already carries
 * 'verified' middleware and has for a while, it was just a no-op until
 * now. Without this backfill, every existing customer (created by
 * Admin, never sent a verification email) would be locked out of their
 * own account the instant this deploy goes live. Only accounts created
 * from here on — via the new self-signup flow — start with a genuinely
 * unverified email and need to click the link.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->whereNull('email_verified_at')->update([
            'email_verified_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Deliberately irreversible — there's no way to tell, after the
        // fact, which rows were backfilled here versus genuinely
        // verified by a user clicking their email link.
    }
};
