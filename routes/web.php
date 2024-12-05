<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SentimentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegisteredUserController; // Ensure this controller exists
use Illuminate\Support\Facades\Auth; // Import Auth facade
use App\Models\Sentiment;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\FileProcessingController;
use App\Http\Controllers\PDFController;

Route::post('/reset-password', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
        'token' => 'required',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user) use ($request) {
            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => \Illuminate\Support\Str::random(60),
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');
Route::post('/forgot-password', function (\Illuminate\Http\Request $request) {
    $request->validate(['email' => 'required|email']);

    // Attempt to send the reset link
    $status = Password::sendResetLink(
        $request->only('email')
    );

    // Check if the reset link was sent
    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->name('password.email');


Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');
    
Route::get('/custom-forgot-password', function () {
    return view('livewire.pages.auth.custom-forgot-password');
})->name('password.request');


Route::redirect('/', '/login')->name('home');
Route::get('/login', function () {
    return view('livewire.pages.auth.custom-login'); // Adjust to your login view
})->name('login');


Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::get('/sentiments/{id}/download', [PDFController::class, 'download'])->name('sentiments.download');
Route::post('/extract-text', [FileProcessingController::class, 'extractText'])->name('extract.text');


// Route for showing the custom register page
Route::get('/register', function () {
    return view('livewire.pages.auth.custom-register'); // Adjust the path as needed
})->name('register')->middleware('guest');

// Route for handling the registration submission
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.submit');


// Dashboard Routes (Protected)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/add-phrase', [DashboardController::class, 'addPhrase'])->name('add.phrase');
    Route::post('/delete-phrase', [DashboardController::class, 'deletePhrase'])->name('delete.phrase');
});

// Sentiment Analysis Routes (Protected)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/analyze', [SentimentController::class, 'analyze'])->name('analyze.sentiment');
    Route::get('/history', [SentimentController::class, 'history'])->name('sentiments.history');
    Route::get('/sentiments/{id}', function ($id) {
        $sentiment = Sentiment::findOrFail($id);
        return response()->json(['text' => $sentiment->highlighted_text]);
    })->name('sentiments.show');
    Route::delete('/sentiments/{id}', [SentimentController::class, 'destroy'])->name('sentiments.destroy');
});

// Static Pages

Route::view('/profile', 'profile')->middleware(['auth'])->name('profile');
Route::view('/analysis', 'analysis')->name('analysis');

// About and Contact Pages
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');

// Authentication Routes (Guest Only)
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', function () {
        return view('livewire.pages.auth.custom-login'); // Adjust path to your custom view
    })->name('login');

    Route::post('/login', function (\Illuminate\Http\Request $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard'); // Redirect after login
        }

        return back()->withErrors([
            'email' => 'The provided credentials are incorrect.',
        ]);
    })->name('login.submit');
}); // <-- This closing brace was missing

// Include Laravel's Auth Scaffolding
require __DIR__ . '/auth.php';
