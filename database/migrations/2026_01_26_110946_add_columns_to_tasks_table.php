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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('nome');
            $table->foreignId('lavoro_id')->constrained('lavori')->onDelete('cascade');
            $table->date('scadenza');
            $table->enum('status', ['in_sospeso', 'completato'])->default('in_sospeso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['lavoro_id']);
            $table->dropColumn(['nome', 'lavoro_id', 'scadenza', 'status']);
        });
    }
};
