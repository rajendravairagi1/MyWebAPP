<?php

namespace App\Http\Controllers;

use App\Models\ProjectUnit;
use App\Models\UnitMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PropertyShareController extends Controller
{
    /**
     * Ensures this property has a share link (generating one on first
     * use) and sends the owner back with it, ready to copy or share.
     */
    public function generate(Request $request, ProjectUnit $unit): RedirectResponse
    {
        $token = $unit->shareToken();

        return back()->with('shareUrl', route('property-share.show', $token));
    }

    /**
     * The public, no-login page a link recipient actually lands on —
     * looked up by its share token alone, since a guest has no tenant
     * session for the usual per-business scoping to apply to.
     */
    public function show(string $token): View
    {
        $unit = ProjectUnit::with(['project', 'business'])
            ->where('share_token', $token)
            ->firstOrFail();

        $photos = $unit->photos()->get();

        return view('property-share.show', compact('unit', 'photos'));
    }

    /**
     * Serves one photo for the public page above — scoped to both the
     * share token (so it only works for a property someone actually
     * generated a link for) and type=photo (layouts/papers never go out
     * on this public route, only the gallery photos do).
     */
    public function photo(string $token, UnitMedia $media): BinaryFileResponse
    {
        $unit = ProjectUnit::where('share_token', $token)->firstOrFail();

        abort_unless($media->project_unit_id === $unit->id && $media->type === 'photo', 404);

        $absolute = Storage::disk('local')->path($media->path);
        abort_unless(file_exists($absolute), 404);

        return response()->file($absolute, ['Content-Type' => $media->mime_type]);
    }

    /**
     * A downloadable/WhatsApp-able PDF brochure of the same property —
     * photos are embedded as base64 data URIs since they live on the
     * private 'local' disk, not a publicly fetchable URL dompdf could
     * load on its own.
     */
    public function pdf(string $token): \Illuminate\Http\Response
    {
        $unit = ProjectUnit::with(['project', 'business'])
            ->where('share_token', $token)
            ->firstOrFail();

        $photos = $unit->photos()->get()->map(function ($photo) {
            $absolute = Storage::disk('local')->path($photo->path);

            if (! file_exists($absolute)) {
                return null;
            }

            $photo->dataUri = 'data:'.$photo->mime_type.';base64,'.base64_encode(file_get_contents($absolute));

            return $photo;
        })->filter()->values();

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('property-share.pdf', compact('unit', 'photos'))
            ->download($unit->project->name.' - '.$unit->unit_number.'.pdf');
    }
}
