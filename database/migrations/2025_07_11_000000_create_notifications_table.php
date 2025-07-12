<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('title'); // "Invoice Created", "Domain Renewed", etc.
            $table->text('message'); // Full description of what happened
            $table->enum('type', [
                'invoice_created',
                'invoice_paid', 
                'invoice_overdue',
                'domain_renewed',
                'domain_expiring',
                'domain_expired',
                'hosting_created',
                'hosting_upgraded',
                'hosting_suspended',
                'support_ticket_resolved',
                'general'
            ]);
            $table->json('data')->nullable(); 
            $table->timestamp('read_at')->nullable(); 
            $table->timestamps();

            // Indexes for performance
            $table->index(['client_id', 'read_at']);
            $table->index(['client_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};