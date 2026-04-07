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
        Schema::table('stocks', function (Blueprint $table) {
            // Adds the payment_id foreign key
            // nullable() is important so existing stock isn't blocked
            // constrained() automatically links to the 'payments' table
            $table->foreignId('payment_id')
                  ->after('product_id') // Keeps the DB organized
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Drop the foreign key first, then the column
            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');
        });
    }
};