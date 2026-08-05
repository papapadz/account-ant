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
            $table->unsignedBigInteger('fund_account_id')->nullable()->after('ledger_account_id');
            $table->foreign('fund_account_id')->references('id')->on('fund_accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger_account_items', function (Blueprint $table) {
            $table->dropForeign(['fund_account_id']);
            $table->dropColumn('fund_account_id');
        });
    }
};
