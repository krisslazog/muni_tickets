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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id(); // id int [PK]
            $table->string('title');
            $table->text('description')->nullable();

            // Claves foráneas
            $table->foreignId('category_id')->constrained('ticket_categories')->onDelete('cascade');
            $table->foreignId('priority_id')->constrained('ticket_priorities')->onDelete('cascade');
            $table->foreignId('status_id')->constrained('ticket_statuses')->onDelete('cascade');
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');

            $table->foreignId('requester_id')->constrained('persons')->onDelete('cascade');
            $table->foreignId('assignee_id')->constrained('persons')->onDelete('cascade');

            $table->timestamps(); // created_at & updated_at
            $table->timestamp('resolved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_tickets');
    }
};
