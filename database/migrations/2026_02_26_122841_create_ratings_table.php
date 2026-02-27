<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_id')->constrained(table: 'users');
            $table->foreignId('target_id')->constrained(table: 'users');
            $table->unsignedInteger('target_type_id');
            $table->decimal('overall_rating', 3, 2);
            $table->timestamps();

            $table->unique(columns: ['reviewer_id', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
