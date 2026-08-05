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
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            // Define 'name' column: varchar(100), not null
            // SQL: `name` varchar(100) ... NOT NULL
            $table->string('name', 100);

            // Define 'translations' column: text, nullable
            // SQL: `translations` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
            $table->text('translations')->nullable(); // Assuming nullable as NOT NULL is absent in SQL

            // Define 'created_at' column: timestamp, nullable
            // SQL: `created_at` timestamp NULL DEFAULT NULL
            $table->timestamp('created_at')->nullable();

            // Define 'updated_at' column: timestamp, not null, default current timestamp, auto-update on row change
            // SQL: `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Define 'flag' column: tinyint(1) (boolean), not null, default 1 (true)
            // SQL: `flag` tinyint(1) NOT NULL DEFAULT '1'
            $table->boolean('flag')->default(true);

            // Define 'wikiDataId' column: varchar(255), nullable, with comment
            // SQL: `wikiDataId` varchar(255) ... DEFAULT NULL COMMENT 'Rapid API GeoDB Cities'
            $table->string('wikiDataId')->nullable()->comment('Rapid API GeoDB Cities');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
