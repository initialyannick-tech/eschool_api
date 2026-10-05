<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('email')
                ->unique()
                ->constrained('users')
                ->nullOnDelete();
        });

        DB::table('eleves')
            ->whereNotNull('email')
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($eleve) => strtolower(trim($eleve->email)))
            ->each(function ($eleves, $email): void {
                if ($eleves->count() !== 1) {
                    return;
                }

                $userId = DB::table('users')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->where('roles.code', 'eleve')
                    ->whereRaw('LOWER(TRIM(users.email)) = ?', [$email])
                    ->value('users.id');

                if ($userId !== null) {
                    DB::table('eleves')
                        ->where('id', $eleves->first()->id)
                        ->whereNull('user_id')
                        ->update(['user_id' => $userId]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
