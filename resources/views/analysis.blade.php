
<link href="{{ mix('css/app.css') }}" rel="stylesheet">
<link href="{{ asset('css/sentiment_analysis.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="analysis-page">
@livewireStyles
@livewireScripts

    <script src="//unpkg.com/alpinejs" defer></script>
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Analyze Text') }}
            </h2>
        </x-slot>
   
    <div class="container">
    <div class="flex justify-center">
    <form id="sentiment-form" class="w-full max-w-lg">
        @csrf
        <div class="mb-6">
        <div style="text-align: center; font-weight: bold; margin-bottom: 1.5rem;">
    <!-- Heading -->
    <h2 >
        <i class="fas fa-search"></i> Analyze Sentiment Text Here
    </h2>

    <!-- Divider with "or" -->
    <div class="flex items-center justify-center my-4">
        <div class="border-t border-gray-300 flex-grow"></div>
        <span class="mx-4 text-gray-500 text-sm">or</span>
        <div class="border-t border-gray-300 flex-grow"></div>
    </div>

        <!-- Drag-and-Drop Section -->
        <div class="flex items-center justify-center">
        <i class="fas fa-file-alt text-lg mr-2"></i>
        <h2>Drag and drop a PDF or DOCX file here</h2>
        </div>
    </div>
            <div class="flex items-center space-x-4">             
                    <textarea 
                    id="text" 
                    name="text" 
                    rows="6" 
                    class="mt-4 block w-full px-4 py-4 border border-gray-300 rounded-3xl shadow-sm focus:ring-gray-500 focus:border-gray-500 sm:text-lg bg-gray-100 text-gray-800 placeholder-gray-400 resize-none relative"
                    placeholder="Type here, or drag and drop a file..."></textarea>
 
                        <div 
                            id="drop-area" 
                            class="absolute top-0 left-0 w-full h-full border-2 border-dashed border-gray-300 rounded-3xl bg-gray-50 text-gray-500 flex items-center justify-center hidden"
                            ondragover="event.preventDefault()"
                            ondrop="handleFileDrop(event)">
                            <span>Drop your file here</span>
                        </div>
                            
                    
                </div>
                            <input 
                            type="file" 
                            id="file-upload" 
                            accept=".pdf,.docx" 
                            class="hidden" />

             </div>
                <span id="text-error" class="text-red-500 text-xs mt-2" style="display: none;"></span>

                <div class="flex justify-between space-x-4 mt-4">
                              <!-- Mic Button -->
                        <button 
                            type="button" 
                            id="speak-button" 
                            class="flex items-center justify-center h-12 w-12 bg-gray-600 text-white rounded-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-300 ease-in-out transform hover:scale-105">
                            <i class="fas fa-microphone"></i>
                        </button>
                        <button 
                            type="button" 
                            id="stop-button" 
                            class="flex items-center justify-center h-12 w-12 bg-red-500 text-white rounded-full shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-300 ease-in-out transform hover:scale-105">
                            <i class="fas fa-stop"></i>
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-3 bg-gray-500 text-white rounded-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-300 ease-in-out transform hover:scale-105">
                            Analyze Sentiment
                        </button>
                    </div>
                </div>

        <div id="result" class="mt-8 bg-white p-8 rounded-3xl shadow-lg hidden">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Analysis Result</h2>
            <p id="input-text" class="text-gray-700 mb-6 font-medium text-center"></p>

            <h3 class="text-lg font-semibold">Sentiment Score</h3>
                    <div class="score-container">
                        <div class="score-bar">
                            <div id="score-indicator" class="score-indicator"></div>
                        </div>
                    <div class="score-labels flex justify-between text-sm mt-2">
                        <span class="text-red-500">Negative</span>
                        <span class="text-gray-500">Neutral</span>
                        <span class="text-blue-500">Positive</span>
                    </div>

        <!-- Sentiment Metrics -->
        <div id="metrics" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Positive Words -->
                <div class="bg-blue-100 shadow-lg rounded-3xl flex flex-col items-center p-6">
                    <div class="text-blue-600 text-3xl">
                        <i class="fas fa-smile"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-blue-600 mt-2">Positive Words</h3>
                    <p id="positive-count-value" class="text-4xl font-bold text-blue-600 mt-2"></p>
                </div>

                <!-- Negative Words -->
                <div class="bg-red-100 shadow-lg rounded-3xl flex flex-col items-center p-6">
                    <div class="text-red-600 text-3xl">
                        <i class="fas fa-frown"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-red-600 mt-2">Negative Words</h3>
                    <p id="negative-count-value" class="text-4xl font-bold text-red-600 mt-2"></p>
                </div>

                <!-- Neutral Words -->
                <div class="bg-green-100 shadow-lg rounded-3xl flex flex-col items-center p-6">
                    <div class="text-green-600 text-3xl">
                        <i class="fas fa-meh"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-green-600 mt-2">Neutral Words</h3>
                    <p id="neutral-count-value" class="text-4xl font-bold text-green-600 mt-2"></p>
                </div>

                <!-- Total Words -->
                <div class="bg-gray-100 shadow-lg rounded-3xl flex flex-col items-center p-6">
                    <div class="text-gray-600 text-3xl">
                        <i class="fas fa-align-left"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-600 mt-2">Sentiment Phrases</h3>
                    <p id="total-word-count-value" class="text-4xl font-bold text-gray-600 mt-2"></p>
                </div>
            </div>

                <!-- Sentiment Grade -->
        <div id="sentiment-grade-container" class="mt-8 shadow-lg rounded-3xl flex flex-col items-center p-6">
            <h3 id="sentiment-grade-title" class="text-lg font-semibold mt-2">Sentiment Grade</h3>
            <p id="grade-value" class="text-4xl font-bold mt-2"></p>
        </div>


            <!-- Sentiment Percentages -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Positive Percentage -->
                <div class="bg-blue-100 shadow-lg rounded-3xl flex flex-col items-center p-6">
                    <div class="text-blue-600 text-3xl">
                        <i class="fas fa-smile"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-blue-600 mt-2">Positive Percentage</h3>
                    <p id="positive-percentage-value" class="text-4xl font-bold text-blue-600 mt-2"></p>
                </div>

                <!-- Negative Percentage -->
                <div class="bg-red-100 shadow-lg rounded-3xl flex flex-col items-center p-6">
                    <div class="text-red-600 text-3xl">
                        <i class="fas fa-frown"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-red-600 mt-2">Negative Percentage</h3>
                    <p id="negative-percentage-value" class="text-4xl font-bold text-red-600 mt-2"></p>
                </div>

                <!-- Neutral Percentage -->
                <div class="bg-green-100 shadow-lg rounded-3xl flex flex-col items-center p-6">
                    <div class="text-green-600 text-3xl">
                        <i class="fas fa-meh"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-green-600 mt-2">Neutral Percentage</h3>
                    <p id="neutral-percentage-value" class="text-4xl font-bold text-green-600 mt-2"></p>
                </div>
            </div>
        </div>

    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#sentiment-form').off('submit').on('submit', function (e) {
            e.preventDefault();
            var text = $('#text').val();

            if (!text) {
            $('#text-error').text('Please enter some text before analyzing.').show();
            return; // Stop execution if the input is empty
        }

            // Clear previous error and result
            $('#text-error').hide();
            $('#result').hide();

            // AJAX request
            $.ajax({
        url: '{{ route("analyze.sentiment") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            text: text
        },
                            success: function (response) {
                        // Use highlighted text from the backend for display
                        $('#input-text').html( response.highlighted_text);

                        // Populate metrics
                        $('#positive-count-value').text(response.positive_count);
                        $('#negative-count-value').text(response.negative_count);
                        $('#neutral-count-value').text(response.neutral_count);
                        $('#total-word-count-value').text(response.total_word_count);
                        $('#score-value').text(response.score);
                        $('#magnitude-value').text(response.magnitude);

                        // Update the sentiment grade and color dynamically
                        updateSentimentGrade(response);

                        // Populate percentages
                        $('#positive-percentage-value').text(response.positive_percentage + '%');
                        $('#negative-percentage-value').text(response.negative_percentage + '%');
                        $('#neutral-percentage-value').text(response.neutral_percentage + '%');

                        // Update the bar position
                        var leftPercentage = ((response.score + 1) / 2) * 100; // Normalize to 0-100%
                        $('#score-indicator').css('left', leftPercentage + '%');

                        // Display the result
                        $('#result').fadeIn();
                    },
            error: function () {
                alert('Something went wrong. Please try again.');
            }
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const speakButton = document.getElementById('speak-button');
    const stopButton = document.getElementById('stop-button');
    const textInput = document.getElementById('text');
    let recognition;

    // Check for browser support
    if (!('webkitSpeechRecognition' in window || 'SpeechRecognition' in window)) {
        alert('Sorry, your browser does not support Speech Recognition.');
        speakButton.disabled = true;
        stopButton.disabled = true;
        return;
    }

    // Initialize Speech Recognition
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();

    recognition.lang = 'en-US'; // Set language
    recognition.interimResults = false; // Get final results only
    recognition.maxAlternatives = 1; // Single most likely result
    recognition.continuous = true; // Enable continuous recognition

    // Start speech recognition
    speakButton.addEventListener('click', () => {
        recognition.start();
    });

    // Handle speech recognition start
    recognition.onstart = () => {
        speakButton.disabled = true;
        stopButton.disabled = false;
        speakButton.classList.add('glow'); // Add glowing effect
        speakButton.innerHTML = '<i class="fas fa-microphone-slash"></i>'; // Change to a slash icon
    };

    // Handle speech recognition results
    recognition.onresult = (event) => {
        const transcript = event.results[event.results.length - 1][0].transcript; // Get the latest result
        textInput.value += transcript + ' '; // Append recognized text to the input
        adjustTextareaHeight(textInput); // Adjust the textarea height dynamically
    };

    // Handle speech recognition errors
    recognition.onerror = (event) => {
        alert(`Error occurred: ${event.error}`);
    };

    // Prevent stopping automatically
    recognition.onend = () => {
        // Do nothing if the stop button hasn't been pressed
        if (speakButton.disabled) {
            recognition.start(); // Restart if not explicitly stopped
        }
    };

    // Stop speech recognition when stop button is pressed
    stopButton.addEventListener('click', () => {
        recognition.stop();
        speakButton.disabled = false;
        stopButton.disabled = true;
        speakButton.classList.remove('glow'); // Remove glowing effect
        speakButton.innerHTML = '<i class="fas fa-microphone"></i>'; // Set the icon as HTML
    });

    // Adjust textarea height dynamically
    function adjustTextareaHeight(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }
});

    

    
    document.addEventListener('DOMContentLoaded', () => {
    const dropArea = document.getElementById('drop-area');
    const textArea = document.getElementById('text');
    const fileInput = document.getElementById('file-upload');

    let dragCounter = 0; // To track drag entries and prevent premature hiding

    // Show the drag-and-drop overlay when dragging over the textarea
    textArea.addEventListener('dragenter', (event) => {
        event.preventDefault();
        dragCounter++;
        dropArea.style.display = 'flex'; // Show overlay
    });

    // Increment drag counter when dragging over the drop area
    dropArea.addEventListener('dragenter', (event) => {
        event.preventDefault();
        dragCounter++;
    });

    // Hide the drag-and-drop overlay when leaving the textarea or drop area
    textArea.addEventListener('dragleave', () => {
        dragCounter--;
        if (dragCounter === 0) {
            dropArea.style.display = 'none'; // Hide overlay
        }
    });

    dropArea.addEventListener('dragleave', () => {
        dragCounter--;
        if (dragCounter === 0) {
            dropArea.style.display = 'none'; // Hide overlay
        }
    });

    // Prevent default behavior for dragover
    dropArea.addEventListener('dragover', (event) => {
        event.preventDefault();
    });

    // Handle file drop
    dropArea.addEventListener('drop', (event) => {
        event.preventDefault();
        dragCounter = 0; // Reset counter
        dropArea.style.display = 'none'; // Hide overlay
        const file = event.dataTransfer.files[0];
        if (file) {
            handleFileUpload(file);
        }
    });

    // Handle manual file selection
    fileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            handleFileUpload(file);
        }
    });

    // Function to upload and process file
    async function handleFileUpload(file) {
        const formData = new FormData();
        formData.append('file', file);

        try {
            const response = await fetch('{{ route('extract.text') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData,
            });

            const result = await response.json();

            if (result.success) {
                textArea.value = result.text;
            } else {
                alert(result.error || 'Failed to process the file.');
            }
        } catch (error) {
            console.error('Error processing file:', error);
            alert('An error occurred while processing the file.');
        }
    }
});


function updateSentimentGrade(response) {
    const gradeContainer = document.getElementById('sentiment-grade-container');
    const gradeTitle = document.getElementById('sentiment-grade-title');
    const gradeValue = document.getElementById('grade-value');

    // Remove existing sentiment classes
    gradeContainer.classList.remove('bg-green-100', 'bg-red-100', 'bg-blue-100');
    gradeTitle.classList.remove('text-green-600', 'text-red-600', 'text-blue-600');
    gradeValue.classList.remove('text-green-600', 'text-red-600', 'text-blue-600');

    // Determine sentiment and apply styles
    if (response.score > 0.5) {
        // Positive sentiment
        gradeContainer.classList.add('bg-blue-100');
        gradeTitle.classList.add('text-blue-600');
        gradeValue.classList.add('text-blue-600');
    } else if (response.score < -0.5) {
        // Negative sentiment
        gradeContainer.classList.add('bg-red-100');
        gradeTitle.classList.add('text-red-600');
        gradeValue.classList.add('text-red-600');
    } else {
        // Neutral sentiment
        gradeContainer.classList.add('bg-green-100');
        gradeTitle.classList.add('text-green-600');
        gradeValue.classList.add('text-green-600');
    }

    // Update the grade value
    gradeValue.textContent = response.grade;
}


</script>
<style>
#drop-area {
    background: rgba(255, 255, 255, 0.9); /* Semi-transparent white background */
    border: 2px dashed #3b82f6; /* Tailwind blue-500 color */
    color: #3b82f6; /* Tailwind blue-500 color */
    font-size: 1.25rem;
    transition: opacity 0.3s ease; /* Smooth fade-in/fade-out */
}

    </style>
</x-app-layout>
</div>