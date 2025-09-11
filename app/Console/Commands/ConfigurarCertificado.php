<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Helpers\CertificadoHelper;

class ConfigurarCertificado extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificado:configurar 
                            {email : Email do usuário}
                            {arquivo : Caminho do arquivo .pfx}
                            {senha : Senha do certificado}
                            {--salvar-senha : Salvar senha criptografada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configurar certificado digital para um usuário';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $arquivo = $this->argument('arquivo');
        $senha = $this->argument('senha');
        $salvarSenha = $this->option('salvar-senha');
        
        $this->info('🔐 Configurando certificado digital');
        $this->info('=' . str_repeat('=', 50));
        
        // Buscar usuário
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("❌ Usuário não encontrado: {$email}");
            return 1;
        }
        
        $this->info("✓ Usuário encontrado: {$user->name}");
        
        // Verificar arquivo
        if (!file_exists($arquivo)) {
            $this->error("❌ Arquivo não encontrado: {$arquivo}");
            return 1;
        }
        
        $this->info("✓ Arquivo encontrado: {$arquivo}");
        
        // Validar certificado
        $this->info("📋 Validando certificado...");
        $validacao = CertificadoHelper::validar($arquivo, $senha);
        
        if (!$validacao['valido']) {
            $this->error("❌ Certificado inválido: " . ($validacao['erro'] ?? 'Erro desconhecido'));
            return 1;
        }
        
        $this->info("✓ Certificado válido!");
        $this->info("   CN: " . $validacao['cn']);
        $this->info("   Válido até: " . $validacao['validade']);
        
        // Configurar certificado
        $this->info("🔄 Configurando certificado...");
        
        if (CertificadoHelper::configurarCertificadoPadrao($user, $arquivo, $senha)) {
            $this->info("✅ Certificado configurado com sucesso!");
            
            // Mostrar status
            $status = CertificadoHelper::getStatus($user);
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['Configurado', $status['configurado'] ? 'Sim' : 'Não'],
                    ['Arquivo existe', $status['existe'] ? 'Sim' : 'Não'],
                    ['Ativo', $status['ativo'] ? 'Sim' : 'Não'],
                    ['Válido', $status['valido'] ? 'Sim' : 'Não'],
                    ['Senha salva', $status['senha_salva'] ? 'Sim' : 'Não'],
                    ['CN', $status['cn'] ?? 'N/A'],
                    ['Validade', $status['validade'] ?? 'N/A'],
                ]
            );
            
            return 0;
        } else {
            $this->error("❌ Erro ao configurar certificado");
            return 1;
        }
    }
}