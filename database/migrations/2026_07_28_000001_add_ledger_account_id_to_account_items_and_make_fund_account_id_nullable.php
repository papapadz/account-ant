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
        Schema::table('ledger_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('fund_account_id')->nullable()->change();
        });

        Schema::table('account_items', function (Blueprint $table) {
            $table->unsignedBigInteger('ledger_account_id')->nullable()->after('description');
            $table->foreign('ledger_account_id')->references('id')->on('ledger_accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('account_items', function (Blueprint $table) {
            $table->dropForeign(['ledger_account_id']);
            $table->dropColumn('ledger_account_id');
        });

        Schema::table('ledger_accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('fund_account_id')->nullable(false)->change();
        });
    }
};
