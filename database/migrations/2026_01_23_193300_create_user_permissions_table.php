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
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();


            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('viewer_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['owner_id', 'viewer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
