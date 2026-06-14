<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentAnalysis;
use App\Models\Rule;
use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * Service that analyses a document against active rules using OpenAI.
 *
 * Flow:
 * 1. Extract raw text from the stored document (PDF, DOCX, TXT, image via OCR).
 * 2. Fetch all active rules from the database.
 * 3. Build a structured prompt that asks OpenAI to:
 *    - Summarise the document.
 *    - Extract key points.
 *    - Cross-reference against each rule and report inconsistencies.
 * 4. Parse the structured JSON response and persist a DocumentAnalysis record.
 */
class DocumentAnalysisService
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    /**
     * Run (or re-run) analysis on the given document.
     */
    public function analyze(Document $document, ?int $userId = null): DocumentAnalysis
    {
        $userId ??= auth()->id();

        $this->auditService->log(
            'analysis_started',
            'Document',
            $document->id,
            ['filename' => $document->filename],
            $userId,
        );

        // 1. Extract text from the document
        $text = $this->extractText($document);

        // 2. Fetch active rules
        $rules = Rule::where('is_active', true)->get();

        // 3. Build the OpenAI prompt
        $rulesText = $rules->map(fn (Rule $r) => "[Rule #{$r->id}] {$r->title}: {$r->description}")
            ->implode("\n");

        $prompt = $this->buildPrompt($text, $rulesText);

        // 4. Call OpenAI
        $requestPayload = [
            'model' => config('openai.model', 'gpt-4o-mini'),
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'response_format' => ['type' => 'json_object'],
        ];

        try {
            $response = OpenAI::chat()->create($requestPayload);
            $responseContent = $response->choices[0]->message->content;
            $parsed = json_decode($responseContent, true, 512, JSON_THROW_ON_ERROR);
            $openaiResponse = $response->toArray();
        } catch (Exception $e) {
            Log::error('OpenAI analysis failed', [
                'document_id' => $document->id,
                'error'       => $e->getMessage(),
            ]);

            $this->auditService->log(
                'analysis_failed',
                'DocumentAnalysis',
                $document->id,
                ['error' => $e->getMessage()],
                $userId,
            );

            throw $e;
        }

        // 5. Map OpenAI "inconsistencies" back to rule_ids for the response
        $inconsistencies = collect($parsed['inconsistencies'] ?? [])->map(function ($inc) use ($rules) {
            $matched = $rules->firstWhere('title', $inc['rule_title'] ?? null);

            return [
                'rule_id'    => $matched?->id ?? $inc['rule_id'] ?? null,
                'rule_title' => $inc['rule_title'] ?? 'Unknown rule',
                'description'=> $inc['description'] ?? '',
                'severity'   => $inc['severity'] ?? 'medium',
            ];
        })->toArray();

        // 6. Save / update analysis record
        $analysis = DocumentAnalysis::updateOrCreate(
            ['document_id' => $document->id],
            [
                'summary'               => $parsed['summary'] ?? '',
                'key_points'            => $parsed['key_points'] ?? [],
                'inconsistencies'       => $inconsistencies,
                'openai_request_payload'=> $requestPayload,
                'openai_response'       => $openaiResponse,
                'analyzed_by'           => $userId,
                'analyzed_at'           => now(),
            ],
        );

        // 7. Update document status
        $document->update(['status' => 'analyzed']);

        $this->auditService->log(
            'analysis_completed',
            'DocumentAnalysis',
            $analysis->id,
            [
                'document_id'    => $document->id,
                'summary_length' => strlen($parsed['summary'] ?? ''),
                'key_points_count' => count($parsed['key_points'] ?? []),
                'inconsistencies_count' => count($inconsistencies),
            ],
            $userId,
        );

        return $analysis;
    }

    /**
     * Build the prompt sent to OpenAI.
     *
     * The prompt asks for a structured JSON response containing:
     * - summary: a short summary of the document.
     * - key_points: array of important points.
     * - inconsistencies: array of objects with rule_title, description, severity.
     *
     * Inconsistencies are detected by comparing document content against each
     * active rule and identifying potential non-compliances or conflicts.
     */
    protected function buildPrompt(string $documentText, string $rulesText): string
    {
        $rulesBlock = $rulesText
            ? "Here are the compliance rules / requirements that the document should satisfy:\n\n{$rulesText}"
            : "There are no specific compliance rules defined yet.";

        return <<<PROMPT
You are a document compliance analysis assistant. Your task is to analyse the provided document content and compare it against a set of compliance rules.

Document content:
"""
{$documentText}
"""

{$rulesBlock}

Please analyse the document and return a **valid JSON object** (no markdown fences) with the following structure:
{
  "summary": "A concise 2-3 sentence summary of the document's purpose and content.",
  "key_points": [
    "Important point extracted from the document."
  ],
  "inconsistencies": [
    {
      "rule_title": "The title of the rule that may be violated or is relevant.",
      "description": "Explain how the document content conflicts with or fails to meet the rule.",
      "severity": "low|medium|high"
    }
  ]
}

- If no inconsistencies are found, return an empty array for "inconsistencies".
- For each inconsistency, assign a severity level: low (minor concern), medium (moderate concern), high (critical compliance failure).
- Base your analysis strictly on the provided document text and rules.
PROMPT;
    }

    /**
     * Extract raw text from a document based on its MIME type.
     *
     * Supports:
     * - text/plain (TXT)
     * - application/pdf (PDF via smalot/pdfparser)
     * - application/vnd.openxmlformats-officedocument.wordprocessingml.document (DOCX)
     * - image/* (basic placeholder – real OCR would need tesseract or a vision API call)
     */
    protected function extractText(Document $document): string
    {
        $fullPath = Storage::path($document->stored_path);

        if (! file_exists($fullPath)) {
            throw new Exception("Document file not found at {$fullPath}");
        }

        $mime = $document->mime_type;
        $ext = strtolower(pathinfo($document->filename, PATHINFO_EXTENSION));

        if (str_starts_with($mime, 'text/') || $ext === 'txt') {
            return file_get_contents($fullPath);
        }

        if ($mime === 'application/pdf' || $ext === 'pdf') {
            return $this->extractPdfText($fullPath);
        }

        if (in_array($mime, [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword',
            'application/zip',
            'application/octet-stream',
        ]) || in_array($ext, ['docx', 'doc'])) {
            return $this->extractDocxText($fullPath);
        }

        if (str_starts_with($mime, 'image/') || in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'])) {
            return '[Image content – OCR not implemented. Upload a text-based document for analysis.]';
        }

        throw new Exception("Unsupported MIME type: {$mime}");
    }

    protected function extractPdfText(string $path): string
    {
        if (! class_exists(\Smalot\PdfParser\Parser::class)) {
            throw new Exception('PDF parser library not installed. Run: composer require smalot/pdfparser');
        }

        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($path);

        return $pdf->getText();
    }

    protected function extractDocxText(string $path): string
    {
        if (! class_exists(\PhpDocxReader\PhpDocxReader::class)) {
            // Fallback: use a simple ZIP + XML extraction
            return $this->fallbackDocxExtract($path);
        }

        $docx = new \PhpDocxReader\PhpDocxReader();
        $docx->setFile($path);

        return $docx->getContent();
    }

    protected function fallbackDocxExtract(string $path): string
    {
        $xml = false;

        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            if ($zip->open($path) === true) {
                $xml = $zip->getFromName('word/document.xml');
                $zip->close();
            }
        }

        if ($xml === false) {
            $tmpDir = sys_get_temp_dir() . '/docx_' . uniqid();
            @mkdir($tmpDir, 0777, true);

            $escaped = escapeshellarg($path);
            $escapedDir = escapeshellarg($tmpDir);
            exec("unzip -o {$escaped} -d {$escapedDir} 2>/dev/null", $output, $exitCode);

            $xmlPath = "{$tmpDir}/word/document.xml";

            if ($exitCode !== 0 || !file_exists($xmlPath)) {
                $this->rmdirRecursive($tmpDir);
                throw new Exception('Failed to extract DOCX text. Install php-zip: sudo apt install php8.2-zip');
            }

            $xml = file_get_contents($xmlPath);
            $this->rmdirRecursive($tmpDir);
        }

        if ($xml === false) {
            throw new Exception('Could not find word/document.xml in DOCX archive');
        }

        $xml = simplexml_load_string($xml);
        if ($xml === false) {
            throw new Exception('Failed to parse word/document.xml in DOCX archive');
        }

        $namespaces = $xml->getNamespaces(true);
        $body = $xml->children($namespaces['w'])->body ?? $xml->body;

        $text = '';
        foreach ($body->children($namespaces['w'])->p ?? [] as $paragraph) {
            foreach ($paragraph->children($namespaces['w'])->r ?? [] as $run) {
                $text .= (string) $run->children($namespaces['w'])->t . ' ';
            }
            $text .= "\n";
        }

        return trim($text);
    }

    protected function rmdirRecursive(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir($item->getRealPath());
            } else {
                @unlink($item->getRealPath());
            }
        }

        @rmdir($dir);
    }
}
