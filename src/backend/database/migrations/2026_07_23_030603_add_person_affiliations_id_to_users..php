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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'person_id')) {
                $table->foreignId('person_id')->nullable()->after('id')->constrained('people')->nullOnDelete();
            }

            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'person_id')) {
                $table->dropConstrainedForeignId('person_id');
            }

            if (! Schema::hasColumn('users', 'name')) {
                $table->string('name')->after('id');
            }
        });
    }
};
