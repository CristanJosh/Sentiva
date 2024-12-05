<?php

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state(['email' => '']);

rules(['email' => ['required', 'string', 'email']]);

$sendPasswordResetLink = function () {
    $this->validate();
    
    // We will send the password reset link to this user. Once we have attempted
    // to send the link, we will examine the response then see the message we
    // need to show to the user. Finally, we'll send out a proper response.
    $status = Password::sendResetLink(
        $this->only('email')
    );

    if ($status != Password::RESET_LINK_SENT) {
        $this->addError('email', __($status));

        return;
    }

    $this->reset('email');

    Session::flash('status', __($status));
};

?>

<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-lg w-full bg-white rounded-lg shadow-lg p-10 relative">
        <!-- Logo Section -->
        <div class="absolute top-4 left-4">
            <img src="{{ asset('images/LOGOSENTIVA.png') }}" alt="Logo" class="h-14 w-auto">
        </div>

        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Forgot Your Password?</h1>
            <p class="text-gray-600">
                No worries! Just enter your email below and we'll send you a password reset link.
            </p>
        </div>

        <!-- Forgot Password Form -->
        <form wire:submit.prevent="sendPasswordResetLink">
            <!-- Email Input -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input wire:model="email" id="email" type="email" required
                        class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-full shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                @error('email')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between mt-6">
                <button wire:loading.attr="disabled" wire:target="sendPasswordResetLink"
                    class="w-full bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-full shadow-md transition">
                    Send Password Reset Link
                </button>
            </div>

            <!-- Loading Indicator -->
            <div class="text-center mt-4" wire:loading wire:target="sendPasswordResetLink">
                <span class="text-sm text-gray-500">Sending...</span>
            </div>

            <!-- Success Message -->
            @if (session('status'))
                <div class="text-green-500 text-center mt-4">
                    {{ session('status') }}
                </div>
            @endif
        </form>

        <!-- Divider -->
        <div class="my-6 border-t border-gray-300"></div>

        <!-- Back to Login -->
        <div class="text-center">
            <p class="text-sm text-gray-600">Remember your password? 
                <a href="{{ route('login') }}" class="text-blue-500 font-medium hover:underline">
                    Log In
                </a>
            </p>
        </div>
    </div>
</div>
