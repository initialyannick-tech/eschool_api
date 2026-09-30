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
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone', 30);
            $table->string('telephone_secondaire', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->string('profession')->nullable();
            $table->string('lieu_travail')->nullable();
            $table->enum('statut', ['actif','inactif'])->default('actif');
            $table->text('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
