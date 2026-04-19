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
            $systemPrompt .= " Use this information subtly to personalize responses only when genuinely relevant. Don't force profile details into every response — if an ingredient isn't a natural fit, don't mention it. The goal is to feel like you know the user, not to constantly reference their profile.";
            $systemPrompt .= " When your response contains a specific dish or recipe suggestion that the user could cook, end your response with exactly this tag on a new line: [RECIPE_READY]. Do not include this tag for general food questions, comparisons, or tips.";
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

        $text = $response->json('content.0.text');
        $showRecipeButton = str_contains($text, '[RECIPE_READY]');
        $text = str_replace('[RECIPE_READY]', '', $text);
        $text = trim($text);

        return response()->json([
            'response' => $text,
            'show_recipe_button' => $showRecipeButton,
        ]);
    }

    public function createRecipe(Request $request)
    {
        $user = auth()->user();
        
        $result = Http::withoutVerifying()->withHeaders([
            'x-api-key' => env('ANTHROPIC_API_KEY'),
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-sonnet-4-6',
            'max_tokens' => 2048,
            'messages' => [
                ['role' => 'user', 'content' => 'Extract this into a recipe JSON with these exact fields: name (string), base_servings (integer), prep_time (integer minutes), cook_time (integer minutes), cuisine_tags (string), notes (string), ingredients (array of objects with amount (decimal), unit (string), name (string), sort_order (integer)), instructions (array of objects with step_number (integer), description (string)). Return ONLY valid JSON, no other text: ' . $request->input('message')]
            ],
        ]);

        $rawText = $result->json('content.0.text');
        $rawText = preg_replace('/```json\s*|\s*```/', '', $rawText);
        $rawText = trim($rawText);
        $recipeData = json_decode($rawText, true);

        if (!$recipeData) {
            return response()->json(['success' => false, 'error' => 'Could not parse recipe'], 500);
        }
        
        $recipe = $user->recipes()->create([
            'name' => $recipeData['name'],
            'base_servings' => $recipeData['base_servings'] ?? null,
            'prep_time' => $recipeData['prep_time'] ?? null,
            'cook_time' => $recipeData['cook_time'] ?? null,
            'cuisine_tags' => $recipeData['cuisine_tags'] ?? null,
            'notes' => $recipeData['notes'] ?? null,
            'source' => 'kogi',
        ]);

        foreach ($recipeData['ingredients'] ?? [] as $ingredient) {
            $recipe->ingredients()->create($ingredient);
        }

        foreach ($recipeData['instructions'] ?? [] as $instruction) {
            $recipe->instructions()->create($instruction);
        }

        return response()->json(['success' => true, 'recipe_id' => $recipe->id]);
    }
}