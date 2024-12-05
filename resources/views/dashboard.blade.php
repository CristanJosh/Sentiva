<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
@livewireStyles
@livewireScripts
<script src="//unpkg.com/alpinejs" defer></script>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Sentiments -->
        <div class="bg-white shadow-lg rounded-3xl flex flex-col items-center p-6">
            <div class="text-gray-800 text-3xl mb-2">
                <i class="fas fa-chart-bar"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Total Sentiments</h3>
            <p class="text-4xl font-bold text-gray-900">{{ $totalSentiments }}</p>
        </div>

        <!-- Positive Sentiments -->
        <div class="bg-blue-200 shadow-lg rounded-3xl flex flex-col items-center p-6">
            <div class="text-blue-700 text-3xl mb-2">
                <i class="fas fa-smile-beam"></i>
            </div>
            <h3 class="text-lg font-semibold text-blue-700">Positive Sentiments</h3>
            <p class="text-4xl font-bold text-blue-700">{{ $positiveCount }}</p>
        </div>

        <!-- Neutral Sentiments -->
        <div class="bg-green-200 shadow-lg rounded-3xl flex flex-col items-center p-6">
            <div class="text-green-700 text-3xl mb-2">
                <i class="fas fa-meh"></i>
            </div>
            <h3 class="text-lg font-semibold text-green-700">Neutral Sentiments</h3>
            <p class="text-4xl font-bold text-green-700">{{ $neutralCount }}</p>
        </div>

        <!-- Negative Sentiments -->
        <div class="bg-red-200 shadow-lg rounded-3xl flex flex-col items-center p-6">
            <div class="text-red-700 text-3xl mb-2">
                <i class="fas fa-frown"></i>
            </div>
            <h3 class="text-lg font-semibold text-red-700">Negative Sentiments</h3>
            <p class="text-4xl font-bold text-red-700">{{ $negativeCount }}</p>
                </div>
            </div>

    <div class="mt-12 bg-white shadow-lg rounded-3xl mx-6 lg:mx-12 p-8">
        <h3 class="text-xl font-extrabold text-gray-700 mb-6">Add a New Phrase</h3>
        <form method="POST" action="{{ route('add.phrase') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Phrase Input -->
                <div>
                    <label for="phrase" class="block text-sm font-medium text-blue-800 mb-2">Phrase</label>
                    <input 
                        type="text" 
                        name="phrase" 
                        id="phrase" 
                        placeholder="Enter a phrase..." 
                        class="w-full px-4 py-3 border border-grey-300 rounded-full shadow-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-700 "
                        required>
                </div>
                
                <!-- Category Select -->
                <div>
                    <label for="category" class="block text-sm font-medium text-blue-800 mb-2">Category</label>
                    <select 
                        name="category" 
                        id="category" 
                        class="w-full px-4 py-3 border border-grey-300 rounded-full shadow-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-700 "
                        required>
                        <option value="" disabled selected class="text-gray-400">Select Category</option>
                        <option value="positive_phrases">Positive</option>
                        <option value="negative_phrases">Negative</option>
                        <option value="neutral_phrases">Neutral</option>
                    </select>
                </div>
                
                <!-- Submit Button -->
                <div class="flex items-end">
                    <button 
                        type="submit" 
                        class="w-full px-4 py-3 bg-gray-600 text-white font-bold rounded-full shadow-md hover:bg-gray-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        Add Phrase
                    </button>
                </div>
            </div>
        </form>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mt-6 px-4 py-3 bg-green-100 text-green-800 rounded-md border border-green-200 shadow-sm">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <div class="mt-6 px-4 py-3 bg-red-100 text-red-800 rounded-md border border-red-200 shadow-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif
    </div>


        <div class="mt-12 bg-white shadow-lg rounded-3xl mx-6 lg:mx-12 p-8">
        <h3 class="text-xl font-extrabold text-gray-700 mb-6">User-Specific Phrases</h3>

        <!-- Positive Phrases -->
        <div class="mb-6">
            <h4 class="text-lg font-semibold text-blue-700 mb-2">Positive Phrases</h4>
            <ul class="list-disc list-inside bg-blue-50 p-4 rounded-lg">
                @forelse ($userPhrases['positive_phrases'] as $phrase)
                    <li class="flex justify-between items-center text-blue-800 mb-2">
                        <span>{{ $phrase }}</span>
                        <button 
                            class="w-6 h-6 flex items-center justify-center rounded-full"
                            onclick="deletePhrase('positive_phrases', '{{ $phrase }}')">
                            &times;
                        </button>
                    </li>
                @empty
                    <li class="text-gray-500">No positive phrases added yet.</li>
                @endforelse
            </ul>
        </div>

        <!-- Negative Phrases -->
        <div class="mb-6">
            <h4 class="text-lg font-semibold text-red-700 mb-2">Negative Phrases</h4>
            <ul class="list-disc list-inside bg-red-50 p-4 rounded-lg">
                @forelse ($userPhrases['negative_phrases'] as $phrase)
                    <li class="flex justify-between items-center text-red-800 mb-2">
                        <span>{{ $phrase }}</span>
                        <button 
                            class="w-6 h-6 flex items-center justify-center rounded-full"
                            onclick="deletePhrase('negative_phrases', '{{ $phrase }}')">
                            &times;
                        </button>
                    </li>
                @empty
                    <li class="text-gray-500">No negative phrases added yet.</li>
                @endforelse
            </ul>
        </div>

        <!-- Neutral Phrases -->
        <div>
            <h4 class="text-lg font-semibold text-green-700 mb-2">Neutral Phrases</h4>
            <ul class="list-disc list-inside bg-green-50 p-4 rounded-lg">
                @forelse ($userPhrases['neutral_phrases'] as $phrase)
                    <li class="flex justify-between items-center text-green-800 mb-2">
                        <span>{{ $phrase }}</span>
                        <button 
                            class="w-6 h-6 flex items-center justify-center rounded-full"
                            onclick="deletePhrase('neutral_phrases', '{{ $phrase }}')">
                            &times;
                        </button>
                    </li>
                @empty
                    <li class="text-gray-500">No neutral phrases added yet.</li>
                @endforelse
            </ul>
        </div>
</div>

<script>
    function deletePhrase(category, phrase) {
        if (!confirm('Are you sure you want to delete this phrase?')) {
            return;
        }

        fetch('{{ route('delete.phrase') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ category: category, phrase: phrase })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Refresh the page to reflect changes
            } else {
                alert(data.error || 'Failed to delete phrase. Try again.');
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>


</x-app-layout>
