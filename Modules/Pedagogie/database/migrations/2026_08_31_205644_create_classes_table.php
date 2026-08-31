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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annee_scolaire_id')->constrained('annees_scolaires')->restrictOnDelete();
            $table->foreignId('cycle_id')->constrained('cycles')->restrictOnDelete();
            $table->foreignId('serie_id')->nullable()->constrained('series')->restrictOnDelete();
            $table->foreignId('professeur_principal_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nom');
            $table->unsignedInteger('capacite')->default(30);
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->unique(['annee_scolaire_id', 'nom']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
