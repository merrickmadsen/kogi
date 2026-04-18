<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function ask(Request $request)
    {
        $user = auth()->user();
        $profile = $user->kogiProfile;

        $systemPrompt = "You are Kogi, a personal food assistant. You help users cook healthier meals at home.";

        if ($profile) {
            $systemPrompt .= " Here is what you know about this user:";
            $systemPrompt .= " Dietary restrictions: " . ($profile->dietary_restrictions ?? 'none');
            $systemPrompt .= " Food allergies: " . ($profile->food_allergies ?? 'none');
            $systemPrompt .= " Kitchen skill level: " . ($profile->skill_level ?? 'unknown');
            $systemPrompt .= " Pantry staples they always have: " . ($profile->pantry_staples ?? 'unknown');
            $systemPrompt .= " Kitchen equipment: " . ($profile->kitchen_equipment ?? 'unknown');
            $systemPrompt .= " Use this information to personalize every response.";
        }

        $response = Http::withoutVerifying()->withHeaders([
            'x-api-key' => env('ANTHROPIC_API_KEY'),
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-sonnet-4-6',
            'max_tokens' => 1024,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => $request->input('message')]
            ],
        ]);

        return response()->json([
            'response' => $response->json('content.0.text')
        ]);
    }
}