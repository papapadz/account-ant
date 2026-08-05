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
        Schema::table('account_items', function (Blueprint $table) {
            $table->enum('transaction_type', ['debit', 'credit'])->default('debit')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_items', function (Blueprint $table) {
            $table->dropColumn('transaction_type');
        });
    }
};
