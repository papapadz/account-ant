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
        Schema::table('ledger_account_items', function (Blueprint $table) {
            $table->date('payment_date')->nullable()->after('is_paid');
            $table->text('payment_remarks')->nullable()->after('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger_account_items', function (Blueprint $table) {
            $table->dropColumn(['payment_date', 'payment_remarks']);
        });
    }
};
