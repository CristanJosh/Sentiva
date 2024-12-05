<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;


class SentimentController extends Controller
{
    public function analyze(Request $request)
{
    $user = auth()->user(); // Ensure the user is logged in
    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Get the input text from the request
    $originalText = $request->input('text'); // Preserve original text for highlighting
    $inputText = strtolower($originalText); // Convert to lowercase for case-insensitive matching

    // Load the JSON dataset
    $datasetPath = storage_path('app/data/data.json');
    if (!File::exists($datasetPath)) {
        return response()->json(['error' => 'Dataset file not found!'], 404);
    }

    $data = json_decode(File::get($datasetPath), true);

    // Extract global words and phrases
    $positiveWords = $data['positive_words'] ?? [];
    $negativeWords = $data['negative_words'] ?? [];
    $neutralWords = $data['neutral_words'] ?? [];
    $positivePhrases = $data['positive_phrases'] ?? [];
    $negativePhrases = $data['negative_phrases'] ?? [];
    $neutralPhrases = $data['neutral_phrases'] ?? [];

    // Load user-specific phrases
    $userId = 'user_' . $user->id;
    $userPhrases = $data['user_phrases'][$userId] ?? [
        'positive_phrases' => [],
        'negative_phrases' => [],
        'neutral_phrases' => []
    ];

    // Merge user-specific phrases into the global lists
    $positivePhrases = array_merge($positivePhrases, $userPhrases['positive_phrases']);
    $negativePhrases = array_merge($negativePhrases, $userPhrases['negative_phrases']);
    $neutralPhrases = array_merge($neutralPhrases, $userPhrases['neutral_phrases']);

    // Initialize counters
    $positiveCount = 0;
    $negativeCount = 0;
    $neutralCount = 0;

    // Step 1: Highlight phrases first and replace them with placeholders
    $highlightedPhrases = []; // To track highlighted phrases
    foreach ($positivePhrases as $phrase) {
        if (stripos($inputText, $phrase) !== false) {
            $positiveCount++;
            $highlight = "<span class='highlight positive'>{$phrase}</span>";
            $highlightedPhrases["__PHRASE_" . count($highlightedPhrases) . "__"] = $highlight;
            $inputText = str_ireplace($phrase, "__PHRASE_" . (count($highlightedPhrases) - 1) . "__", $inputText);
        }
    }

    foreach ($negativePhrases as $phrase) {
        if (stripos($inputText, $phrase) !== false) {
            $negativeCount++;
            $highlight = "<span class='highlight negative'>{$phrase}</span>";
            $highlightedPhrases["__PHRASE_" . count($highlightedPhrases) . "__"] = $highlight;
            $inputText = str_ireplace($phrase, "__PHRASE_" . (count($highlightedPhrases) - 1) . "__", $inputText);
        }
    }

    foreach ($neutralPhrases as $phrase) {
        if (stripos($inputText, $phrase) !== false) {
            $neutralCount++;
            $highlight = "<span class='highlight neutral'>{$phrase}</span>";
            $highlightedPhrases["__PHRASE_" . count($highlightedPhrases) . "__"] = $highlight;
            $inputText = str_ireplace($phrase, "__PHRASE_" . (count($highlightedPhrases) - 1) . "__", $inputText);
        }
    }

    // Step 2: Highlight remaining single words
    $words = explode(' ', $inputText); // Split the text into words

    foreach ($words as &$word) {
        if (in_array($word, $positiveWords)) {
            $positiveCount++;
            $word = "<span class='highlight positive'>{$word}</span>";
        } elseif (in_array($word, $negativeWords)) {
            $negativeCount++;
            $word = "<span class='highlight negative'>{$word}</span>";
        } elseif (in_array($word, $neutralWords)) {
            $neutralCount++;
            $word = "<span class='highlight neutral'>{$word}</span>";
        }
    }

    // Step 3: Reassemble the text with placeholders replaced
    $highlightedText = implode(' ', $words);
    foreach ($highlightedPhrases as $placeholder => $highlightedPhrase) {
        $highlightedText = str_replace($placeholder, $highlightedPhrase, $highlightedText);
    }

    // Step 4: Calculate sentiment score
    $rawScore = ($positiveCount * 1) + ($negativeCount * -1); // Weighted raw score
    $totalWords = $positiveCount + $negativeCount + $neutralCount;

    // Normalize score to a range between -1 and 1
    $score = $totalWords > 0 ? $rawScore / $totalWords : 0;

    // Calculate percentages
    $positivePercentage = $totalWords > 0 ? ($positiveCount / $totalWords) * 100 : 0;
    $negativePercentage = $totalWords > 0 ? ($negativeCount / $totalWords) * 100 : 0;
    $neutralPercentage = $totalWords > 0 ? ($neutralCount / $totalWords) * 100 : 0;

    // Step 5: Determine grade based on normalized score
    $grade = 'Neutral'; // Default grade
    if ($score > 0.25) {
        $grade = 'Positive';
    } elseif ($score < -0.25) {
        $grade = 'Negative';
    }

    // Save to database
    $user->sentiments()->create([
        'text' => $originalText,
        'highlighted_text' => $highlightedText,
        'positive_count' => $positiveCount,
        'negative_count' => $negativeCount,
        'neutral_count' => $neutralCount,
        'total_word_count' => $totalWords,
        'positive_percentage' => round($positivePercentage, 2),
        'negative_percentage' => round($negativePercentage, 2),
        'neutral_percentage' => round($neutralPercentage, 2),
        'score' => round($score, 2),
        'grade' => $grade,
    ]);

    // Return response for frontend display
    return response()->json([
        'text' => $originalText,
        'highlighted_text' => $highlightedText,
        'positive_count' => $positiveCount,
        'negative_count' => $negativeCount,
        'neutral_count' => $neutralCount,
        'total_word_count' => $totalWords,
        'positive_percentage' => round($positivePercentage, 2),
        'negative_percentage' => round($negativePercentage, 2),
        'neutral_percentage' => round($neutralPercentage, 2),
        'score' => round($score, 2),
        'grade' => $grade,
    ]);
}


        
    private function highlightWords($text, $positiveWords, $positiveColor, $negativeWords, $negativeColor, $neutralWords, $neutralColor, $positivePhrases, $negativePhrases, $neutralPhrases)
    {
        // 1. Highlight phrases first to avoid splitting them into words.
        foreach ($positivePhrases as $phrase) {
            $text = preg_replace_callback('/\b' . preg_quote($phrase, '/') . '\b/i', function ($matches) use ($positiveColor) {
                return "<span class='highlight positive'>{$matches[0]}</span>";
            }, $text);
        }

        foreach ($negativePhrases as $phrase) {
            $text = preg_replace_callback('/\b' . preg_quote($phrase, '/') . '\b/i', function ($matches) use ($negativeColor) {
                return "<span class='highlight negative'>{$matches[0]}</span>";
            }, $text);
        }

        foreach ($neutralPhrases as $phrase) {
            $text = preg_replace_callback('/\b' . preg_quote($phrase, '/') . '\b/i', function ($matches) use ($neutralColor) {
                return "<span class='highlight neutral'>{$matches[0]}</span>";
            }, $text);
        }

        // 2. Remove matched phrases from the text temporarily to prevent word-level matches inside phrases.
        $textWithoutPhrases = strip_tags($text);

        // 3. Highlight single words (only for text that hasn’t been matched as a phrase).
        foreach ($positiveWords as $word) {
            $textWithoutPhrases = preg_replace_callback('/\b' . preg_quote($word, '/') . '\b/i', function ($matches) use ($positiveColor) {
                return "<span class='highlight positive'>{$matches[0]}</span>";
            }, $textWithoutPhrases);
        }

        foreach ($negativeWords as $word) {
            $textWithoutPhrases = preg_replace_callback('/\b' . preg_quote($word, '/') . '\b/i', function ($matches) use ($negativeColor) {
                return "<span class='highlight negative'>{$matches[0]}</span>";
            }, $textWithoutPhrases);
        }

        foreach ($neutralWords as $word) {
            $textWithoutPhrases = preg_replace_callback('/\b' . preg_quote($word, '/') . '\b/i', function ($matches) use ($neutralColor) {
                return "<span class='highlight neutral'>{$matches[0]}</span>";
            }, $textWithoutPhrases);
        }

        // 4. Merge the phrase-highlighted text with word-highlighted text.
        $finalText = $textWithoutPhrases;

        return $finalText;
}

    public function history(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $filter = $request->query('filter', 'all');
        $search = $request->query('search', '');

        $query = $user->sentiments();

        if ($filter !== 'all') {
            $query->where('grade', $filter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('text', 'like', "%{$search}%")
                    ->orWhere('highlighted_text', 'like', "%{$search}%");
            });
        }

        $sentiments = $query->orderBy('created_at', 'desc')->paginate(9);

        if ($request->ajax()) {
            return view('partials.sentiments', compact('sentiments'))->render();
        }
        

        return view('sentiments.history', compact('sentiments', 'filter', 'search'));
    }



        public function show($id)
    {
        // Retrieve the sentiment record by its ID
        $sentiment = \App\Models\Sentiment::findOrFail($id);

        // Return the highlighted text as JSON for the frontend
        return response()->json(['text' => $sentiment->highlighted_text]);
    }

     public function destroy($id)
    {
        $user = auth()->user();

        // Find the sentiment by ID and ensure it belongs to the authenticated user
        $sentiment = $user->sentiments()->findOrFail($id);

        // Soft delete the sentiment
        $sentiment->delete();

        return response()->json(['message' => 'Sentiment deleted successfully.']);
    }

    




}