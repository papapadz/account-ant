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
        Schema::create('person_affiliations', function (Blueprint $table) {
            $table->id();
            $table->foreign('person_id')->references('id')->on('people');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            $table->enum('affiliation_level', ['Rank and File', 'Supervisory', 'Managerial', 'Executive']);
            $table->enum('employment_status', ['Regular', 'Part-time', 'Contractual', 'Internship', 'Temporary'])->default('Contractual');
            $table->string('employee_id', 20);
            $table->unsignedBigInteger('position_id');
            $table->foreign('position_id')->references('id')->on('positions')->nullOnDelete();
            $table->boolean('is_head')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_affiliations');
    }
};
