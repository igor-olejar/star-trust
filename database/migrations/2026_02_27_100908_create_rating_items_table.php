<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rating_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rating_id')->constrained()->onDelete('cascade');
            $table->foreignId(column: 'voting_category_id')->constrained();
            $table->integer('score');
            $table->integer('number_of_votes')->default(0);
            $table->timestamps();

            $table->unique(columns: ['rating_id', 'voting_category_id', 'number_of_votes'], name: 'unique_vote_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rating_items');
    }
};
