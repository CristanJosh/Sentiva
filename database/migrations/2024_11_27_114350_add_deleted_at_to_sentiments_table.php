<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToSentimentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sentiments', function (Blueprint $table) {
            $table->softDeletes(); // Adds the `deleted_at` column for soft deletes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sentiments', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Removes the `deleted_at` column
        });
    }
}
