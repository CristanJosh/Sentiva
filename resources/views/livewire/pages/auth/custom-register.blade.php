<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <div class="max-w-lg w-full bg-white rounded-lg shadow-lg p-10 relative">
        <!-- Logo Section -->
        <div class="absolute top-4 left-4">
            <img src="{{ asset('images/LOGOSENTIVA.png') }}" alt="Logo" class="h-14 w-auto">
        </div>

        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome To Sentiva</h1>

        </div>

        <!-- Icon Section -->
        <div class="grid grid-cols-3 gap-4 mb-8">
            <!-- Positive Icon -->
            <div class="flex flex-col items-center bg-blue-100 rounded-lg p-4 shadow">
                <i class="fas fa-smile text-blue-500 text-3xl"></i>
                <span class="text-blue-500 font-medium mt-2">Positive</span>
            </div>

            <!-- Neutral Icon -->
            <div class="flex flex-col items-center bg-green-100 rounded-lg p-4 shadow">
                <i class="fas fa-meh text-green-500 text-3xl"></i>
                <span class="text-green-500 font-medium mt-2">Neutral</span>
            </div>

            <!-- Negative Icon -->
            <div class="flex flex-col items-center bg-red-100 rounded-lg p-4 shadow">
                <i class="fas fa-frown text-red-500 text-3xl"></i>
                <span class="text-red-500 font-medium mt-2">Negative</span>
            </div>
        </div>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name Input -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input id="name" name="name" type="text" required
                        class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-full shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

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

            <!-- Password Input -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input id="password" name="password" type="password" required
                        class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-full shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                @error('password')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password Input -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-full shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit"
                    class="w-full bg-gray-700 hover:bg-gray-500 text-white font-medium py-2 px-4 rounded-full shadow-md transition">
                    Register
                </button>
            </div>
        </form>

        <!-- Divider -->
        <div class="my-6 border-t border-gray-300"></div>

        <!-- Login Redirect -->
        <div class="text-center">
            <p class="text-sm text-gray-600">Already have an account? 
                <a href="{{ route('login') }}" class="text-blue-500 font-medium hover:underline">
                    Log In
                </a>
            </p>
        </div>
    </div>

</body>
</html>
