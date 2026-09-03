<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Support\ImageCompressor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        if ($request->hasFile('photo')) {
            if ($user->photo_path) {
                Storage::disk('local')->delete($user->photo_path);
            }

            $file = $request->file('photo');
            $path = $file->store('user-photo/'.$user->id, 'local');
            $absolute = Storage::disk('local')->path($path);

            $tmp = $absolute.'.compressed';
            if (ImageCompressor::compress($absolute, $tmp, maxWidth: 500) && filesize($tmp) > 0) {
                rename($tmp, $absolute);
            } else {
                @unlink($tmp);
            }

            $data['photo_path'] = $path;
        }

        unset($data['photo']);

        $user->fill($data);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Serves the current user's own uploaded photo — used inside the
     * app (header avatar, profile page preview) while logged in. The
     * public profile page uses a separate, token-scoped route instead
     * (see App\Http\Controllers\PublicProfileController::photo), since a
     * guest viewing that page has no login for this one to check.
     */
    public function photo(Request $request): BinaryFileResponse
    {
        $user = $request->user();

        abort_unless($user->photo_path, 404);

        $absolute = Storage::disk('local')->path($user->photo_path);
        abort_unless(file_exists($absolute), 404);

        return response()->file($absolute);
    }

    /**
     * Ensures this user has a public profile link (generating one on
     * first use) and sends them back with it, ready to copy or share.
     */
    public function generateProfileLink(Request $request): RedirectResponse
    {
        $token = $request->user()->profileToken();

        return back()->with('profileUrl', route('public-profile.show', $token));
    }

    /**
     * Switches the language the app is shown in for this user only —
     * every other team member on the same business keeps their own
     * preference (see App\Http\Middleware\SetLocale).
     */
    public function updateLocale(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'locale' => ['required', 'string', 'in:'.implode(',', array_keys(config('locales')))],
        ]);

        $request->user()->update(['locale' => $data['locale']]);

        return Redirect::route('profile.edit')->with('status', 'locale-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
