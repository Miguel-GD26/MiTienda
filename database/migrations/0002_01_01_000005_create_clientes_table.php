<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =======================================================
        // 1. PRIMERO, CREAR LA TABLA PRINCIPAL 'clientes'
        // =======================================================
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('telefono')->nullable();
            $table->timestamps();
        });

        // =======================================================
        // 2. DESPUÉS, CREAR LA TABLA PIVOTE que depende de 'clientes'
        // =======================================================
        Schema::create('cliente_empresa', function (Blueprint $table) {
            $table->id();

            // Ahora esta línea funcionará, porque la tabla 'clientes' ya existe
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            
            $table->timestamps();
            $table->unique(['cliente_id', 'empresa_id']);
        });
    }

    public function down(): void
    {
        // En el 'down', el orden debe ser el inverso para borrar sin errores
        Schema::dropIfExists('cliente_empresa');
        Schema::dropIfExists('clientes');
    }
};