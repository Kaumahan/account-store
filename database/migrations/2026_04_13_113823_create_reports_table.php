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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            // Link to the payment being reported
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            // Link to the user making the report
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('type'); // e.g., 'invalid_login', 'expired', 'other'
            $table->text('description');
            $table->string('status')->default('pending'); // pending, resolved, dismissed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
