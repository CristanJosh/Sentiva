<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sentiment; // Assuming this is the model for your sentiments
use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    public function download(Request $request, $id)
    {
        $sentiment = Sentiment::findOrFail($id);
    
        // Validate and sanitize the filename
        $filename = $request->query('filename', 'sentiment-analysis.pdf');
        $filename = preg_replace('/[^\w\-. ]/', '', $filename);
    
        // Generate the PDF content
        $pdf = Pdf::loadView('pdf.sentiment', compact('sentiment'));
    
        // Return the PDF as a download
        return $pdf->download($filename);
    }
    
}
