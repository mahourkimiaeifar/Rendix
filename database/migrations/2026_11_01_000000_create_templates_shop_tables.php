<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('features')->nullable(); // JSON
            $table->unsignedBigInteger('price'); // به تومان
            $table->string('demo_path')->nullable(); // مسیر فایل دمو
            $table->boolean('has_support')->default(false);
            $table->boolean('has_trial')->default(false);
            $table->boolean('has_guarantee')->default(false);
            $table->boolean('is_fullstack')->default(false); // false = front-end only
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('downloads_count')->default(0);
            $table->timestamps();
        });

        Schema::create('template_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->cascadeOnDelete();
            $table->string('language'); // React, Laravel, Vue, etc.
            $table->string('color')->nullable(); // رنگ badge
            $table->timestamps();
        });

        Schema::create('template_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->cascadeOnDelete();
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->boolean('is_demo')->default(false); // آیا این فایل دمو است؟
            $table->timestamps();
        });

        Schema::create('template_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->cascadeOnDelete();
            $table->string('reporter_email');
            $table->text('description');
            $table->string('status')->default('pending'); // pending, resolved, ignored
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_reports');
        Schema::dropIfExists('template_files');
        Schema::dropIfExists('template_languages');
        Schema::dropIfExists('templates');
    }
};