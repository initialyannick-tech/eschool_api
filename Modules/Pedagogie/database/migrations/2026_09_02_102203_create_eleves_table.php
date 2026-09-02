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
        Schema::create('eleves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('parents')->nullOnDelete();
            $table->string('matricule', 50)->unique();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->enum('sexe', ['masculin', 'feminin',]);
            $table->date('date_naissance');
            $table->string('lieu_naissance', 150);
            $table->string('nationalite', 100)->default('Gabonaise');
            $table->text('adresse')->nullable();
            $table->string('telephone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('photo')->nullable();
            $table->text('situation_particuliere')->nullable();
            $table->enum('statut', ['preinscrit', 'inscrit', 'reinscrit', 'transfere', 'suspendu', 'exclu', 'orienté', 'reorienté',])->default('preinscrit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eleves');
    }
};
