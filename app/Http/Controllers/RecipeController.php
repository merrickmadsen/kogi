<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = auth()->user()->recipes()->with(['ingredients', 'instructions'])->get();
        return response()->json($recipes);
    }

    public function store(Request $request)
    {
        $recipe = auth()->user()->recipes()->create($request->only([
            'name', 'base_servings', 'prep_time', 'cook_time', 
            'cuisine_tags', 'photo', 'source', 'notes'
        ]));

        foreach ($request->input('ingredients', []) as $ingredient) {
            $recipe->ingredients()->create($ingredient);
        }

        foreach ($request->input('instructions', []) as $instruction) {
            $recipe->instructions()->create($instruction);
        }

        return response()->json($recipe->load(['ingredients', 'instructions']));
    }

    public function show(Recipe $recipe)
    {
        return response()->json($recipe->load(['ingredients', 'instructions']));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $recipe->update($request->only([
            'name', 'base_servings', 'prep_time', 'cook_time',
            'cuisine_tags', 'photo','source', 'notes'
        ]));

        return response()->json($recipe->load(['ingredients', 'instructions']));
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        return response()->json(['message' => 'Recipe deleted']);
    }
}
