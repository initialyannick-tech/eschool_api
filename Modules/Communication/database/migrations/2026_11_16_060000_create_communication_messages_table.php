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
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->string('title', 180);
                $table->text('message');
                $table->string('audience', 20)->default('all');
                $table->foreignId('target_role_id')->nullable()->constrained('roles')->nullOnDelete();
                $table->json('channels');
                $table->unsignedInteger('recipient_count')->default(0);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();

                $table->index(['audience', 'target_role_id', 'published_at']);
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
