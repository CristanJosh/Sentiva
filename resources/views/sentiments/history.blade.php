<link href="{{ mix('css/app.css') }}" rel="stylesheet">
<link href="{{ asset('css/sentiment_analysis.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
@livewireStyles
@livewireScripts
<script src="//unpkg.com/alpinejs" defer></script>
<script src="{{ mix('js/app.js') }}" defer></script>
<script src="https://cdn.tailwindcss.com"></script>
<link href="{{ mix('css/app.css') }}" rel="stylesheet">
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

        
<style>
/* Sentiment Analysis Styles */
.positive-text {
    color: rgb(20, 78, 185);
    font-weight: bold;
}

.negative-text {
    color: red;
    font-weight: bold;
}

#sentiment-result {
    font-size: 1.2rem;
    font-weight: 600;
}

#positive-count {
    color: blue;
}

#negative-count {
    color: red;
}
.positive-text {
    color: green;
}

.negative-text {
    color: red;
}
/* General container styles */
.container {
    max-width: 100%;
    margin: 0 auto;
    padding: 2rem;
    background-color: transparent; /* Transparent background */
}


/* Text input container styling */
#sentiment-form {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-bottom: 2rem;
}

/* Sentiment analysis result container styling */
#result {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-bottom: 2rem;
    display: none; /* Hide by default */
}

/* Update button styling */
button {
    background-color: #2563eb;
    padding: 1rem 2rem;
    color: white;
    border-radius: 12px;
    transition: all 0.3s ease;
    width: 100%;
    text-align: center;
}

button:hover {
    background-color: #1d4ed8;
    transform: scale(1.05);
}

button:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

/* Result count section */
#word-count {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

#positive-count, #negative-count {
    border-radius: 12px;
    padding: 1.5rem;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

#positive-count {
    background-color: #2563eb;
    color: white;
}

#negative-count {
    background-color: #f87171;
    color: white;
}

#sentiment-result {
    font-size: 1.5rem;
    font-weight: bold;
    margin-top: 1.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }

    #sentiment-form, #result {
        padding: 1.5rem;
    }

    button {
        font-size: 1rem;
    }
}
.highlight {
    font-weight: bold;
    font-size: 1.2em;
}

.positive {
    color: blue;
}

.negative {
    color: red;
}

.neutral {
    color: green;
}
.highlight {
    font-weight: bold;
    font-size: 1.1em;
}

.positive {
    color: blue; /* You can change this color */
}

.negative {
    color: red;  /* You can change this color */
}

.neutral {
    color: green; /* You can change this color */
}
.highlight.positive {
    color: blue;
    font-weight: bold;
}

.highlight.negative {
    color: red;
    font-weight: bold;
}

.highlight.neutral {
    color: green;
    font-weight: bold;
}

.score-container {
    width: 100%;
    margin-top: 10px;
}

.score-bar {
    position: relative;
    height: 12px;
    background: linear-gradient(to right, red, gray, blue); /* Positive is now blue */
    border-radius: 6px;
    overflow: hidden;
}


.score-indicator {
    position: absolute;
    height: 100%;
    width: 2px;
    background-color: black;
    transition: left 0.3s ease;
}
.pill {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-radius: 25px;
    font-size: 1rem; /* Default font size */
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease-in-out;
}

/* Media query for mobile devices */
@media screen and (max-width: 768px) {
    .pill {
        font-size: 0.75rem; /* Smaller font size for mobile */
        padding: 0.75rem 1rem; /* Adjust padding for mobile */
    }
}



.pill span:last-child {
    font-size: 1.25rem;
    font-weight: 700; /* Make the count bold */
}
#full-text-modal {
    position: fixed;
    inset: 0; /* Full screen */
    background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 50;
    pointer-events: none; /* Disable clicks when hidden */
    opacity: 0;
    transition: opacity 0.3s ease;
}

#full-text-modal.show {
    pointer-events: all;
    opacity: 1;
}

/* Modal content container */
#full-text-modal .modal-content {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
    max-width: 90%; /* Restrict width for responsiveness */
    max-height: 80%; /* Restrict height to allow scrolling */
    overflow-y: auto; /* Enable vertical scrolling */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.glow {
    box-shadow: 0 0 20px 5px rgba(59, 130, 246, 0.7), 0 0 30px 10px rgba(59, 130, 246, 0.5);
    animation: glowing 1.5s infinite alternate;
}

@keyframes glowing {
    from {
        box-shadow: 0 0 10px 3px rgba(59, 130, 246, 0.6);
    }
    to {
        box-shadow: 0 0 20px 6px rgba(59, 130, 246, 0.8);
    }
}

#drop-area {
    transition: background-color 0.3s ease;
}

#drop-area:hover {
    background-color: #f3f4f6; /* Tailwind's gray-100 */
}

textarea {
    transition: border-color 0.3s ease;
}

textarea:focus {
    border-color: #3b82f6; /* Tailwind's blue-500 */
    outline: none;
}
.dark\:bg-black {
        /* --tw-bg-opacity: 1; */
        background-color: rgb(0 0 0 / var(--tw-bg-opacity, 1)) !important;
    }

    .shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, .120) !important;
}
    </style>
</x-app-layout>
