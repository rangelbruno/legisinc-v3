<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CertificadoHelper
{
    /**
     * Obter caminho completo do certificado do usuário
     */
    public static function getCaminhoCompleto(User $user): ?string
    {
        if (!$user->certificado_digital_path) {
            return null;
        }
        
        return storage_path('app/private/' . $user->certificado_digital_path);
    }
    
    /**
     * Verificar se o certificado existe fisicamente
     */
    public static function certificadoExiste(User $user): bool
    {
        $caminho = self::getCaminhoCompleto($user);
        return $caminho && file_exists($caminho);
    }
    
    /**
     * Obter senha descriptografada do certificado
     */
    public static function getSenha(User $user): ?string
    {
        if (!$user->certificado_digital_senha_salva || !$user->certificado_digital_senha) {
            return null;
        }
        
        try {
            return decrypt($user->certificado_digital_senha);
        } catch (\Exception $e) {
            Log::error('Erro ao descriptografar senha do certificado', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Verificar se o certificado está válido (não expirado)
     */
    public static function isValido(User $user): bool
    {
        if (!$user->certificado_digital_validade) {
            return false;
        }
        
        return now()->lt($user->certificado_digital_validade);
    }
    
    /**
     * Obter status completo do certificado
     */
    public static function getStatus(User $user): array
    {
        return [
            'configurado' => !empty($user->certificado_digital_path),
            'existe' => self::certificadoExiste($user),
            'ativo' => $user->certificado_digital_ativo,
            'valido' => self::isValido($user),
            'senha_salva' => $user->certificado_digital_senha_salva,
            'cn' => $user->certificado_digital_cn,
            'validade' => $user->certificado_digital_validade,
            'nome_arquivo' => $user->certificado_digital_nome,
            'upload_em' => $user->certificado_digital_upload_em,
        ];
    }
    
    /**
     * Validar certificado com senha
     */
    public static function validar(string $caminhoCertificado, string $senha): array
    {
        try {
            // Comando com suporte a algoritmos legacy
            $comando = sprintf(
                'openssl pkcs12 -legacy -in %s -passin pass:%s -noout 2>&1',
                escapeshellarg($caminhoCertificado),
                escapeshellarg($senha)
            );
            
            exec($comando, $output, $returnCode);
            
            if ($returnCode !== 0) {
                return [
                    'valido' => false,
                    'erro' => implode("\n", $output)
                ];
            }
            
            // Extrair CN
            $comandoCN = sprintf(
                'openssl pkcs12 -legacy -in %s -passin pass:%s -nokeys -clcerts 2>/dev/null | openssl x509 -noout -subject | sed "s/.*CN=\\([^,/]*\\).*/\\1/"',
                escapeshellarg($caminhoCertificado),
                escapeshellarg($senha)
            );
            
            $cn = trim(shell_exec($comandoCN));
            
            // Extrair validade
            $comandoValidade = sprintf(
                'openssl pkcs12 -legacy -in %s -passin pass:%s -nokeys -clcerts 2>/dev/null | openssl x509 -noout -enddate | cut -d= -f2',
                escapeshellarg($caminhoCertificado),
                escapeshellarg($senha)
            );
            
            $validadeStr = trim(shell_exec($comandoValidade));
            $validade = $validadeStr ? date('Y-m-d H:i:s', strtotime($validadeStr)) : null;
            
            return [
                'valido' => true,
                'cn' => $cn,
                'validade' => $validade
            ];
            
        } catch (\Exception $e) {
            return [
                'valido' => false,
                'erro' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Configurar certificado padrão para testes
     */
    public static function configurarCertificadoPadrao(User $user, string $caminhoOrigem = null, string $senha = '123Ligado'): bool
    {
        try {
            // Usar certificado padrão se não fornecido
            if (!$caminhoOrigem) {
                $caminhoOrigem = '/tmp/certificado_teste.pfx';
            }
            
            if (!file_exists($caminhoOrigem)) {
                Log::error('Arquivo de certificado não encontrado', ['caminho' => $caminhoOrigem]);
                return false;
            }
            
            // Validar certificado
            $validacao = self::validar($caminhoOrigem, $senha);
            if (!$validacao['valido']) {
                Log::error('Certificado inválido', ['erro' => $validacao['erro']]);
                return false;
            }
            
            // Criar diretório se não existir
            $dirPrivate = storage_path('app/private/certificados-digitais');
            if (!is_dir($dirPrivate)) {
                mkdir($dirPrivate, 0755, true);
            }
            
            // Remover certificado anterior
            if ($user->certificado_digital_path && Storage::exists('private/' . $user->certificado_digital_path)) {
                Storage::delete('private/' . $user->certificado_digital_path);
            }
            
            // Copiar novo certificado
            $nomeArquivo = 'certificado_' . $user->id . '_' . time() . '.pfx';
            $caminhoRelativo = 'certificados-digitais/' . $nomeArquivo;
            $caminhoCompleto = $dirPrivate . '/' . $nomeArquivo;
            
            if (!copy($caminhoOrigem, $caminhoCompleto)) {
                Log::error('Erro ao copiar certificado', ['destino' => $caminhoCompleto]);
                return false;
            }
            
            // Ajustar permissões
            chmod($caminhoCompleto, 0600);
            @chown($caminhoCompleto, 'www-data');
            @chgrp($caminhoCompleto, 'www-data');
            
            // Atualizar dados do usuário
            $user->update([
                'certificado_digital_path' => $caminhoRelativo,
                'certificado_digital_nome' => basename($caminhoOrigem),
                'certificado_digital_upload_em' => now(),
                'certificado_digital_validade' => $validacao['validade'],
                'certificado_digital_cn' => $validacao['cn'],
                'certificado_digital_ativo' => true,
                'certificado_digital_senha' => encrypt($senha),
                'certificado_digital_senha_salva' => true,
            ]);
            
            Log::info('Certificado configurado com sucesso', [
                'user_id' => $user->id,
                'cn' => $validacao['cn'],
                'validade' => $validacao['validade']
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Erro ao configurar certificado', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}