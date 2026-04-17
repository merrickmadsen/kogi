<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Food Profile') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Help Kogi personalize your experience by telling us about your food preferences.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.kogi.update') }}" class="mt-6 space-y-6">
        @csrf

        <div>
            <x-input-label for="dietary_restrictions" :value="__('Dietary Restrictions')" />
            <x-text-input id="dietary_restrictions" name="dietary_restrictions" type="text" class="mt-1 block w-full" :value="old('dietary_restrictions', $kogiProfile?->dietary_restrictions ?? '')" />
        </div>

        <div>
            <x-input-label for="food_allergies" :value="__('Food Allergies')" />
            <x-text-input id="food_allergies" name="food_allergies" type="text" class="mt-1 block w-full" :value="old('food_allergies', $kogiProfile?->food_allergies ?? '')" />
        </div>

        <div>
            <x-input-label for="skill_level" :value="__('Kitchen Skill Level')" />
            <select id="skill_level" name="skill_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="beginner" {{ ($kogiProfile?->skill_level ?? '') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                <option value="intermediate" {{ ($kogiProfile?->skill_level ?? '') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                <option value="advanced" {{ ($kogiProfile?->skill_level ?? '') == 'advanced' ? 'selected' : '' }}>Advanced</option>
            </select>
        </div>

        <div>
            <x-input-label for="pantry_staples" :value="__('Pantry Staples')" />
            <textarea id="pantry_staples" name="pantry_staples" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('pantry_staples', $kogiProfile?->pantry_staples ?? '') }}</textarea>
        </div>

        <div>
            <x-input-label for="kitchen_equipment" :value="__('Kitchen Equipment')" />
            <textarea id="kitchen_equipment" name="kitchen_equipment" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('kitchen_equipment', $kogiProfile?->kitchen_equipment ?? '') }}</textarea>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </div>
    </form>
</section>