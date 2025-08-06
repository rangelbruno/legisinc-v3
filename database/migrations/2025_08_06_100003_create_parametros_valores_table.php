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
        Schema::create('parametros_valores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campo_id')->constrained('parametros_campos')->cascadeOnDelete();
            $table->text('valor');
            $table->string('tipo_valor')->default('string'); // string, json, boolean, etc.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('valido_ate')->nullable();
            $table->timestamps();
            
            $table->index(['campo_id', 'valido_ate']);
            $table->unique('campo_id'); // Apenas um valor por campo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros_valores');
    }
};