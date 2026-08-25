<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Support\ImageCompressor;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BusinessController extends Controller
{
    public function edit(): View
    {
        $business = Business::findOrFail(Tenant::id());

        return view('business.edit', compact('business'));
    }

    public function update(Request $request): RedirectResponse
    {
        $business = Business::findOrFail(Tenant::id());

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'invoice_prefix' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('logo')) {
            if ($business->logo_path) {
                Storage::disk('local')->delete($business->logo_path);
            }

            $file = $request->file('logo');
            $path = $file->store('business-logo/'.$business->id, 'local');
            $absolute = Storage::disk('local')->path($path);

            $tmp = $absolute.'.compressed';
            if (ImageCompressor::compress($absolute, $tmp, maxWidth: 500) && filesize($tmp) > 0) {
                rename($tmp, $absolute);
            } else {
                @unlink($tmp);
            }

            $data['logo_path'] = $path;
        }

        unset($data['logo']);

        $business->update($data);

        return back()->with('status', 'Business details updated.');
    }

    public function logo(): BinaryFileResponse
    {
        $business = Business::findOrFail(Tenant::id());

        abort_unless($business->logo_path, 404);

        $absolute = Storage::disk('local')->path($business->logo_path);
        abort_unless(file_exists($absolute), 404);

        return response()->file($absolute);
    }
}
