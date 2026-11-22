<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('conversation_messages')) {
            return;
        }

        Schema::create('conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('auteur_id')->constrained('users')->cascadeOnDelete();
            $table->text('contenu')->nullable();
            $table->string('piece_jointe_chemin')->nullable();
            $table->string('piece_jointe_nom')->nullable();
            $table->string('piece_jointe_mime', 150)->nullable();
            $table->unsignedBigInteger('piece_jointe_taille')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        // The table may predate this migration and contain private messages or attachments.
    }
};
