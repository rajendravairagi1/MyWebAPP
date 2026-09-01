<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Models\BrokerDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BrokerDocumentController extends Controller
{
    public function store(Request $request, Broker $broker): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $data['file'];
        $path = $file->store('broker-documents/'.$broker->id, 'local');

        $broker->documents()->create([
            'name' => ($data['name'] ?? null) ?: $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('status', 'Document uploaded.');
    }

    public function download(Broker $broker, BrokerDocument $document): StreamedResponse
    {
        abort_unless($document->broker_id === $broker->id, 404);

        return Storage::disk('local')->download($document->path, $document->name);
    }

    public function destroy(Broker $broker, BrokerDocument $document): RedirectResponse
    {
        abort_unless($document->broker_id === $broker->id, 404);

        Storage::disk('local')->delete($document->path);
        $document->delete();

        return back()->with('status', 'Document removed.');
    }
}
