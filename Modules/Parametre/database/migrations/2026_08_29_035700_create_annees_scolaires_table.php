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
        Schema::create('annees_scolaires', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 20)->unique();
            $table->unsignedSmallInteger('annee_debut');
            $table->unsignedSmallInteger('annee_fin');
            $table->enum('statut', ['preparee', 'ouverte', 'cloturee'])->default('preparee');
            $table->boolean('active')->default(false);
            $table->date('date_ouverture')->nullable();
            $table->date('date_cloture')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annees_scolaires');
    }
};
