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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('business_name', 100);
            $table->string('business_description', 255);
            $table->bigInteger('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->string('business_classification', 255)->nullable();
            $table->enum('business_scope', ['National', 'Regional', 'City/Municipality', 'Barangay'])->nullable();
            $table->string('street', 20)->nullable();
            $table->string('building_number', 20)->nullable();
            $table->string('barangay', 50)->nullable();
            $table->string('zip', 10)->nullable();
            $table->date('date_started')->nullable();
            $table->date('date_ended')->nullable();
            $table->boolean('is_government')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
