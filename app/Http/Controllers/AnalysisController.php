<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function index()
    {
        // Return the analysis view
        return view('analysis');
    }
}
