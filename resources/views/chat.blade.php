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

    <script>
        const messagesDiv = document.getElementById('chat-messages');
        const input = document.getElementById('chat-input');
        const button = document.getElementById('send-button');

        function addMessage(text, isUser) {
            const div = document.createElement('div');
            div.className = isUser 
                ? 'text-right' 
                : 'text-left';
            div.innerHTML = `<span class="inline-block px-4 py-2 rounded-lg ${isUser ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-800'} max-w-prose text-sm whitespace-pre-wrap">${text}</span>`;
            messagesDiv.appendChild(div);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
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
                addMessage(data.response, false);
            } catch (error) {
                addMessage('Something went wrong. Please try again.', false);
            }

            button.disabled = false;
            button.textContent = 'Ask';
        });

        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') button.click();
        });
    </script>
</x-app-layout>