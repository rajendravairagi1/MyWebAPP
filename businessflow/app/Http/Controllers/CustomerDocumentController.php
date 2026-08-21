<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerDocumentController extends Controller
{
    public function store(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $data['file'];
        $path = $file->store('customer-documents/'.$customer->id, 'local');

        $customer->documents()->create([
            'name' => ($data['name'] ?? null) ?: $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('status', 'Document uploaded.');
    }

    public function download(Customer $customer, CustomerDocument $document): StreamedResponse
    {
        abort_unless($document->customer_id === $customer->id, 404);

        return Storage::disk('local')->download($document->path, $document->name);
    }

    public function destroy(Customer $customer, CustomerDocument $document): RedirectResponse
    {
        abort_unless($document->customer_id === $customer->id, 404);

        Storage::disk('local')->delete($document->path);
        $document->delete();

        return back()->with('status', 'Document removed.');
    }
}
