<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Welcome back, {{ auth()->user()->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Quick Ask -->
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-3">What's on your mind?</h3>
                <div class="flex gap-3">
                    <input type="text" id="quick-ask" placeholder="Ask Kogi anything about food..." class="flex-1 border-gray-300 rounded-md shadow-sm text-sm" />
                    <button onclick="goToChat()" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Ask</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Recent Recipes -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-800">Recent Recipes</h3>
                        <a href="{{ route('recipes') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all →</a>
                    </div>
                    <div id="recent-recipes">
                        <p class="text-sm text-gray-400">Loading...</p>
                    </div>
                </div>

                <!-- Shopping List -->
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-800">Shopping List</h3>
                        <a href="{{ route('shopping') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all →</a>
                    </div>
                    <div id="shopping-summary">
                        <p class="text-sm text-gray-400">Loading...</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function goToChat() {
            const message = document.getElementById('quick-ask').value.trim();
            if (message) {
                window.location.href = '/chat?q=' + encodeURIComponent(message);
            } else {
                window.location.href = '/chat';
            }
        }

        document.getElementById('quick-ask').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') goToChat();
        });

        async function loadRecentRecipes() {
            const response = await fetch('/my-recipes');
            const recipes = await response.json();
            const container = document.getElementById('recent-recipes');

            if (!recipes.length) {
                container.innerHTML = '<p class="text-sm text-gray-400">No recipes saved yet. Ask Kogi to get started!</p>';
                return;
            }

            container.innerHTML = recipes.slice(0, 3).map(r => `
                <a href="/recipes/${r.id}" class="block py-2 border-b border-gray-100 last:border-0">
                    <p class="text-sm text-gray-700 hover:text-indigo-600">${r.name}</p>
                    ${r.cuisine_tags ? `<p class="text-xs text-gray-400">${r.cuisine_tags}</p>` : ''}
                </a>
            `).join('');
        }

        async function loadShoppingSummary() {
            const response = await fetch('/shopping-summary');
            const data = await response.json();
            const container = document.getElementById('shopping-summary');

            if (!data.total) {
                container.innerHTML = '<p class="text-sm text-gray-400">Your shopping list is empty.</p>';
                return;
            }

            container.innerHTML = `
                <p class="text-sm text-gray-700">${data.remaining} item${data.remaining !== 1 ? 's' : ''} remaining</p>
                <p class="text-xs text-gray-400">${data.checked} of ${data.total} checked off</p>
                <div class="mt-3 bg-gray-100 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: ${data.total ? (data.checked / data.total * 100) : 0}%"></div>
                </div>
            `;
        }

        loadRecentRecipes();
        loadShoppingSummary();
    </script>
</x-app-layout>