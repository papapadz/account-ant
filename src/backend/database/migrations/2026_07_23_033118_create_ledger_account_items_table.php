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
        Schema::create('ledger_account_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ledger_account_id');
            $table->foreign('ledger_account_id')->references('id')->on('ledger_accounts')->nullOnDelete();
            $table->unsignedBigInteger('account_item_id');
            $table->foreign('account_item_id')->references('id')->on('account_items')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('transaction_type', ['debit', 'credit']);
            $table->string('description', 255)->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_account_items');
    }
};
