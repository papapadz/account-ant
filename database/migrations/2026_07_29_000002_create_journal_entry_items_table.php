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
        Schema::create('journal_entry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ledger_account_item_id')->constrained('ledger_account_items')->onDelete('cascade');
            $table->string('description');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit', 50)->default('pcs');
            $table->decimal('price', 14, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_items');
    }
};
