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
    </script>
</x-app-layout>