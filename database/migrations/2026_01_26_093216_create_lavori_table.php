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
        Schema::create('lavori', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clienti')->onDelete('cascade');
            $table->date('data_lavoro');
            $table->text('descrizione');
            $table->enum('stato', ['da_fare', 'in_corso', 'completato'])->default('da_fare');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lavori');
    }
};
