<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fund_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('fund_accounts', 'status')) {
                $table->string('status', 20)->default('active')->after('amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fund_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('fund_accounts', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
