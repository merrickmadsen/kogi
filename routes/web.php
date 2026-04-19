<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KogiProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RecipeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/kogi', [KogiProfileController::class, 'update'])->name('profile.kogi.update');
    Route::get('/chat', function () {
        return view('chat');
    })->name('chat');
    Route::post('/chat', [ChatController::class, 'ask'])->name('chat.ask');
    Route::get('/recipes', function () {
        return view('recipes.index');
    })->name('recipes');
    Route::get('/my-recipes', [RecipeController::class, 'index'])->name('recipes.data');
    Route::get('/recipes/{id}', function ($id) {
        $recipe = \App\Models\Recipe::with(['ingredients', 'instructions'])->findOrFail($id);
        return view('recipes.show', ['recipe' => $recipe]);
    })->name('recipes.show');
    Route::post('/create-recipe', [ChatController::class, 'createRecipe'])->name('create.recipe');
});

require __DIR__.'/auth.php';
