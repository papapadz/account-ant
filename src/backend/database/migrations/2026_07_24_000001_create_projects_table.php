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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('budget', 15, 2)->default(0.00);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('client_name', 150);
            $table->boolean('is_government')->default(false);
            
            // Address Model Integration
            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
            $table->string('house_number', 50)->nullable();
            $table->string('street', 100)->nullable();
            $table->string('village', 100)->nullable();
            $table->string('barangay', 100);
            $table->string('zip', 10);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
