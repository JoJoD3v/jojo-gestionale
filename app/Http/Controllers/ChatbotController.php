<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatbotController extends Controller
{
    public function __construct(private ChatbotService $chatbotService) {}

    /**
     * Pagina dedicata al chatbot (vista intera).
     */
    public function index()
    {
        return view('chatbot.index');
    }

    /**
     * Endpoint AJAX: riceve la domanda e la cronologia, restituisce la risposta GPT.
     */
    public function ask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'domanda'       => ['required', 'string', 'min:1', 'max:1000'],
            'cronologia'    => ['nullable', 'array', 'max:20'],
            'cronologia.*.role'    => ['required_with:cronologia', 'string', 'in:user,assistant'],
            'cronologia.*.content' => ['required_with:cronologia', 'string', 'max:2000'],
        ]);

        $risposta = $this->chatbotService->ask(
            $validated['domanda'],
            $validated['cronologia'] ?? []
        );

        return response()->json(['risposta' => $risposta]);
    }
}
