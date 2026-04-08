<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VectorService
{
    public function upsertChunks(array $chunks, array $embeddings, string $documentName): void
    {
        $vectors = [];

        foreach ($chunks as $i => $chunk) {
            $vectors[] = [
                'id' => (string) Str::uuid(),
                'values' => $embeddings[$i],
                'metadata' => [
                    'text' => $chunk,
                    'document' => $documentName,
                    'chunk_index' => $i,
                ],
            ];
        }

        $response = Http::withHeaders($this->headers())
            ->post($this->host() . '/vectors/upsert', [
                'vectors' => $vectors,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Pinecone upsert failed: ' . $response->body());
        }
    }

    public function query(array $vector, int $topK = 5): array
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->host() . '/query', [
                'vector' => $vector,
                'topK' => $topK,
                'includeMetadata' => true,
            ]);

        if (!$response->successful()) {
            throw new \Exception('Pinecone query failed: ' . $response->body());
        }

        return $response->json('matches', []);
    }

    public function buildContextFromMatches(array $matches): string
    {
        $parts = [];
        foreach ($matches as $match) {
            $parts[] = $match['metadata']['text'] ?? '';
        }

        return trim(implode("\n\n---\n\n", array_filter($parts)));
    }

    private function headers(): array
    {
        return [
            'Api-Key' => env('PINECONE_API_KEY'),
            'Content-Type' => 'application/json',
        ];
    }

    private function host(): string
    {
        return 'https://' . env('PINECONE_INDEX_HOST');
    }
}
