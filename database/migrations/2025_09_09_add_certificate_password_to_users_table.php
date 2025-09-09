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
        Schema::table('users', function (Blueprint $table) {
            $table->text('certificado_digital_senha')->nullable()
                ->after('certificado_digital_ativo')
                ->comment('Senha do certificado criptografada (opcional)');
            $table->boolean('certificado_digital_senha_salva')->default(false)
                ->after('certificado_digital_senha')
                ->comment('Indica se a senha foi salva');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'certificado_digital_senha',
                'certificado_digital_senha_salva'
            ]);
        });
    }
};