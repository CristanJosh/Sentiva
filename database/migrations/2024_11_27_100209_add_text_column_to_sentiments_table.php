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
        if (!Schema::hasColumn('sentiments', 'text')) {
            Schema::table('sentiments', function (Blueprint $table) {
                $table->text('text')->nullable();
            });
        }
    }
    

public function down()
{
    Schema::table('sentiments', function (Blueprint $table) {
        $table->dropColumn('text');
    });
}

};
