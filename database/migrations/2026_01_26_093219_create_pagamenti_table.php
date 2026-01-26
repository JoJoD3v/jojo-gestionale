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
        Schema::create('pagamenti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clienti')->onDelete('cascade');
            $table->string('tipo_lavoro');
            $table->decimal('importo', 10, 2);
            $table->enum('cadenza', ['oneshot', 'periodico']);
            $table->enum('frequenza', ['mensile', 'trimestrale', 'annuale'])->nullable();
            $table->date('data_inizio')->nullable();
            $table->date('data_scadenza');
            $table->enum('stato', ['in_sospeso', 'pagato', 'annullato'])->default('in_sospeso');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamenti');
    }
};
