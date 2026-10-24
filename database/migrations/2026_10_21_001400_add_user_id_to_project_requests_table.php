<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('project_requests', 'user_id')) {
            return;
        }

        Schema::table('project_requests', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['user_id', 'created_at'], 'project_requests_user_id_created_at_index');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('project_requests', 'user_id')) {
            return;
        }

        Schema::table('project_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex('project_requests_user_id_created_at_index');
            $table->dropColumn('user_id');
        });
    }
};