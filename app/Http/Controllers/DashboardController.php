<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class DashboardController extends Controller
{   
    public function index()
{
    $user = Auth::user();
    $path = storage_path('app/data/data.json');

    // Check if the file exists
    if (!File::exists($path)) {
        return view('dashboard', [
            'totalSentiments' => $user->sentiments()->count(),
            'positiveCount' => $user->sentiments()->where('grade', 'Positive')->count(),
            'neutralCount' => $user->sentiments()->where('grade', 'Neutral')->count(),
            'negativeCount' => $user->sentiments()->where('grade', 'Negative')->count(),
            'phrases' => [
                'positive_phrases' => [],
                'negative_phrases' => [],
                'neutral_phrases' => []
            ],
            'userPhrases' => [
                'positive_phrases' => [],
                'negative_phrases' => [],
                'neutral_phrases' => []
            ]
        ])->with('error', 'Data file not found.');
    }

    // Load the JSON data
    $data = json_decode(File::get($path), true);

    // Get user-specific phrases
    $userId = 'user_' . $user->id;
    $userPhrases = $data['user_phrases'][$userId] ?? [
        'positive_phrases' => [],
        'negative_phrases' => [],
        'neutral_phrases' => []
    ];

    return view('dashboard', [
        'totalSentiments' => $user->sentiments()->count(),
        'positiveCount' => $user->sentiments()->where('grade', 'Positive')->count(),
        'neutralCount' => $user->sentiments()->where('grade', 'Neutral')->count(),
        'negativeCount' => $user->sentiments()->where('grade', 'Negative')->count(),
        'phrases' => [
            'positive_phrases' => $data['positive_phrases'] ?? [],
            'negative_phrases' => $data['negative_phrases'] ?? [],
            'neutral_phrases' => $data['neutral_phrases'] ?? []
        ],
        'userPhrases' => $userPhrases
    ]);
}


    public function addPhrase(Request $request)
    {
        $path = storage_path('app/data/data.json');

        $request->validate([
            'phrase' => 'required|string|max:255',
            'category' => 'required|in:positive_phrases,negative_phrases,neutral_phrases',
        ]);

        if (!File::exists($path)) {
            return redirect()->back()->with('error', 'Data file not found.');
        }

        $data = json_decode(File::get($path), true);
        $newPhrase = $request->input('phrase');
        $category = $request->input('category');
        $userId = 'user_' . Auth::id();

        // Ensure user-specific phrases section exists
        if (!isset($data['user_phrases'][$userId])) {
            $data['user_phrases'][$userId] = [
                'positive_phrases' => [],
                'negative_phrases' => [],
                'neutral_phrases' => []
            ];
        }

        // Avoid duplicates
        if (!in_array($newPhrase, $data['user_phrases'][$userId][$category])) {
            $data['user_phrases'][$userId][$category][] = $newPhrase;
            File::put($path, json_encode($data, JSON_PRETTY_PRINT));

            return redirect()->back()->with('success', 'Phrase added successfully!');
        }

        return redirect()->back()->with('error', 'Phrase already exists in this category.');
    }

    public function dashboard()
    {
        $path = storage_path('app/data/data.json');

        if (!File::exists($path)) {
            return view('dashboard', ['data' => [], 'error' => 'Data file not found.']);
        }

        $data = json_decode(File::get($path), true);

        return view('dashboard', ['data' => $data]);
    }
    public function deletePhrase(Request $request)
{
    $path = storage_path('app/data/data.json');

    // Validate input
    $request->validate([
        'category' => 'required|in:positive_phrases,negative_phrases,neutral_phrases',
        'phrase' => 'required|string',
    ]);

    if (!File::exists($path)) {
        return response()->json(['error' => 'Data file not found.'], 404);
    }

    $data = json_decode(File::get($path), true);
    $userId = 'user_' . Auth::id();
    $category = $request->input('category');
    $phrase = $request->input('phrase');

    // Check if the phrase exists in the user's category
    if (isset($data['user_phrases'][$userId][$category])) {
        $key = array_search($phrase, $data['user_phrases'][$userId][$category]);
        if ($key !== false) {
            // Remove the phrase
            unset($data['user_phrases'][$userId][$category][$key]);
            $data['user_phrases'][$userId][$category] = array_values($data['user_phrases'][$userId][$category]); // Re-index

            // Save the updated data back to the file
            File::put($path, json_encode($data, JSON_PRETTY_PRINT));

            return response()->json(['success' => true]);
        }
    }

    return response()->json(['error' => 'Phrase not found.'], 404);
}
}
