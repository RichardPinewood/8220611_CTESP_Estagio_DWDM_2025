<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ticket_id');
            $table->uuid('admin_id');
            $table->decimal('hours_spent', 5, 2);
            $table->text('description');
            $table->date('work_date');
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('support_tickets')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['ticket_id', 'work_date']);
            $table->index('admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};