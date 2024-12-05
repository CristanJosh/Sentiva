<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSentimentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sentiments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->text('text');
    $table->text('highlighted_text')->nullable();
    $table->integer('positive_count');
    $table->integer('negative_count');
    $table->integer('neutral_count');
    $table->integer('total_word_count');
    $table->decimal('positive_percentage', 5, 2);
    $table->decimal('negative_percentage', 5, 2);
    $table->decimal('neutral_percentage', 5, 2);
    $table->decimal('score', 5, 2);
    $table->string('grade');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sentiments');
    }
}
