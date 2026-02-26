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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('user_type_id');
            $table->enum('status', ['pending', 'verified', 'blocked']);
            $table->string('city');
            $table->string('country_code', 2);
            $table->json('socials')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_type_id']);
            $table->dropColumn([
                'user_type_id',
                'status',
                'city',
                'country_code',
                'socials',
            ]);
        });
    }
};
