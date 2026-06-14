<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use App\Services\AuditService;
use App\Services\DocumentAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenAI\Exceptions\RateLimitException;

class DocumentController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
        protected DocumentAnalysisService $analysisService,
    ) {}

    public function index()
    {
        $documents = Document::with('user')
            ->latest()
            ->paginate(15);

        return view('documents.index', compact('documents'));
    }

    public function store(StoreDocumentRequest $request)
    {
        $file = $request->file('document');
        $storedPath = $file->store('documents');

        $extToMime = [
            'pdf'  => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc'  => 'application/msword',
            'txt'  => 'text/plain',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'bmp'  => 'image/bmp',
            'webp' => 'image/webp',
        ];
        $ext = strtolower($file->getClientOriginalExtension());
        $mimeType = $extToMime[$ext] ?? $file->getMimeType();

        $document = Document::create([
            'user_id'       => auth()->id(),
            'filename'      => $file->getClientOriginalName(),
            'original_path' => $file->getRealPath(),
            'stored_path'   => $storedPath,
            'mime_type'     => $mimeType,
            'size'          => $file->getSize(),
            'status'        => 'pending',
        ]);

        $this->auditService->log(
            'document_uploaded',
            'Document',
            $document->id,
            [
                'filename'  => $document->filename,
                'size'      => $document->size,
                'mime_type' => $document->mime_type,
            ],
        );

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        $document->load('user', 'analysis');

        return view('documents.show', compact('document'));
    }

    public function analyze(Request $request, Document $document)
    {
        try {
            $this->analysisService->analyze($document);
        } catch (RateLimitException $e) {
            return redirect()->route('documents.show', $document)
                ->with('error', 'AI analysis is temporarily unavailable due to rate limiting. Please wait a moment and try again.');
        } catch (\Exception $e) {
            return redirect()->route('documents.show', $document)
                ->with('error', 'Analysis failed: ' . $e->getMessage());
        }

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document analysis completed.');
    }

    public function destroyAnalysis(Document $document)
    {
        if ($document->analysis) {
            $document->analysis->delete();
            $document->update(['status' => 'pending']);

            $this->auditService->log(
                'analysis_deleted',
                'Document',
                $document->id,
                ['filename' => $document->filename],
            );
        }

        return redirect()->route('documents.show', $document)
            ->with('success', 'Analysis removed successfully.');
    }

    public function destroy(Document $document)
    {
        if ($document->analysis) {
            $document->analysis->delete();
        }

        Storage::delete($document->stored_path);
        $document->delete();

        $this->auditService->log(
            'document_deleted',
            'Document',
            $document->id,
            ['filename' => $document->filename],
        );

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }
}
