<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sentiement History') }}
        </h2>
    </x-slot>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">Contact Us</h1>
        <p class="mt-4 text-lg">Feel free to reach out to us via email or phone. We'd love to hear from you!</p>
        <!-- Example Contact Form -->
        <form action="#" method="POST">
            @csrf
            <div class="mt-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Your Name</label>
                <input type="text" id="name" name="name" class="mt-1 block w-full rounded-md shadow-sm" required>
            </div>

            <div class="mt-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Your Email</label>
                <input type="email" id="email" name="email" class="mt-1 block w-full rounded-md shadow-sm" required>
            </div>

            <div class="mt-4">
                <label for="message" class="block text-sm font-medium text-gray-700">Your Message</label>
                <textarea id="message" name="message" rows="4" class="mt-1 block w-full rounded-md shadow-sm" required></textarea>
            </div>

            <div class="mt-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Send Message</button>
            </div>
        </form>
    </div>
    </x-app-layout>