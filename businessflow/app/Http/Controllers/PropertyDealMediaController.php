<?php

namespace App\Http\Controllers;

use App\Models\PropertyDeal;
use App\Models\PropertyDealMedia;
use App\Support\ImageCompressor;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PropertyDealMediaController extends Controller
{
    /**
     * Allowed file types and max upload size (KB) per media category,
     * checked before any compression runs.
     */
    protected const RULES = [
        'photo' => ['mimes' => 'jpg,jpeg,png,webp', 'max' => 20480],
        'layout' => ['mimes' => 'jpg,jpeg,png,webp,pdf', 'max' => 20480],
        'document' => ['mimes' => 'pdf,jpg,jpeg,png', 'max' => 20480],
    ];

    public function store(Request $request, PropertyDeal $deal): RedirectResponse
    {
        $type = $request->validate([
            'type' => ['required', 'in:photo,layout,document'],
        ])['type'];

        // Layout/Papers can carry the seller's original documents (often
        // showing the actual purchase price), so uploading or viewing
        // them needs the same "financials" access as the deal itself —
        // only Photos are open to a marketing-only team member.
        abort_unless($type === 'photo' || Tenant::canFinancials('property_deals'), 403);

        $rule = self::RULES[$type];

        $data = $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'mimes:'.$rule['mimes'], 'max:'.$rule['max']],
        ]);

        foreach ($data['files'] as $file) {
            $this->storeOne($deal, $type, $file);
        }

        return back()->with('status', ucfirst($type).' uploaded.');
    }

    protected function storeOne(PropertyDeal $deal, string $type, UploadedFile $file): void
    {
        $path = $file->store('deal-media/'.$deal->id.'/'.$type, 'local');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $size = $file->getSize();
        $absolute = Storage::disk('local')->path($path);

        if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            $tmp = $absolute.'.compressed';

            if (ImageCompressor::compress($absolute, $tmp) && filesize($tmp) > 0 && filesize($tmp) < $size) {
                rename($tmp, $absolute);
                $size = filesize($absolute);
            } else {
                @unlink($tmp);
            }
        }

        $deal->media()->create([
            'type' => $type,
            'path' => $path,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'size' => $size,
            'uploaded_by' => auth()->id(),
        ]);
    }

    public function show(PropertyDeal $deal, PropertyDealMedia $media): BinaryFileResponse
    {
        abort_unless($media->property_deal_id === $deal->id, 404);
        abort_unless($media->type === 'photo' || Tenant::canFinancials('property_deals'), 403);

        $absolute = Storage::disk('local')->path($media->path);
        abort_unless(file_exists($absolute), 404);

        return response()->file($absolute, ['Content-Type' => $media->mime_type]);
    }

    public function download(PropertyDeal $deal, PropertyDealMedia $media): BinaryFileResponse
    {
        abort_unless($media->property_deal_id === $deal->id, 404);
        abort_unless($media->type === 'photo' || Tenant::canFinancials('property_deals'), 403);

        $absolute = Storage::disk('local')->path($media->path);
        abort_unless(file_exists($absolute), 404);

        return response()->download($absolute, $media->original_name);
    }

    public function destroy(PropertyDeal $deal, PropertyDealMedia $media): RedirectResponse
    {
        abort_unless($media->property_deal_id === $deal->id, 404);
        abort_unless($media->type === 'photo' || Tenant::canFinancials('property_deals'), 403);

        Storage::disk('local')->delete($media->path);
        $media->delete();

        return back()->with('status', 'Removed.');
    }
}
