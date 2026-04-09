<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Use unsignedDecimal to prevent negative point injections
            $table->decimal('amount', 15, 2)->unsigned();

            // Optional: Reference the source (e.g., the purchase that earned these points)
            $table->string('source_type')->nullable(); // e.g., 'purchase', 'referral'
            $table->unsignedBigInteger('source_id')->nullable();

            // Audit trail
            $table->string('description')->nullable();

            $table->timestamps();

            // Indexing for faster balance calculations
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
