<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Smalot\PdfParser\Parser;

class DocumentService
{
    public function __construct(
        private EmbeddingService $embeddingService,
        private VectorService $vectorService
    ) {
    }

    public function processPdf(UploadedFile $file): array
    {
        // Save PDF to storage/app/documents
        $path = $file->store('documents');
        $fullPath = storage_path('app/' . $path);

        // Extract PDF text
        $text = $this->extractText($fullPath);
        if ($text === '') {
            throw new \Exception('No readable text found in this PDF.');
        }

        // Split text into chunk size between 500-1000
        $chunks = $this->chunkText($text, 800);

        // Generate embedding for each chunk
        $embeddings = [];
        foreach ($chunks as $chunk) {
            $embeddings[] = $this->embeddingService->createEmbedding($chunk);
        }

        // Save vectors in Pinecone with metadata
        $this->vectorService->upsertChunks($chunks, $embeddings, $file->getClientOriginalName());

        return [
            'file' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'chunks' => count($chunks),
        ];
    }

    private function extractText(string $path): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();

        // Basic cleanup for easier chunking
        $text = preg_replace('/\s+/', ' ', $text ?? '');
        return trim((string) $text);
    }

    private function chunkText(string $text, int $size = 800): array
    {
        $chunks = [];
        $length = mb_strlen($text);
        $start = 0;

        while ($start < $length) {
            $chunks[] = trim(mb_substr($text, $start, $size));
            $start += $size;
        }

        return array_values(array_filter($chunks));
    }
}
