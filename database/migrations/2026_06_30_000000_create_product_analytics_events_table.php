<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('project', 80);
            $table->string('event_name', 80);
            $table->string('feature', 80)->nullable();
            $table->string('source', 120)->nullable();
            $table->string('destination', 120)->nullable();
            $table->string('page_path')->nullable();
            $table->string('session_id', 80)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();

            $table->index(['project', 'event_name']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_analytics_events');
    }
};
