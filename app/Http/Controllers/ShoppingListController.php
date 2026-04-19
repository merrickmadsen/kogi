<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;

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
        $item = $list->items()->create([
            'name' => $request->input('name'),
            'quantity' => $request->input('quantity'),
            'unit' => $request->input('unit'),
            'store_section' => $request->input('store_section'),
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
        
        foreach ($items as $item) {
            $list->items()->create([
                'name' => $item['name'],
                'quantity' => $item['quantity'] ?? null,
                'unit' => $item['unit'] ?? null,
                'store_section' => $item['store_section'] ?? null,
                'checked' => false,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
