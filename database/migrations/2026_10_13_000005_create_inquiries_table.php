<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('business_type')->nullable();
            $table->string('service')->nullable();
            $table->string('budget', 64)->nullable();
            $table->text('message');
            $table->enum('status', ['new', 'read', 'replied'])->default('new')->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->nullable()->index(); // immutable: no updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
