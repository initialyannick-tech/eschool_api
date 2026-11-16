<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = [
        'communication_messages' => [
            'sender_id' => 'auteur_id',
            'title' => 'titre',
            'message' => 'contenu',
            'target_role_id' => 'role_cible_id',
            'channels' => 'canaux',
            'recipient_count' => 'nombre_destinataires',
            'published_at' => 'date_publication',
        ],
        'conversations' => [
            'created_by' => 'cree_par_id',
        ],
        'conversation_participants' => [
            'last_read_at' => 'derniere_lecture_le',
            'archived_at' => 'archive_le',
        ],
        'conversation_messages' => [
            'sender_id' => 'auteur_id',
            'body' => 'contenu',
            'attachment_path' => 'piece_jointe_chemin',
            'attachment_name' => 'piece_jointe_nom',
            'attachment_mime' => 'piece_jointe_mime',
            'attachment_size' => 'piece_jointe_taille',
        ],
    ];

    public function up(): void
    {
        foreach ($this->columns as $table => $columns) {
            foreach ($columns as $oldName => $newName) {
                $this->renameColumn($table, $oldName, $newName);
            }
        }

        $this->removeLegacyColumn('communication_messages', 'role_id', 'role_cible_id');
        $this->removeLegacyColumn('communication_messages', 'sent_at', 'date_publication');
    }

    public function down(): void
    {
        foreach ($this->columns as $table => $columns) {
            foreach ($columns as $oldName => $newName) {
                $this->renameColumn($table, $newName, $oldName);
            }
        }
    }

    private function renameColumn(string $table, string $oldName, string $newName): void
    {
        if (! Schema::hasColumn($table, $oldName)) {
            return;
        }

        if (! Schema::hasColumn($table, $newName)) {
            Schema::table($table, function (Blueprint $blueprint) use ($oldName, $newName): void {
                $blueprint->renameColumn($oldName, $newName);
            });

            return;
        }

        DB::table($table)
            ->whereNull($newName)
            ->update([$newName => DB::raw($oldName)]);

        $this->dropColumn($table, $oldName);
    }

    private function removeLegacyColumn(string $table, string $column, string $replacement): void
    {
        if (! Schema::hasColumn($table, $column)) {
            return;
        }

        if (Schema::hasColumn($table, $replacement)) {
            DB::table($table)
                ->whereNull($replacement)
                ->update([$replacement => DB::raw($column)]);
        }

        $this->dropColumn($table, $column);
    }

    private function dropColumn(string $table, string $column): void
    {
        foreach (Schema::getForeignKeys($table) as $foreignKey) {
            if (in_array($column, $foreignKey['columns'], true)) {
                Schema::table($table, function (Blueprint $blueprint) use ($foreignKey): void {
                    $blueprint->dropForeign($foreignKey['name']);
                });
            }
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column): void {
            $blueprint->dropColumn($column);
        });
    }
};
