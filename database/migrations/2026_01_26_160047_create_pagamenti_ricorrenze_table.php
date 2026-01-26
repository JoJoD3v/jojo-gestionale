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
        Schema::create('ricorrenze_pagamenti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pagamento_id')->constrained('pagamenti')->onDelete('cascade');
            $table->date('data_ricorrenza'); // Data specifica della ricorrenza (es. 2026-02-15)
            $table->enum('stato', ['in_sospeso', 'pagato', 'annullato'])->default('in_sospeso');
            $table->date('data_pagamento')->nullable(); // Quando è stato effettivamente pagato
            $table->text('note')->nullable();
            $table->timestamps();
            
            // Evita duplicati: una sola ricorrenza per pagamento per data
            $table->unique(['pagamento_id', 'data_ricorrenza']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ricorrenze_pagamenti');
    }
};
