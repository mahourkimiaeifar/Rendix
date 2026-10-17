<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('service');
            $table->text('message');

            $table->string('source_page')->nullable();

            $table->string('status')->default('new')->index();
            $table->string('priority')->default('normal')->index();

            $table->timestamp('next_follow_up_at')->nullable();
            $table->timestamp('last_contact_at')->nullable();

            $table->boolean('is_archived')->default(false)->index();

            $table->json('metadata')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('created_at');
            $table->index(['status', 'is_archived']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};