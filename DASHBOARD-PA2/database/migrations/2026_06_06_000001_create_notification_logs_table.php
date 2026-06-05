<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('reference_type', 50);
            $table->unsignedBigInteger('reference_id');
            $table->string('target_type', 50)->default('akun');
            $table->unsignedBigInteger('target_id');
            $table->string('period', 50)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(
                ['type', 'reference_type', 'reference_id', 'target_type', 'target_id', 'period'],
                'notification_logs_unique_target'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
