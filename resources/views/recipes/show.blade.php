<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" id="recipe-title">
            Loading...
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-8">

                <!-- Meta Info -->
                <div id="recipe-meta" class="flex gap-6 text-sm text-gray-500 mb-8"></div>

                <div class="mt-4 mb-8">
                    <button onclick="document.getElementById('shopping-modal').classList.remove('hidden')" class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                        + Add to Shopping List
                    </button>
                </div>

                <!-- Ingredients -->
                <div class="mb-8">
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Ingredients</h3>
                    <ul id="recipe-ingredients" class="space-y-2 text-gray-700"></ul>
                </div>

                <!-- Instructions -->
                <div>
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">Instructions</h3>
                    <ol id="recipe-instructions" class="space-y-4 text-gray-700 list-decimal list-inside"></ol>
                </div>

                <!-- Notes -->
                <div id="recipe-notes-section" class="mt-8 hidden">
                    <h3 class="font-semibold text-lg text-gray-800 mb-2">Notes</h3>
                    <p id="recipe-notes" class="text-gray-600 text-sm"></p>
                </div>

            </div>
        </div>
    </div>

    <!-- Shopping List Modal -->
    <div id="shopping-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="font-semibold text-lg text-gray-800 mb-4">Add to Shopping List</h3>
            
            <div id="modal-ingredients" class="space-y-2 mb-6 max-h-64 overflow-y-auto"></div>
            
            <div class="flex gap-3 justify-end">
                <button onclick="document.getElementById('shopping-modal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="addToShoppingList()" class="px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                    Add Selected
                </button>
            </div>
        </div>
    </div>

    <script>
        const recipe = @json($recipe);
        
        document.getElementById('recipe-title').textContent = recipe.name;

        const meta = [];
        if (recipe.prep_time) meta.push(`Prep: ${recipe.prep_time} mins`);
        if (recipe.cook_time) meta.push(`Cook: ${recipe.cook_time} mins`);
        if (recipe.base_servings) meta.push(`Serves: ${recipe.base_servings}`);
        if (recipe.cuisine_tags) meta.push(`Cuisine: ${recipe.cuisine_tags}`);
        document.getElementById('recipe-meta').innerHTML = meta.map(m => `<span>${m}</span>`).join('');

        document.getElementById('recipe-ingredients').innerHTML = recipe.ingredients
            .map(i => `<li>${i.amount ? i.amount + ' ' : ''}${i.unit ? i.unit + ' ' : ''}${i.name}</li>`)
            .join('');

        document.getElementById('recipe-instructions').innerHTML = recipe.instructions
            .map(i => `<li>${i.description}</li>`)
            .join('');

        if (recipe.notes) {
            document.getElementById('recipe-notes-section').classList.remove('hidden');
            document.getElementById('recipe-notes').textContent = recipe.notes;
        }

        const pantryStaples = @json($pantryStaples);
        const pantryList = pantryStaples.toLowerCase().split(',').map(s => s.trim());

        function isPantryItem(name) {
            return pantryList.some(staple => name.toLowerCase().includes(staple));
        }

        // Populate modal when opened
        document.querySelector('[onclick*="shopping-modal"]').addEventListener('click', () => {
            const ingredients = recipe.ingredients;
            const container = document.getElementById('modal-ingredients');
            
            container.innerHTML = ingredients.map(i => {
                const label = `${i.amount ? i.amount + ' ' : ''}${i.unit ? i.unit + ' ' : ''}${i.name}`;
                const isPantry = isPantryItem(i.name);
                return `
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" value='${JSON.stringify(i)}' ${isPantry ? '' : 'checked'} class="ingredient-checkbox rounded border-gray-300" />
                        <span class="text-sm ${isPantry ? 'text-gray-400' : 'text-gray-700'}">${label} ${isPantry ? '<span class="text-xs text-gray-400">(pantry)</span>' : ''}</span>
                    </label>
                `;
            }).join('');
        });

        async function addToShoppingList() {
            const checked = [...document.querySelectorAll('.ingredient-checkbox:checked')];
            const items = checked.map(cb => {
                const i = JSON.parse(cb.value);
                return {
                    name: i.name,
                    quantity: i.amount,
                    unit: i.unit,
                    store_section: null
                };
            });

            const listId = {{ auth()->user()->shoppingLists()->first()?->id ?? 'null' }};
            
            if (!listId) {
                alert('No shopping list found. Please visit the Shopping List page first.');
                return;
            }

            const response = await fetch(`/shopping/${listId}/add-from-recipe`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ items })
            });

            if (response.ok) {
                document.getElementById('shopping-modal').classList.add('hidden');
                alert('Items added to your shopping list!');
            }
        }
    </script>
</x-app-layout>