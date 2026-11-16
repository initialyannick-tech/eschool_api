<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('communication_messages')) {
            Schema::create('communication_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('auteur_id')->constrained('users')->cascadeOnDelete();
                $table->string('titre', 180);
                $table->text('contenu');
                $table->string('audience', 20)->default('all');
                $table->foreignId('role_cible_id')->nullable()->constrained('roles')->nullOnDelete();
                $table->json('canaux');
                $table->unsignedInteger('nombre_destinataires')->default(0);
                $table->timestamp('date_publication')->nullable();
                $table->timestamps();

                $table->index(['audience', 'role_cible_id', 'date_publication']);
            });

            return;
        }

        Schema::table('communication_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('communication_messages', 'target_role_id')) {
                $table->foreignId('target_role_id')->nullable()->constrained('roles')->nullOnDelete();
            }

            if (! Schema::hasColumn('communication_messages', 'published_at')) {
                $table->timestamp('published_at')->nullable();
            }

            if (Schema::hasColumn('communication_messages', 'title')) {
                $table->string('title', 180)->change();
            }
        });

        if (Schema::hasColumn('communication_messages', 'role_id')) {
            DB::table('communication_messages')
                ->whereNull('target_role_id')
                ->update(['target_role_id' => DB::raw('role_id')]);
        }

        if (Schema::hasColumn('communication_messages', 'sent_at')) {
            DB::table('communication_messages')
                ->whereNull('published_at')
                ->update(['published_at' => DB::raw('sent_at')]);
        }
    }

    public function down(): void
    {
        // The table may predate this migration and contain announcements to preserve.
    }
};
