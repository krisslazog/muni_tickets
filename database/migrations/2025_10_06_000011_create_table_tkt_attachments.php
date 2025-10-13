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
        Schema::create('tkt_attachments', function (Blueprint $table) {
            $table->id(); // id int [PK]

            // Clave foránea al ticket
            $table->foreignId('ticket_id')->constrained('tkt_tickets')->onDelete('cascade');

            $table->string('file_path');
            $table->string('file_name');
            $table->timestamp('uploaded_at')->useCurrent();

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkt_attachments');
    }
};
