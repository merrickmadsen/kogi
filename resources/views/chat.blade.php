<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ask Kogi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                
                <!-- Chat Messages -->
                <div id="chat-messages" class="space-y-4 mb-6 h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                    <p class="text-gray-400 text-sm text-center">Ask Kogi anything about food, cooking, or meal ideas...</p>
                </div>

                <!-- Input -->
                <div class="flex gap-3">
                    <input 
                        type="text" 
                        id="chat-input" 
                        placeholder="What should I make for dinner tonight?" 
                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <button 
                        id="send-button"
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                    >
                        Ask
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        const messagesDiv = document.getElementById('chat-messages');
        const input = document.getElementById('chat-input');
        const button = document.getElementById('send-button');

        function addMessage(text, isUser, showRecipeButton = false) {
            const div = document.createElement('div');
            div.className = isUser ? 'text-right' : 'text-left';
            
            let html = `<span class="inline-block px-4 py-2 rounded-lg ${isUser ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-800'} max-w-prose text-sm prose">${isUser ? text : marked.parse(text)}</span>`;
            
            if (showRecipeButton) {
                html += `<div class="mt-2">
                    <button onclick="createRecipe(this)" data-message="${encodeURIComponent(text)}" class="px-3 py-1 bg-green-600 text-white text-xs rounded-md hover:bg-green-700">
                        + Create Recipe
                    </button>
                </div>`;
            }
            
            div.innerHTML = html;
            messagesDiv.appendChild(div);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        async function createRecipe(button) {
            const message = decodeURIComponent(button.dataset.message);
            button.textContent = 'Creating...';
            button.disabled = true;

            try {
                const response = await fetch('/create-recipe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message })
                });

                const data = await response.json();
                
                if (data.success) {
                    button.textContent = '✓ Recipe Saved';
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-gray-400');
                    
                    const link = document.createElement('a');
                    link.href = `/recipes/${data.recipe_id}`;
                    link.className = 'ml-2 text-xs text-indigo-600 hover:text-indigo-800';
                    link.textContent = 'View Recipe →';
                    button.parentElement.appendChild(link);
                }
            } catch (error) {
                button.textContent = 'Failed - Try Again';
                button.disabled = false;
            }
        }

        button.addEventListener('click', async () => {
            const message = input.value.trim();
            if (!message) return;

            addMessage(message, true);
            input.value = '';
            button.disabled = true;
            button.textContent = 'Thinking...';

            try {
                const response = await fetch('/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message })
                });

                const data = await response.json();
                addMessage(data.response, false, data.show_recipe_button);
            } catch (error) {
                addMessage(data.response, false);
            }

            button.disabled = false;
            button.textContent = 'Ask';
        });

        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') button.click();
        });

        // Auto-send message from dashboard
        const urlParams = new URLSearchParams(window.location.search);
        const prefilledMessage = urlParams.get('q');
        if (prefilledMessage) {
            input.value = prefilledMessage;
            button.click();
        }
    </script>
</x-app-layout>