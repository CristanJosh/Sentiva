<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sentiments', function (Blueprint $table) {
            // Only add the column if it doesn't already exist
            if (!Schema::hasColumn('sentiments', 'original_text')) {
                $table->text('original_text')->nullable();
            }
        });
    }
    
    public function down()
    {
        Schema::table('sentiments', function (Blueprint $table) {
            // Safely remove the column in the down method
            if (Schema::hasColumn('sentiments', 'original_text')) {
                $table->dropColumn('original_text');
            }
        });
    }
    

};
