<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_funds', function (Blueprint $table) {
            if (!Schema::hasColumn('project_funds', 'date_received')) {
                $table->date('date_received')->nullable()->after('initial_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_funds', function (Blueprint $table) {
            if (Schema::hasColumn('project_funds', 'date_received')) {
                $table->dropColumn('date_received');
            }
        });
    }
};
