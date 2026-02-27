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
        Schema::create('voting_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('target_type_id')->constrained(table: 'user_types');
            $table->decimal('weight', 3, 2)->default(1.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voting_categories');
    }
};
