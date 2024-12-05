<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser as PDFParser;
use PhpOffice\PhpWord\IOFactory as WordReader;

class FileProcessingController extends Controller
{
    public function extractText(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,docx|max:10240', // 10 MB limit for larger files
        ]);

        $file = $request->file('file');
        $filePath = $file->getPathname();
        $fileExtension = $file->getClientOriginalExtension();

        try {
            $text = '';

            if ($fileExtension === 'pdf') {
                $text = $this->extractTextFromPDF($filePath);
            } elseif ($fileExtension === 'docx') {
                $text = $this->extractTextFromDOCX($filePath);
            }

            return response()->json([
                'success' => true,
                'text' => $text,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to process the file: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function extractTextFromPDF($filePath)
    {
        $parser = new PDFParser();
        $pdf = $parser->parseFile($filePath);
        return $pdf->getText();
    }

    private function extractTextFromDOCX($filePath)
    {
        try {
            $phpWord = WordReader::load($filePath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }

            return $text;
        } catch (\Exception $e) {
            throw new \Exception('Error processing DOCX file: ' . $e->getMessage());
        }
    }
}
