<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $this->addUserLink('parents');
        $this->addUserLink('eleves');

        $roleUsers = DB::table('users')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->whereNotNull('users.email')
            ->get(['users.id', 'users.email', 'roles.code', 'roles.libelle']);

        $this->linkUniqueEmailMatches('parents', $roleUsers, ['parent', 'tuteur']);
        $this->linkUniqueEmailMatches('eleves', $roleUsers, ['eleve', 'student']);
    }

    public function down(): void
    {
        // Existing databases may already have these links; preserve them on rollback.
    }

    private function addUserLink(string $tableName): void
    {
        if (! Schema::hasColumn($tableName, 'user_id')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        $hasUniqueIndex = collect(Schema::getIndexes($tableName))
            ->contains(fn (array $index): bool => $index['columns'] === ['user_id'] && $index['unique']);

        if (! $hasUniqueIndex) {
            $hasDuplicateLinks = DB::table($tableName)
                ->select('user_id')
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) > 1')
                ->exists();

            if ($hasDuplicateLinks) {
                throw new RuntimeException(
                    "Impossible d'ajouter la contrainte unique: plusieurs lignes de {$tableName} utilisent le même user_id.",
                );
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->unique('user_id', $tableName.'_user_id_unique');
            });
        }
    }

    private function linkUniqueEmailMatches(string $table, iterable $roleUsers, array $roleKeywords): void
    {
        $usersByEmail = [];
        foreach ($roleUsers as $user) {
            $role = Str::ascii(strtolower($user->code.' '.$user->libelle));
            if (! collect($roleKeywords)->contains(fn (string $keyword): bool => str_contains($role, $keyword))) {
                continue;
            }

            $email = strtolower(trim($user->email));
            if ($email !== '') {
                $usersByEmail[$email][] = $user->id;
            }
        }

        $recordsByEmail = [];
        foreach (DB::table($table)->whereNotNull('email')->get(['id', 'email', 'user_id']) as $record) {
            $email = strtolower(trim($record->email));
            if ($email !== '' && $record->user_id === null) {
                $recordsByEmail[$email][] = $record->id;
            }
        }

        foreach ($recordsByEmail as $email => $recordIds) {
            $userIds = $usersByEmail[$email] ?? [];
            if (count($recordIds) === 1 && count($userIds) === 1) {
                DB::table($table)
                    ->where('id', $recordIds[0])
                    ->whereNull('user_id')
                    ->update(['user_id' => $userIds[0]]);
            }
        }
    }
};
