<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToSentimentsTable extends Migration
{
    public function up()
    {
        Schema::table('sentiments', function (Blueprint $table) {
            // Add columns only if they do not already exist
            if (!Schema::hasColumn('sentiments', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }

            if (!Schema::hasColumn('sentiments', 'highlighted_text')) {
                $table->text('highlighted_text')->nullable();
            }

            if (!Schema::hasColumn('sentiments', 'positive_percentage')) {
                $table->float('positive_percentage')->nullable();
            }

            if (!Schema::hasColumn('sentiments', 'negative_percentage')) {
                $table->float('negative_percentage')->nullable();
            }

            if (!Schema::hasColumn('sentiments', 'neutral_percentage')) {
                $table->float('neutral_percentage')->nullable();
            }

            if (!Schema::hasColumn('sentiments', 'score')) {
                $table->float('score')->nullable();
            }

            if (!Schema::hasColumn('sentiments', 'grade')) {
                $table->string('grade')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('sentiments', function (Blueprint $table) {
            // Drop columns only if they exist
            if (Schema::hasColumn('sentiments', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('sentiments', 'highlighted_text')) {
                $table->dropColumn('highlighted_text');
            }

            if (Schema::hasColumn('sentiments', 'positive_percentage')) {
                $table->dropColumn('positive_percentage');
            }

            if (Schema::hasColumn('sentiments', 'negative_percentage')) {
                $table->dropColumn('negative_percentage');
            }

            if (Schema::hasColumn('sentiments', 'neutral_percentage')) {
                $table->dropColumn('neutral_percentage');
            }

            if (Schema::hasColumn('sentiments', 'score')) {
                $table->dropColumn('score');
            }

            if (Schema::hasColumn('sentiments', 'grade')) {
                $table->dropColumn('grade');
            }
        });
    }
}
