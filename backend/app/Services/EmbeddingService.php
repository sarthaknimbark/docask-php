<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    public function createEmbedding(string $text): array
    {
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/embeddings', [
                'model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
                'input' => $text,
            ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI embedding failed: ' . $response->body());
        }

        return $response->json('data.0.embedding', []);
    }
}
