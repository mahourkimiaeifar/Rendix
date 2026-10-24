<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_requests', function (Blueprint $table) {
            $table->id();

            $table->string('tracking_code')->unique();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');

            $table->string('company')->nullable();
            $table->string('role')->nullable();

            $table->string('service');
            $table->string('current_website')->nullable();
            $table->string('current_website_url')->nullable();

            $table->text('business_description')->nullable();
            $table->text('project_goals')->nullable();
            $table->text('competitors')->nullable();
            $table->text('references')->nullable();

            $table->string('timeline')->nullable();
            $table->string('budget')->nullable();
            $table->text('message')->nullable();

            $table->string('status')->default('new')->index();

            $table->json('metadata')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_requests');
    }
};