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
        Schema::table('productos', function (Blueprint $table) {
            // Definimos la nueva columna para el precio de oferta.
            $table->decimal('precio_oferta', 8, 2) // Usamos 'decimal' para dinero. 8 dígitos en total, 2 decimales.
                  ->nullable()                     // MUY IMPORTANTE: La hacemos 'nullable' para que los productos existentes no den error.
                  ->after('precio');              // Opcional, pero coloca la columna justo después de 'precio' en la BD, lo cual es ordenado.
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('precio_oferta');
        });
    }
};
