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
        Schema::create('tkt_tickets', function (Blueprint $table) {
            $table->id(); // id int [PK]
            $table->string('title');
            $table->text('description')->nullable();

            // Claves foráneas
            $table->foreignId('category_id')->nullable()->constrained('tkt_categories')->onDelete('set null');
            $table->foreignId('priority_id')->nullable()->constrained('tkt_priorities')->onDelete('set null');
            $table->foreignId('status_id')->constrained('tkt_statuses')->onDelete('restrict');
            $table->foreignId('area_id')->nullable()->constrained('areas')->onDelete('set null');

            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps(); // created_at & updated_at
            $table->timestamp('resolved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_tickets');
    }
};
