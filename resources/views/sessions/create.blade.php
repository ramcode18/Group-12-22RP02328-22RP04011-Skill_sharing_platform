<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Host a New Learning Session') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('sessions.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Session Title')" />
                            <x-text-input id="title" 
                                         class="block mt-1 w-full" 
                                         type="text" 
                                         name="title" 
                                         :value="old('title')" 
                                         required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" 
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    name="description" 
                                    rows="4" 
                                    required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="start_time" :value="__('Start Time')" />
                                <x-text-input id="start_time" 
                                             class="block mt-1 w-full" 
                                             type="datetime-local" 
                                             name="start_time" 
                                             :value="old('start_time')" 
                                             required />
                                <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="end_time" :value="__('End Time')" />
                                <x-text-input id="end_time" 
                                             class="block mt-1 w-full" 
                                             type="datetime-local" 
                                             name="end_time" 
                                             :value="old('end_time')" 
                                             required />
                                <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="mode" :value="__('Session Mode')" />
                            <select id="mode" 
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    name="mode" 
                                    required>
                                <option value="">Select mode</option>
                                <option value="online" {{ old('mode') === 'online' ? 'selected' : '' }}>Online</option>
                                <option value="offline" {{ old('mode') === 'offline' ? 'selected' : '' }}>Offline</option>
                            </select>
                            <x-input-error :messages="$errors->get('mode')" class="mt-2" />
                        </div>

                        <div id="locationField" class="mb-4" style="display: none;">
                            <x-input-label for="location" :value="__('Location')" />
                            <x-text-input id="location" 
                                         class="block mt-1 w-full" 
                                         type="text" 
                                         name="location" 
                                         :value="old('location')" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="max_participants" :value="__('Maximum Participants')" />
                            <x-text-input id="max_participants" 
                                         class="block mt-1 w-full" 
                                         type="number" 
                                         name="max_participants" 
                                         :value="old('max_participants')" 
                                         min="1" 
                                         required />
                            <x-input-error :messages="$errors->get('max_participants')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="tools_needed" :value="__('Tools Needed (one per line)')" />
                            <textarea id="tools_needed" 
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    name="tools_needed" 
                                    rows="3">{{ old('tools_needed') }}</textarea>
                            <x-input-error :messages="$errors->get('tools_needed')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                       name="is_recurring" 
                                       value="1" 
                                       {{ old('is_recurring') ? 'checked' : '' }}>
                                <span class="ml-2">{{ __('This is a recurring session') }}</span>
                            </label>
                        </div>

                        <div id="recurringFields" class="space-y-4" style="display: none;">
                            <div>
                                <x-input-label for="recurrence_pattern" :value="__('Recurrence Pattern')" />
                                <select id="recurrence_pattern" 
                                        class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                        name="recurrence_pattern">
                                    <option value="daily" {{ old('recurrence_pattern') === 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ old('recurrence_pattern') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('recurrence_pattern') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                                <x-input-error :messages="$errors->get('recurrence_pattern')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="recurrence_end_date" :value="__('Recurrence End Date')" />
                                <x-text-input id="recurrence_end_date" 
                                             class="block mt-1 w-full" 
                                             type="date" 
                                             name="recurrence_end_date" 
                                             :value="old('recurrence_end_date')" />
                                <x-input-error :messages="$errors->get('recurrence_end_date')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-secondary-button onclick="window.location='{{ route('sessions.index') }}'" class="ml-3">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button class="ml-3">
                                {{ __('Create Session') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modeSelect = document.getElementById('mode');
        const locationField = document.getElementById('locationField');
        const isRecurring = document.querySelector('input[name="is_recurring"]');
        const recurringFields = document.getElementById('recurringFields');

        // Handle session mode change
        modeSelect.addEventListener('change', function() {
            locationField.style.display = this.value === 'offline' ? 'block' : 'none';
        });

        // Handle recurring checkbox change
        isRecurring.addEventListener('change', function() {
            recurringFields.style.display = this.checked ? 'block' : 'none';
        });

        // Set initial states
        locationField.style.display = modeSelect.value === 'offline' ? 'block' : 'none';
        recurringFields.style.display = isRecurring.checked ? 'block' : 'none';
    });
    </script>
    @endpush
</x-app-layout>
