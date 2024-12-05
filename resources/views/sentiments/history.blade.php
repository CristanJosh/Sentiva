<link href="{{ mix('css/app.css') }}" rel="stylesheet">
<link href="{{ asset('css/sentiment_analysis.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
@livewireStyles
@livewireScripts
<script src="//unpkg.com/alpinejs" defer></script>
<script src="{{ mix('js/app.js') }}" defer></script>
<div class="history-page">
<x-app-layout>
    <x-slot name="header">
    <div class="flex justify-between items-center mb-6">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex-1">
                    {{ __('Sentiment History') }}
                </h2>
              
                <div class="relative mr-4 w-1/3">
    <form method="GET" action="{{ route('sentiments.history') }}" class="relative flex">
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-500">
            <i class="fas fa-search"></i>
        </span>
        <input 
            type="text" 
            name="search"
            id="search-bar" 
            placeholder="Search by keyword..." 
            value="{{ request('search') }}" 
            class="pl-10 px-4 py-2 w-full border border-gray-300 bg-white text-black placeholder-gray-400 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-500 transition ease-in-out duration-150"
        />
        <input type="hidden" name="filter" value="{{ request('filter', 'all') }}">
        <button 
            type="submit" 
            class="ml-2 px-4 py-2 bg-white text-black border border-gray-300 rounded-full shadow-sm hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 transition ease-in-out duration-150">
            Search
        </button>
    </form>

    <button 
        id="dropdown-button" 
        class="w-full px-4 py-2 bg-white text-black border border-gray-300 rounded-full shadow-sm hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 transition ease-in-out duration-150">
        Filter
    </button>
    <div 
        id="dropdown-menu" 
        class="absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-lg shadow-lg hidden z-10">
        <button class="dropdown-item w-full text-left px-4 py-2 text-black hover:bg-gray-100 hover:text-gray-800 transition ease-in-out duration-150" data-filter="all">
            All
        </button>
        <button class="dropdown-item w-full text-left px-4 py-2 text-black hover:bg-gray-100 hover:text-gray-800 transition ease-in-out duration-150" data-filter="Positive">
            Positive
        </button>
        <button class="dropdown-item w-full text-left px-4 py-2 text-black hover:bg-gray-100 hover:text-gray-800 transition ease-in-out duration-150" data-filter="Neutral">
            Neutral
        </button>
        <button class="dropdown-item w-full text-left px-4 py-2 text-black hover:bg-gray-100 hover:text-gray-800 transition ease-in-out duration-150" data-filter="Negative">
            Negative
        </button>
    </div>
</div>

    </x-slot>

    <div class="container">
        @if($sentiments->isEmpty())
            <div class="text-center text-gray-500">
                <p>No sentiment analyses have been performed yet.</p>
            </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @include('partials.sentiments', ['sentiments' => $sentiments])
                </div>
        @endif



<!-- Modal for Full Text -->
        <div id="full-text-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center">
            <div class="modal-content">
                <h2 class="text-xl font-semibold mb-4">Full Analysis Text</h2>
                <p id="full-text-content" class="text-gray-700"></p>
                <div class="mt-4 text-right">
                    <button id="close-modal" class="bg-red-500 text-white px-4 py-2 rounded">Close</button>
                </div>
            </div>
        </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('full-text-modal');
    const modalContent = document.getElementById('full-text-content');
    const closeModal = document.getElementById('close-modal');

    document.querySelectorAll('.read-more').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const sentimentId = this.dataset.id;

            // Fetch the full text using AJAX
            fetch(`/sentiments/${sentimentId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch sentiment text');
                    }
                    return response.json();
                })
                .then(data => {
                    modalContent.innerHTML = data.text;
                    modal.classList.remove('hidden');
                    modal.classList.add('show'); // Trigger animation
                })
                .catch(error => {
                    console.error(error);
                    alert('Failed to load full text.');
                });
        });
    });

    closeModal.addEventListener('click', () => {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.classList.add('hidden'); // Ensure it's completely hidden after animation
        }, 300); // Match the duration of the CSS animation
    });

    // Close the modal when clicking outside the modal content
    modal.addEventListener('click', (e) => {
    if (e.target === modal) {
        e.stopPropagation(); // Stop the event from bubbling
        closeModal.click();
    }
});

});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-sentiment').forEach(button => {
        button.addEventListener('click', function () {
            const sentimentId = this.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this sentiment?')) {
                fetch(`/sentiments/${sentimentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to delete sentiment');
                    }
                    return response.json();
                })
                .then(data => {
                    alert(data.message);
                    // Remove the sentiment card from the DOM
                    const card = document.querySelector(`.delete-sentiment[data-id="${sentimentId}"]`).closest('.sentiment-card');
                    if (card) {
                        card.remove();
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('An error occurred while deleting the sentiment.');
                });
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const dropdownButton = document.getElementById('dropdown-button');
    const dropdownMenu = document.getElementById('dropdown-menu');
    const sentimentCards = document.querySelectorAll('.sentiment-card');

    // Toggle dropdown menu
    dropdownButton.addEventListener('click', () => {
        dropdownMenu.classList.toggle('hidden');
    });

    // Handle filter logic
    dropdownMenu.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', () => {
            const filter = item.getAttribute('data-filter');

            sentimentCards.forEach(card => {
                if (filter === 'all' || card.getAttribute('data-grade') === filter) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });

            // Close dropdown after selection
            dropdownMenu.classList.add('hidden');
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!dropdownMenu.contains(e.target) && e.target !== dropdownButton) {
            dropdownMenu.classList.add('hidden');
        }
    });
});


</script>
<script>
function fetchSentiments(query = '') {
    const container = document.querySelector('.container');

    fetch(`/sentiments?search=${encodeURIComponent(query)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
    .then(response => response.text())
    .then(html => {
        container.innerHTML = html; // Replace the content with the new HTML
        initEventListeners(); // Reinitialize event listeners
    })
    .catch(error => console.error('Error fetching sentiments:', error));
}
function initEventListeners() {
    // Reinitialize delete buttons
    document.querySelectorAll('.delete-sentiment').forEach(button => {
        button.addEventListener('click', function () {
            const sentimentId = this.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this sentiment?')) {
                fetch(`/sentiments/${sentimentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to delete sentiment');
                    }
                    return response.json();
                })
                .then(data => {
                    alert(data.message);
                    const card = document.querySelector(`.delete-sentiment[data-id="${sentimentId}"]`).closest('.sentiment-card');
                    if (card) {
                        card.remove();
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('An error occurred while deleting the sentiment.');
                });
            }
        });
    });

    // Reinitialize filter functionality
    const dropdownMenu = document.getElementById('dropdown-menu');
    const dropdownButton = document.getElementById('dropdown-button');
    const sentimentCards = document.querySelectorAll('.sentiment-card');

    dropdownButton.addEventListener('click', () => {
        dropdownMenu.classList.toggle('hidden');
    });

    dropdownMenu.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', () => {
            const filter = item.getAttribute('data-filter');
            sentimentCards.forEach(card => {
                if (filter === 'all' || card.getAttribute('data-grade') === filter) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
            dropdownMenu.classList.add('hidden');
        });
    });
}



    document.addEventListener('DOMContentLoaded', () => {
        const searchBar = document.getElementById('search-bar');
        const sentimentCards = document.querySelectorAll('.sentiment-card');

        searchBar.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();

            sentimentCards.forEach(card => {
                const text = card.querySelector('p').textContent.toLowerCase();
                const grade = card.getAttribute('data-grade').toLowerCase();

                if (text.includes(query) || grade.includes(query)) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    });
</script>

<div style="margin-top: 1.5rem; margin-bottom: 20px;">
    {{ $sentiments->links() }}
</div>

</div>
</x-app-layout>
