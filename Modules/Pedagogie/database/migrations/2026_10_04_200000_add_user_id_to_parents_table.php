<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('email')
                ->unique()
                ->constrained('users')
                ->nullOnDelete();
        });

        DB::table('parents')
            ->whereNotNull('email')
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($parent) => strtolower(trim($parent->email)))
            ->each(function ($parents, $email): void {
                if ($parents->count() !== 1) {
                    return;
                }

                $userId = DB::table('users')
                    ->join('roles', 'roles.id', '=', 'users.role_id')
                    ->where('roles.code', 'parent')
                    ->whereRaw('LOWER(TRIM(users.email)) = ?', [$email])
                    ->value('users.id');

                if ($userId !== null) {
                    DB::table('parents')
                        ->where('id', $parents->first()->id)
                        ->whereNull('user_id')
                        ->update(['user_id' => $userId]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
