<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Adds user_id after the ID column
            // We use nullable() in case you have existing products without owners
            $table->foreignId('user_id')
                  ->after('id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the foreign key first, then the column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};