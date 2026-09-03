<?php

namespace App\Http\Controllers;

use App\Models\ProjectUnit;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicProfileController extends Controller
{
    /**
     * The public, no-login "business card" page a link recipient lands
     * on — the builder's photo/about/contact alongside every property
     * their business currently has open for sale, each already carrying
     * its own share link so a visitor can drill into one directly.
     */
    public function show(string $token): View
    {
        $user = User::where('profile_token', $token)->firstOrFail();
        $business = $user->primaryBusiness();

        $units = $business
            ? ProjectUnit::with(['project', 'photos'])
                ->where('business_id', $business->id)
                ->where('status', 'available')
                ->whereNull('archived_at')
                ->get()
                ->sortBy([['project.name', 'asc'], ['unit_number', 'asc']])
                ->each(fn (ProjectUnit $unit) => $unit->shareToken())
            : collect();

        return view('public-profile.show', compact('user', 'business', 'units'));
    }

    /**
     * Serves the profile photo for the page above — scoped to the
     * profile token alone, since a guest has no login to check the
     * photo against anything else.
     */
    public function photo(string $token): BinaryFileResponse
    {
        $user = User::where('profile_token', $token)->firstOrFail();

        abort_unless($user->photo_path, 404);

        $absolute = Storage::disk('local')->path($user->photo_path);
        abort_unless(file_exists($absolute), 404);

        return response()->file($absolute);
    }
}
