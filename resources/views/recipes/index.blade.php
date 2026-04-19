<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Recipes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Recipe Grid -->
            <div id="recipe-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <p class="text-gray-400 col-span-3 text-center py-12">No recipes saved yet. Ask Kogi for a recipe to get started!</p>
            </div>

        </div>
    </div>

    <script>
        async function loadRecipes() {
            const response = await fetch('/my-recipes', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const recipes = await response.json();
            const grid = document.getElementById('recipe-grid');

            if (recipes.length === 0) return;

            grid.innerHTML = recipes.map(recipe => `
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg text-gray-800">${recipe.name}</h3>
                    <div class="mt-2 text-sm text-gray-500 space-y-1">
                        ${recipe.prep_time ? `<p>Prep: ${recipe.prep_time} mins</p>` : ''}
                        ${recipe.cook_time ? `<p>Cook: ${recipe.cook_time} mins</p>` : ''}
                        ${recipe.cuisine_tags ? `<p>Cuisine: ${recipe.cuisine_tags}</p>` : ''}
                    </div>
                    <div class="mt-4">
                        <a href="/recipes/${recipe.id}" class="text-sm text-indigo-600 hover:text-indigo-800">View Recipe →</a>
                    </div>
                </div>
            `).join('');
        }

        loadRecipes();
    </script>
</x-app-layout>