<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_status_changes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->string('from_status');
            $table->string('to_status');

            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['admin_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_status_changes');
    }
};

