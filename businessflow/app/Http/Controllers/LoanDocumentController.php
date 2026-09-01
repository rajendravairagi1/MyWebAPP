<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LoanDocumentController extends Controller
{
    public function store(Request $request, Loan $loan): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $data['file'];
        $path = $file->store('loan-documents/'.$loan->id, 'local');

        $loan->documents()->create([
            'name' => ($data['name'] ?? null) ?: $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('status', 'Document uploaded.');
    }

    public function download(Loan $loan, LoanDocument $document): StreamedResponse
    {
        abort_unless($document->loan_id === $loan->id, 404);

        return Storage::disk('local')->download($document->path, $document->name);
    }

    public function destroy(Loan $loan, LoanDocument $document): RedirectResponse
    {
        abort_unless($document->loan_id === $loan->id, 404);

        Storage::disk('local')->delete($document->path);
        $document->delete();

        return back()->with('status', 'Document removed.');
    }
}
