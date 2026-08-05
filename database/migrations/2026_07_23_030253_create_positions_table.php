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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->enum('industry', ['Health', 'Finance', 'Education', 'Information Technology', 'Retail', 'Manufacturing', 'Hospitality', 'Construction', 'Transportation', 'Engineering', 'Public Service', 'Others']);
            $table->integer('salary_grade')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
