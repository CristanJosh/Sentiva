<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #2563eb;
            font-size: 24px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h3 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .highlighted {
            background-color: #fef08a;
            font-weight: bold;
        }

        .score-bar {
            height: 10px;
            background-color: #e5e7eb;
            border-radius: 5px;
            position: relative;
            margin: 10px 0;
        }

        .score-bar .indicator {
            height: 100%;
            border-radius: 5px;
            background-color: #2563eb;
        }

        .metric {
            font-size: 16px;
            margin: 10px 0;
            font-weight: bold;
        }

        .positive {
            color: #2563eb;
        }

        .negative {
            color: #ef4444;
        }

        .neutral {
            color: #10b981;
        }

        .grade {
            text-align: center;
            font-size: 20px;
            margin-top: 20px;
            font-weight: bold;
        }

        .grade.positive {
            color: #2563eb;
        }

        .grade.negative {
            color: #ef4444;
        }

        .grade.neutral {
            color: #10b981;
        }
    </style>
</head>
<body>
    <h1>Sentiment Analysis</h1>

    <div class="section">
        <h3>Analysis Text:</h3>
        <p>{!! $sentiment->highlighted_text !!}</p>
    </div>

    <div class="section">
        <h3>Sentiment Score:</h3>
        <div class="score-bar">
            <div class="indicator" style="width: {{ (($sentiment->score + 1) / 2) * 100 }}%;"></div>
        </div>
    </div>

    <div class="section">
        <div class="metric positive">Positive Words: {{ $sentiment->positive_count }}</div>
        <div class="metric negative">Negative Words: {{ $sentiment->negative_count }}</div>
        <div class="metric neutral">Neutral Words: {{ $sentiment->neutral_count }}</div>
    </div>

    <div class="grade {{ strtolower($sentiment->grade) }}">
        Grade: {{ $sentiment->grade }}
    </div>
</body>
</html>
