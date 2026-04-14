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
            // You can use enum for strict validation
            $table->enum('status', ['received', 'resolved'])->default('received')->after('id');
            
            // OR use a simple string for more flexibility
            // $table->string('status')->default('received')->after('id');
    });
    }

    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
