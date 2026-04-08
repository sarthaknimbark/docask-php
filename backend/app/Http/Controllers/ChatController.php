<?php

namespace App\Http\Controllers;

use App\Services\EmbeddingService;
use App\Services\VectorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function __construct(
        private EmbeddingService $embeddingService,
        private VectorService $vectorService
    ) {
    }

    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string|min:3',
        ]);

        $question = $request->input('question');
        $questionVector = $this->embeddingService->createEmbedding($question);
        $matches = $this->vectorService->query($questionVector, 5);
        $context = $this->vectorService->buildContextFromMatches($matches);

        $answer = $this->generateAnswer($question, $context);

        return response()->json([
            'answer' => $answer,
            'matches' => count($matches),
        ]);
    }

    private function generateAnswer(string $question, string $context): string
    {
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_CHAT_MODEL', 'gpt-4o-mini'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Answer from provided context only. If not present, say "I do not know from this document."',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Context:\n{$context}\n\nQuestion:\n{$question}",
                    ],
                ],
                'temperature' => 0.2,
            ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI chat failed: ' . $response->body());
        }

        return $response->json('choices.0.message.content', 'No answer generated.');
    }
}
