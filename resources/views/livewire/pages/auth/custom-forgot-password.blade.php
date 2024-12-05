<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Add FontAwesome -->
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-lg w-full bg-white rounded-lg shadow-lg p-10 relative">
        <!-- Logo Section -->
        <div class="absolute top-4 left-4">
            <img src="{{ asset('images/LOGOSENTIVA.png') }}" alt="Logo" class="h-12 w-auto">
        </div>

        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Forgot Your Password?</h1>
            <p class="text-gray-600">
                No worries! Just enter your email below and we'll send you a password reset link.
            </p>
        </div>

        <!-- Forgot Password Form -->
        @if (session('status'))
            <!-- Success Message -->
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <p>{{ session('status') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Input -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input id="email" name="email" type="email" required
                        class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-full shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                @error('email')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between mt-6">
                <button type="submit"
                    class="w-full bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-full shadow-md transition">
                    Send Password Reset Link
                </button>
            </div>
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
</body>
</html>
