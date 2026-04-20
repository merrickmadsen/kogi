<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Support\Facades\Http;

class ShoppingListController extends Controller
{
    public function index()
    {
        $lists = auth()->user()->shoppingLists()->with('items')->get();
        
        if ($lists->isEmpty()) {
            $list = auth()->user()->shoppingLists()->create(['name' => 'My List']);
            $lists = collect([$list]);
        }
        
        return view('shopping.index', ['lists' => $lists]);
    }

    public function addItem(Request $request, ShoppingList $list)
    {
        $name = $request->input('name');
        $section = $request->input('store_section');
        
        if (!$section) {
            $categories = $this->categorizeItems([$name]);
            $section = $categories[$name] ?? 'Other';
        }

        $item = $list->items()->create([
            'name' => $name,
            'quantity' => $request->input('quantity'),
            'unit' => $request->input('unit'),
            'store_section' => $section,
            'checked' => false,
        ]);

        return response()->json($item);
    }

    public function toggleItem(ShoppingListItem $item)
    {
        $item->update(['checked' => !$item->checked]);
        return response()->json($item);
    }

    public function removeItem(ShoppingListItem $item)
    {
        $item->delete();
        return response()->json(['success' => true]);
    }

    public function addFromRecipe(Request $request, ShoppingList $list)
    {
        $items = $request->input('items', []);
        $itemNames = array_column($items, 'name');
        
        $categories = $this->categorizeItems($itemNames);

        foreach ($items as $item) {
            $list->items()->create([
                'name' => $item['name'],
                'quantity' => $item['quantity'] ?? null,
                'unit' => $item['unit'] ?? null,
                'store_section' => $categories[$item['name']] ?? 'Other',
                'checked' => false,
            ]);
        }

        return response()->json(['success' => true]);
    }

    private function categorizeItems(array $itemNames): array
    {
        $categories = [
            'Produce', 'Meat & Poultry', 'Seafood', 'Dairy & Eggs', 'Bakery',
            'Canned Goods', 'Grains & Starches', 'Baking', 'Oils & Vinegars',
            'Seasonings & Spices', 'International', 'Beverages', 'Alcohol',
            'Packaged & Prepared Foods', 'Condiments & Sauces', 'Health & Supplements',
            'Freezer', 'Household', 'Personal Care', 'Other'
        ];

        $response = Http::withoutVerifying()->withHeaders([
            'x-api-key' => env('ANTHROPIC_API_KEY'),
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-haiku-4-5',
            'max_tokens' => 1024,
            'messages' => [
                ['role' => 'user', 'content' => 'Categorize each of these grocery items into exactly one of these categories: ' . implode(', ', $categories) . '. Return ONLY a JSON object where keys are the item names and values are the category. No other text. Items: ' . implode(', ', $itemNames)]
            ],
        ]);

        $raw = $response->json('content.0.text');
        $raw = preg_replace('/```json\s*|\s*```/', '', $raw);
        $result = json_decode(trim($raw), true);

        return $result ?? [];
    }
}
