<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sentiment extends Model
{
    use HasFactory;


    

    public function user()
{
    
    return $this->belongsTo(User::class);
    
}


        protected $fillable = [
            'user_id',
            'text',
            'highlighted_text',
            'positive_count',
            'negative_count',
            'neutral_count',
            'total_word_count',
            'positive_percentage',
            'negative_percentage',
            'neutral_percentage',
            'score',
            'grade',
        ];

        
}
