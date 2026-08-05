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
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            // Define 'name' column: varchar(255), not null
            // SQL: `name` varchar(255) ... NOT NULL
            $table->string('name'); // Defaults to varchar(255)

            // Define 'country_id' column: big integer unsigned, not null
            // SQL: `country_id` bigint unsigned NOT NULL
            $table->bigInteger('country_id')->unsigned();

            // Define 'country_code' column: char(2), not null
            // SQL: `country_code` char(2) ... NOT NULL
            $table->char('country_code', 2);

            // Define 'fips_code' column: varchar(255), nullable
            // SQL: `fips_code` varchar(255) ... DEFAULT NULL
            $table->string('fips_code')->nullable();

            // Define 'iso2' column: varchar(255), nullable
            // SQL: `iso2` varchar(255) ... DEFAULT NULL
            $table->string('iso2')->nullable();

            // Define 'type' column: varchar(191), nullable
            // SQL: `type` varchar(191) ... DEFAULT NULL
            $table->string('type', 191)->nullable(); // varchar(191) for utf8mb4 index compatibility

            // Define 'latitude' column: decimal(10, 8), nullable
            // SQL: `latitude` decimal(10,8) DEFAULT NULL
            $table->decimal('latitude', 10, 8)->nullable();

            // Define 'longitude' column: decimal(11, 8), nullable
            // SQL: `longitude` decimal(11,8) DEFAULT NULL
            $table->decimal('longitude', 11, 8)->nullable();

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

            // Define index based on SQL KEY definition
            // SQL: KEY `country_region` (`country_id`)
            // $table->index('country_id'); // Explicitly add index for country_id

            // Define foreign key constraint
            // Prerequisite: 'countries' table must exist with a compatible 'id' column (e.g., mediumIncrements).
            // SQL: CONSTRAINT `country_region_final` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`)
            $table->foreign('country_id'/* , 'states_country_id_foreign' */) // Optional: specify constraint name if needed
                ->references('id')->on('countries');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
