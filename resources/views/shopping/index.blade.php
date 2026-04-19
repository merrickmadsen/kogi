<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Shopping List
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @foreach($lists as $list)
            <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-lg text-gray-800 mb-4">{{ $list->name }}</h3>

                <!-- Add Item Form -->
                <div class="flex gap-2 mb-6">
                    <input type="text" id="item-name-{{ $list->id }}" placeholder="Add item..." class="flex-1 border-gray-300 rounded-md shadow-sm text-sm" />
                    <input type="text" id="item-quantity-{{ $list->id }}" placeholder="Qty" class="w-16 border-gray-300 rounded-md shadow-sm text-sm" />
                    <input type="text" id="item-unit-{{ $list->id }}" placeholder="Unit" class="w-20 border-gray-300 rounded-md shadow-sm text-sm" />
                    <select id="item-section-{{ $list->id }}" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">Section</option>
                        <option value="Produce">Produce</option>
                        <option value="Meat">Meat</option>
                        <option value="Dairy">Dairy</option>
                        <option value="Pantry">Pantry</option>
                        <option value="Freezer">Freezer</option>
                        <option value="Bakery">Bakery</option>
                        <option value="Other">Other</option>
                    </select>
                    <button onclick="addItem({{ $list->id }})" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Add</button>
                </div>

                <!-- Items grouped by section -->
                <div id="items-{{ $list->id }}">
                    @php
                        $grouped = $list->items->groupBy('store_section');
                    @endphp

                    @foreach($grouped as $section => $items)
                    <div class="mb-4">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ $section ?: 'Uncategorized' }}</h4>
                        @foreach($items as $item)
                        <div class="flex items-center gap-3 py-2 border-b border-gray-100" id="item-row-{{ $item->id }}">
                            <input type="checkbox" {{ $item->checked ? 'checked' : '' }} onchange="toggleItem({{ $item->id }})" class="rounded border-gray-300" />
                            <span class="{{ $item->checked ? 'line-through text-gray-400' : 'text-gray-700' }} flex-1 text-sm">
                                {{ $item->quantity ? $item->quantity . ' ' : '' }}{{ $item->unit ? $item->unit . ' ' : '' }}{{ $item->name }}
                            </span>
                            <button onclick="removeItem({{ $item->id }})" class="text-red-400 hover:text-red-600 text-xs">Remove</button>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <script>
        async function addItem(listId) {
            const name = document.getElementById(`item-name-${listId}`).value.trim();
            if (!name) return;

            const quantity = document.getElementById(`item-quantity-${listId}`).value;
            const unit = document.getElementById(`item-unit-${listId}`).value;
            const section = document.getElementById(`item-section-${listId}`).value;

            const response = await fetch(`/shopping/${listId}/items`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ name, quantity, unit, store_section: section })
            });

            if (response.ok) {
                window.location.reload();
            }
        }

        async function toggleItem(itemId) {
            await fetch(`/shopping/items/${itemId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
        }

        async function removeItem(itemId) {
            await fetch(`/shopping/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            document.getElementById(`item-row-${itemId}`).remove();
        }

        document.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && document.activeElement.id.startsWith('item-name-')) {
                const listId = document.activeElement.id.split('-')[2];
                addItem(listId);
            }
        });
    </script>
</x-app-layout>