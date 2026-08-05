<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ledger_account_items', function (Blueprint $table) {
            if (!Schema::hasColumn('ledger_account_items', 'posting_date')) {
                $table->date('posting_date')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ledger_account_items', function (Blueprint $table) {
            if (Schema::hasColumn('ledger_account_items', 'posting_date')) {
                $table->dropColumn('posting_date');
            }
        });
    }
};
