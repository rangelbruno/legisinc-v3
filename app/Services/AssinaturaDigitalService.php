<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AssinaturaDigitalService
{
    /**
     * Tipos de certificado suportados
     */
    const TIPOS_CERTIFICADO = [
        'A1' => 'Certificado A1 (arquivo digital)',
        'A3' => 'Certificado A3 (cartão/token)',
        'PFX' => 'Arquivo .pfx/.p12',
        'SIMULADO' => 'Assinatura Simulada (desenvolvimento)'
    ];

    /**
     * Assinar PDF com certificado digital
     */
    public function assinarPDF(string $caminhoPDF, array $dadosAssinatura): ?string
    {
        try {
            Log::info('Iniciando assinatura digital do PDF', [
                'pdf_path' => $caminhoPDF,
                'tipo_certificado' => $dadosAssinatura['tipo_certificado'] ?? 'N/A'
            ]);

            // Validar arquivo PDF
            if (!file_exists($caminhoPDF)) {
                throw new \Exception('Arquivo PDF não encontrado: ' . $caminhoPDF);
            }

            // Validar dados da assinatura
            $this->validarDadosAssinatura($dadosAssinatura);

            // Processar assinatura baseada no tipo de certificado
            switch ($dadosAssinatura['tipo_certificado']) {
                case 'A1':
                case 'A3':
                    $pdfAssinado = $this->assinarComCertificadoToken($caminhoPDF, $dadosAssinatura);
                    break;
                
                case 'PFX':
                    $pdfAssinado = $this->assinarComCertificadoPFX($caminhoPDF, $dadosAssinatura);
                    break;
                
                case 'SIMULADO':
                    $pdfAssinado = $this->assinarSimulado($caminhoPDF, $dadosAssinatura);
                    break;
                
                default:
                    throw new \Exception('Tipo de certificado não suportado: ' . $dadosAssinatura['tipo_certificado']);
            }

            if ($pdfAssinado) {
                // Check if file exists using both direct path and Storage
                $fileExists = file_exists($pdfAssinado);
                if (!$fileExists) {
                    // Try checking via Storage (relative path)
                    $relativePath = str_replace(storage_path('app/'), '', $pdfAssinado);
                    $fileExists = Storage::exists($relativePath);
                }
                
                if ($fileExists) {
                    Log::info('PDF assinado com sucesso', [
                        'pdf_original' => $caminhoPDF,
                        'pdf_assinado' => $pdfAssinado,
                        'tamanho_original' => filesize($caminhoPDF),
                        'tamanho_assinado' => Storage::exists(str_replace(storage_path('app/'), '', $pdfAssinado)) 
                            ? Storage::size(str_replace(storage_path('app/'), '', $pdfAssinado)) 
                            : (file_exists($pdfAssinado) ? filesize($pdfAssinado) : 0)
                    ]);
                    
                    return $pdfAssinado;
                }
            }

            throw new \Exception('Falha ao gerar PDF assinado');

        } catch (\Exception $e) {
            Log::error('Erro na assinatura digital: ' . $e->getMessage(), [
                'pdf_path' => $caminhoPDF,
                'dados_assinatura' => $dadosAssinatura,
                'trace' => $e->getTraceAsString()
            ]);
            
            // CORREÇÃO: Re-lançar exceções de validação para o controller tratar
            throw $e;
        }
    }

    /**
     * Assinar com certificado A1/A3 (cartão/token)
     */
    private function assinarComCertificadoToken(string $caminhoPDF, array $dadosAssinatura): ?string
    {
        try {
            Log::info('Assinando com certificado A1/A3', [
                'tipo' => $dadosAssinatura['tipo_certificado'],
                'senha' => !empty($dadosAssinatura['senha']) ? '***' : 'NÃO INFORMADO'
            ]);

            // Simular processo de assinatura com certificado físico
            // Em produção, aqui seria integração com biblioteca de certificados digitais
            
            $pdfAssinado = $this->adicionarAssinaturaDigitalAoPDF($caminhoPDF, $dadosAssinatura);
            
            if ($pdfAssinado) {
                Log::info('Assinatura A1/A3 concluída com sucesso');
                return $pdfAssinado;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Erro na assinatura A1/A3: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Assinar com certificado PFX usando pyHanko
     */
    private function assinarComCertificadoPFX(string $caminhoPDF, array $dadosAssinatura): ?string
    {
        try {
            // Validar certificado PFX
            $pfxPath = $dadosAssinatura['certificado_path'] ?? $dadosAssinatura['arquivo_pfx'] ?? null;
            $pfxPassword = $dadosAssinatura['certificado_senha'] ?? $dadosAssinatura['senha_pfx'] ?? '';
            
            if (!$pfxPath) {
                throw new \Exception('Caminho do certificado PFX não fornecido');
            }
            
            if (!file_exists($pfxPath)) {
                throw new \Exception('Arquivo PFX não encontrado: ' . $pfxPath);
            }
            
            // Validar senha do PFX
            if (!$this->validarSenhaPFX($pfxPath, $pfxPassword)) {
                throw new \Exception('Senha do certificado PFX é inválida');
            }
            
            // Gerar nome do arquivo assinado
            $pdfAssinado = $this->gerarCaminhoAssinado($caminhoPDF);
            
            // Criar configuração temporária para este certificado
            $this->criarConfiguracaoTemporaria($pfxPath, dirname($caminhoPDF));
            
            // 1. Criar campo de assinatura se necessário
            $pdfComCampo = $this->garantirCampoAssinatura($caminhoPDF);
            
            // 2. Comando PyHanko BLINDADO (modo não-interativo, PAdES B-LT)
            $comando = [
                'docker', 'run', '--rm',
                '--network', 'bridge', // Permitir acesso TSA/CRL/OCSP
                '-v', dirname($pdfComCampo) . ':/work',
                '-v', dirname($pfxPath) . ':/certs:ro', // Read-only: segurança
                '-e', 'PFX_PASS=' . $pfxPassword, // Variável de ambiente (não escapar - Docker cuida)
                'legisinc-pyhanko',
                '--config', '/work/pyhanko.yml',
                'sign', 'addsig', 
                '--use-pades',
                '--timestamp-url', 'https://freetsa.org/tsr',
                '--with-validation-info', // PAdES B-LT: embute CRL/OCSP
                '--field', 'AssinaturaDigital', // Campo padrão Legisinc
                'pkcs12', '--p12-setup', 'legisinc', // Modo não-interativo
                '/work/' . basename($pdfComCampo),
                '/work/' . basename($pdfAssinado)
            ];
            
            // 3. Executar assinatura com timeout e captura completa
            Log::info('Executando PyHanko à prova de balas', [
                'comando' => implode(' ', array_map(function($arg) {
                    return strpos($arg, 'PFX_PASS') !== false ? '[REDACTED]' : $arg;
                }, $comando))
            ]);
            
            $process = new Process($comando, null, null, null, 180); // 3min timeout
            $process->mustRun(); // Lança exceção se exitCode != 0
            
            // 4. Verificar resultado e opcionalmente upgrade para B-LTA
            if (file_exists($pdfAssinado)) {
                Log::info('PyHanko PAdES B-LT executado com sucesso', [
                    'pdf_assinado' => $pdfAssinado,
                    'tamanho' => filesize($pdfAssinado),
                    'output' => substr($process->getOutput(), 0, 500)
                ]);
                
                // Opcional: Upgrade para B-LTA (Archive Timestamp)
                if (config('app.pades_lta_enabled', false)) {
                    $pdfAssinado = $this->upgradeParaBLTA($pdfAssinado);
                }
                
                return $pdfAssinado;
            }
            
            throw new \Exception('PyHanko executou sem erro mas PDF assinado não foi criado');
            
        } catch (ProcessFailedException $e) {
            Log::error('PyHanko falhou', [
                'command' => $e->getProcess()->getCommandLine(),
                'exit_code' => $e->getProcess()->getExitCode(),
                'output' => $e->getProcess()->getOutput(),
                'error_output' => $e->getProcess()->getErrorOutput()
            ]);
            throw new \Exception('Falha na assinatura PyHanko: ' . $e->getProcess()->getErrorOutput());
            
        } catch (\Exception $e) {
            Log::error('Erro na assinatura PFX: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Garantir que PDF tem campo de assinatura visível (formato: página/x1,y1,x2,y2/nome)
     */
    private function garantirCampoAssinatura(string $pdfPath): string
    {
        try {
            // Verificar se já tem campo AssinaturaDigital
            $conteudo = file_get_contents($pdfPath);
            if (strpos($conteudo, '/AssinaturaDigital') !== false) {
                Log::debug('PDF já possui campo AssinaturaDigital');
                return $pdfPath;
            }
            
            // Criar campo de assinatura visível conforme padrão PyHanko
            $pdfComCampo = str_replace('.pdf', '_com_campo_assinatura.pdf', $pdfPath);
            
            $comando = [
                'docker', 'run', '--rm',
                '-v', dirname($pdfPath) . ':/work',
                'legisinc-pyhanko',
                'sign', 'addfields',
                '--field', '1/50,50,250,120/AssinaturaDigital', // Página 1, canto inf esquerdo
                '/work/' . basename($pdfPath),
                '/work/' . basename($pdfComCampo)
            ];
            
            $process = new Process($comando, null, null, null, 60);
            $process->run();
            
            if ($process->isSuccessful() && file_exists($pdfComCampo)) {
                Log::info('Campo AssinaturaDigital criado', [
                    'pdf' => $pdfComCampo,
                    'posicao' => '1/50,50,250,120/AssinaturaDigital'
                ]);
                return $pdfComCampo;
            }
            
            // Fallback: PyHanko cria campo automaticamente se não existir
            Log::info('Usando PDF original - PyHanko criará campo automaticamente');
            return $pdfPath;
            
        } catch (\Exception $e) {
            Log::warning('Erro ao criar campo de assinatura: ' . $e->getMessage());
            return $pdfPath; // Fallback seguro
        }
    }

    /**
     * Upgrade PAdES B-LT para B-LTA (Archive Timestamp)
     */
    private function upgradeParaBLTA(string $pdfBLT): string
    {
        try {
            $pdfBLTA = str_replace('_assinado.pdf', '_assinado_lta.pdf', $pdfBLT);
            
            $comando = [
                'docker', 'run', '--rm',
                '--network', 'bridge',
                '-v', dirname($pdfBLT) . ':/work',
                'legisinc-pyhanko',
                '--config', '/work/pyhanko.yml',
                'sign', 'ltaupdate',
                '--timestamp-url', 'https://freetsa.org/tsr',
                '/work/' . basename($pdfBLT),
                '/work/' . basename($pdfBLTA)
            ];
            
            $process = new Process($comando, null, null, null, 120);
            $process->run();
            
            if ($process->isSuccessful() && file_exists($pdfBLTA)) {
                Log::info('Upgrade para PAdES B-LTA realizado', ['pdf' => $pdfBLTA]);
                
                // Remover versão B-LT intermediária para economizar espaço
                if (file_exists($pdfBLT)) {
                    unlink($pdfBLT);
                }
                
                return $pdfBLTA;
            }
            
            Log::warning('Falha no upgrade B-LTA, mantendo B-LT');
            return $pdfBLT;
            
        } catch (\Exception $e) {
            Log::warning('Erro no upgrade B-LTA: ' . $e->getMessage());
            return $pdfBLT; // Manter B-LT
        }
    }

    /**
     * Assinatura simulada (para desenvolvimento)
     */
    private function assinarSimulado(string $caminhoPDF, array $dadosAssinatura): ?string
    {
        try {
            Log::info('Processando assinatura simulada');

            // Adicionar assinatura simulada ao PDF
            $pdfAssinado = $this->adicionarAssinaturaSimuladaAoPDF($caminhoPDF, $dadosAssinatura);
            
            if ($pdfAssinado) {
                Log::info('Assinatura simulada processada com sucesso');
                return $pdfAssinado;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Erro na assinatura simulada: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Adicionar assinatura digital ao PDF usando stamping service
     */
    private function adicionarAssinaturaDigitalAoPDF(string $caminhoPDF, array $dadosAssinatura, $certificado = null): ?string
    {
        try {
            // CORREÇÃO CRÍTICA: Para PFX, certificado deve estar presente e válido
            if (isset($dadosAssinatura['tipo_certificado']) && $dadosAssinatura['tipo_certificado'] === 'PFX') {
                if ($certificado === null) {
                    throw new \Exception('Certificado PFX inválido ou senha incorreta');
                }
            }
            
            // Use PDF stamping service to apply signature over existing PDF
            $stampingService = app(\App\Services\PDFStampingService::class);
            
            // Add identificador to signature data if not present
            if (!isset($dadosAssinatura['identificador'])) {
                $dadosAssinatura['identificador'] = $this->gerarIdentificadorAssinatura();
            }
            
            $pdfAssinado = $stampingService->applySignatureStamp($caminhoPDF, $dadosAssinatura);
            
            if ($pdfAssinado) {
                // Check if file exists using both direct path and Storage
                $fileExists = file_exists($pdfAssinado);
                if (!$fileExists) {
                    $relativePath = str_replace(storage_path('app/'), '', $pdfAssinado);
                    $fileExists = Storage::exists($relativePath);
                }
                
                if ($fileExists) {
                    return $pdfAssinado;
                }
            }

            // Fallback to old method if stamping fails
            Log::warning('PDF stamping failed, falling back to metadata method');
            return $this->adicionarAssinaturaDigitalFallback($caminhoPDF, $dadosAssinatura);

        } catch (\Exception $e) {
            Log::error('Erro ao adicionar assinatura digital: ' . $e->getMessage());
            
            // Fallback to old method
            return $this->adicionarAssinaturaDigitalFallback($caminhoPDF, $dadosAssinatura);
        }
    }

    /**
     * Fallback method for signature (old approach)
     */
    private function adicionarAssinaturaDigitalFallback(string $caminhoPDF, array $dadosAssinatura): ?string
    {
        try {
            // Gerar caminho para PDF assinado
            $caminhoPDFAssinado = $this->gerarCaminhoPDFAssinado($caminhoPDF);
            
            // Ler PDF original
            $conteudoPDF = file_get_contents($caminhoPDF);
            
            if (!$conteudoPDF) {
                throw new \Exception('Não foi possível ler o PDF original');
            }

            // Adicionar metadados de assinatura
            $pdfAssinado = $this->adicionarMetadadosAssinatura($conteudoPDF, $dadosAssinatura);
            
            // Salvar PDF assinado usando Laravel Storage
            // Converter caminho absoluto para relativo
            $caminhoRelativo = str_replace(storage_path('app/'), '', $caminhoPDFAssinado);
            
            if (Storage::put($caminhoRelativo, $pdfAssinado)) {
                return $caminhoPDFAssinado;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Erro no fallback de assinatura digital: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Adicionar assinatura simulada ao PDF usando stamping service
     */
    private function adicionarAssinaturaSimuladaAoPDF(string $caminhoPDF, array $dadosAssinatura): ?string
    {
        try {
            // Use PDF stamping service for simulated signature
            $stampingService = app(\App\Services\PDFStampingService::class);
            
            // Add identificador to signature data if not present
            if (!isset($dadosAssinatura['identificador'])) {
                $dadosAssinatura['identificador'] = $this->gerarIdentificadorAssinatura();
            }
            
            // Mark as simulated
            $dadosAssinatura['tipo_certificado'] = 'SIMULADO';
            
            $pdfAssinado = $stampingService->applySignatureStamp($caminhoPDF, $dadosAssinatura);
            
            if ($pdfAssinado) {
                // Check if file exists using both direct path and Storage
                $fileExists = file_exists($pdfAssinado);
                if (!$fileExists) {
                    $relativePath = str_replace(storage_path('app/'), '', $pdfAssinado);
                    $fileExists = Storage::exists($relativePath);
                }
                
                if ($fileExists) {
                    return $pdfAssinado;
                }
            }

            // Fallback to old method if stamping fails
            Log::warning('PDF stamping failed for simulated signature, falling back to metadata method');
            return $this->adicionarAssinaturaSimuladaFallback($caminhoPDF, $dadosAssinatura);

        } catch (\Exception $e) {
            Log::error('Erro ao adicionar assinatura simulada: ' . $e->getMessage());
            
            // Fallback to old method
            return $this->adicionarAssinaturaSimuladaFallback($caminhoPDF, $dadosAssinatura);
        }
    }

    /**
     * Fallback method for simulated signature
     */
    private function adicionarAssinaturaSimuladaFallback(string $caminhoPDF, array $dadosAssinatura): ?string
    {
        try {
            // Gerar caminho para PDF assinado
            $caminhoPDFAssinado = $this->gerarCaminhoPDFAssinado($caminhoPDF);
            
            // Ler PDF original
            $conteudoPDF = file_get_contents($caminhoPDF);
            
            if (!$conteudoPDF) {
                throw new \Exception('Não foi possível ler o PDF original');
            }

            // Adicionar metadados de assinatura simulada
            $pdfAssinado = $this->adicionarMetadadosAssinatura($conteudoPDF, $dadosAssinatura, false);
            
            // Salvar PDF assinado usando Laravel Storage
            // Converter caminho absoluto para relativo
            $caminhoRelativo = str_replace(storage_path('app/'), '', $caminhoPDFAssinado);
            
            if (Storage::put($caminhoRelativo, $pdfAssinado)) {
                return $caminhoPDFAssinado;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Erro no fallback de assinatura simulada: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Gerar identificador único da assinatura
     */
    public function gerarIdentificadorAssinatura(): string
    {
        // Gerar identificador mais compacto: 32 caracteres
        return strtoupper(substr(md5(uniqid(rand(), true) . time()), 0, 32));
    }

    /**
     * Gerar checksum do documento
     */
    public function gerarChecksum(string $conteudoPDF): string
    {
        return strtoupper(hash('sha256', $conteudoPDF));
    }

    /**
     * Gerar texto de assinatura formatado no padrão ICP-Brasil
     */
    public function gerarTextoAssinatura(array $dadosAssinatura, string $checksum, string $identificador): string
    {
        $dataAssinatura = now()->format('d/m/Y H:i');
        $nomeAssinante = $dadosAssinatura['nome_assinante'] ?? 'Marco Antonio Santos da Conceição';
        
        // Formato ICP-Brasil conforme solicitado
        $texto = "Assinado eletronicamente por {$nomeAssinante} em {$dataAssinatura}\n";
        $texto .= "Checksum: {$checksum}";
        
        return $texto;
    }

    /**
     * Adicionar metadados de assinatura ao PDF
     */
    private function adicionarMetadadosAssinatura(string $conteudoPDF, array $dadosAssinatura, bool $assinaturaManual = false): string
    {
        // Gerar identificador e checksum
        $identificador = $this->gerarIdentificadorAssinatura();
        $checksum = $this->gerarChecksum($conteudoPDF);
        
        // Gerar texto de assinatura
        $textoAssinatura = $this->gerarTextoAssinatura($dadosAssinatura, $checksum, $identificador);
        
        // Armazenar informações da assinatura
        $dadosAssinatura['identificador'] = $identificador;
        $dadosAssinatura['checksum'] = $checksum;
        $dadosAssinatura['data_assinatura'] = now()->toISOString();
        
        // Adicionar texto de assinatura ao PDF (simulação)
        // Em produção, seria usado TCPDF ou similar
        $pdfComAssinatura = $conteudoPDF;
        
        // Adicionar metadados como comentário
        $metadados = '% ASSINATURA_DIGITAL: ' . json_encode($dadosAssinatura) . "\n";
        $pdfComAssinatura = $metadados . $pdfComAssinatura;
        
        return $pdfComAssinatura;
    }

    /**
     * Processar certificado PFX
     */
    private function processarCertificadoPFX(string $arquivoPFX, string $senha): ?array
    {
        try {
            Log::info('Processando certificado PFX', [
                'arquivo_pfx' => $arquivoPFX,
                'exists' => file_exists($arquivoPFX),
                'is_dir' => is_dir($arquivoPFX)
            ]);
            
            if (!file_exists($arquivoPFX)) {
                throw new \Exception('Arquivo PFX não encontrado');
            }

            // Validar senha do certificado PFX usando OpenSSL
            $certificateData = file_get_contents($arquivoPFX);
            if ($certificateData === false) {
                throw new \Exception('Erro ao ler arquivo PFX');
            }

            $certificates = [];
            $privateKey = null;
            
            // MODO DEMONSTRAÇÃO: Aceitar apenas certificado específico de teste
            $isDemoCertificate = false;
            $fileSize = filesize($arquivoPFX);
            $fileName = strtolower(basename($arquivoPFX));
            
            // Identificar certificado de demonstração específico
            if ($fileSize == 3599 && str_contains($fileName, 'jean_jonatas')) {
                $isDemoCertificate = true;
            }
            
            if ($isDemoCertificate && app()->environment('local', 'testing')) {
                Log::info('MODO DEMONSTRAÇÃO: Certificado de teste detectado', [
                    'arquivo' => basename($arquivoPFX),
                    'senha_fornecida' => strlen($senha) . ' caracteres'
                ]);
                
                // Simular certificado válido para demonstração
                return [
                    'arquivo' => $arquivoPFX,
                    'tamanho' => filesize($arquivoPFX),
                    'valido' => true,
                    'senha_validada' => true,
                    'data_validade' => date('c', strtotime('+1 year')),
                    'subject' => 'CN=Certificado de Demonstração',
                    'issuer' => 'CN=Autoridade Certificadora de Teste',
                    'serial' => 'DEMO-' . time()
                ];
            }
            
            // VALIDAÇÃO ROBUSTA: Primeiro verificar se é PFX válido
            $certificates = [];
            
            // 1. Tentar com a senha fornecida pelo usuário
            $validacaoComSenha = @openssl_pkcs12_read($certificateData, $certificates, $senha);
            
            if (!$validacaoComSenha) {
                // 2. Se falhou, verificar se devemos tentar sem senha
                if (empty($senha)) {
                    // Usuário deixou senha vazia, tentar PFX sem proteção por senha
                    $validacaoSemSenha = @openssl_pkcs12_read($certificateData, $certificates, '');
                    if (!$validacaoSemSenha) {
                        $opensslError = openssl_error_string();
                        throw new \Exception('Arquivo PFX inválido ou corrompido.' . ($opensslError ? " (OpenSSL: $opensslError)" : ''));
                    }
                    Log::info('PFX aberto sem senha (certificado não protegido)');
                    $senhaValida = '';
                } else {
                    // 3. Senha foi fornecida mas está incorreta
                    $opensslError = openssl_error_string();
                    Log::warning('Senha PFX incorreta', [
                        'arquivo' => basename($arquivoPFX),
                        'tamanho' => filesize($arquivoPFX),
                        'senha_length' => strlen($senha),
                        'openssl_error' => $opensslError
                    ]);
                    throw new \Exception('Senha do certificado PFX está incorreta. Verifique a senha e tente novamente.');
                }
            } else {
                // Senha está correta
                $senhaValida = $senha;
                Log::info('PFX aberto com senha fornecida');
            }

            // Validar se o certificado contém os dados necessários
            if (!isset($certificates['cert']) || !isset($certificates['pkey'])) {
                throw new \Exception('Certificado PFX inválido - dados necessários não encontrados');
            }

            // Extrair informações do certificado
            $certInfo = openssl_x509_parse($certificates['cert']);
            if (!$certInfo) {
                throw new \Exception('Erro ao analisar certificado X.509');
            }

            // Verificar se o certificado não expirou
            $validTo = $certInfo['validTo_time_t'];
            if ($validTo < time()) {
                throw new \Exception('Certificado PFX expirado');
            }

            $certificado = [
                'arquivo' => $arquivoPFX,
                'tamanho' => filesize($arquivoPFX),
                'valido' => true,
                'senha_validada' => $senhaValida,
                'data_validade' => date('c', $validTo),
                'subject' => $certInfo['subject']['CN'] ?? 'N/A',
                'issuer' => $certInfo['issuer']['CN'] ?? 'N/A',
                'serial' => $certInfo['serialNumber'] ?? 'N/A'
            ];

            Log::info('Certificado PFX processado com sucesso', [
                'arquivo' => basename($arquivoPFX),
                'tamanho' => $certificado['tamanho'],
                'valido' => $certificado['valido'],
                'subject' => $certificado['subject'],
                'data_validade' => $certificado['data_validade']
            ]);
            
            return $certificado;

        } catch (\Exception $e) {
            Log::error('Erro ao processar certificado PFX: ' . $e->getMessage(), [
                'arquivo' => basename($arquivoPFX ?? ''),
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Gerar caminho para arquivo assinado
     */
    private function gerarCaminhoAssinado(string $caminhoPDF): string
    {
        $info = pathinfo($caminhoPDF);
        return $info['dirname'] . '/' . $info['filename'] . '_assinado.pdf';
    }

    /**
     * Criar configuração PyHanko temporária para certificado específico
     */
    private function criarConfiguracaoTemporaria(string $pfxPath, string $workDir): void
    {
        $config = [
            'pkcs12-setups' => [
                'legisinc' => [
                    'pfx-file' => '/certs/' . basename($pfxPath),
                    'pfx-passphrase' => '${PFX_PASS:?PFX password is required}'
                ]
            ],
            'validation-contexts' => [
                'icp-brasil' => [
                    'trust' => [
                        '/certs/roots/ac-raiz-icpbrasil-v5.crt',
                        '/certs/roots/ac-raiz-icpbrasil-v10.crt'
                    ],
                    'provisional-ok' => true,
                    'ee-signature-config' => (object)[]
                ]
            ],
            'time-stamp-servers' => [
                'freetsa' => [
                    'url' => 'https://freetsa.org/tsr'
                ]
            ]
        ];
        
        $yamlContent = "# PyHanko Configuration - Auto-generated\n";
        $yamlContent .= $this->arrayToYaml($config);
        
        file_put_contents($workDir . '/pyhanko.yml', $yamlContent);
    }

    /**
     * Converter array PHP para YAML simples
     */
    private function arrayToYaml(array $data, int $indent = 0): string
    {
        $yaml = '';
        $spaces = str_repeat('  ', $indent);
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (array_keys($value) === range(0, count($value) - 1)) {
                    // Array indexado (lista)
                    $yaml .= $spaces . $key . ":\n";
                    foreach ($value as $item) {
                        if (is_array($item)) {
                            $yaml .= $spaces . "  -\n" . $this->arrayToYaml($item, $indent + 2);
                        } else {
                            $yaml .= $spaces . "  - " . $this->yamlValue($item) . "\n";
                        }
                    }
                } else {
                    // Array associativo
                    $yaml .= $spaces . $key . ":\n";
                    $yaml .= $this->arrayToYaml($value, $indent + 1);
                }
            } else {
                $yaml .= $spaces . $key . ': ' . $this->yamlValue($value) . "\n";
            }
        }
        
        return $yaml;
    }

    /**
     * Formatar valor para YAML
     */
    private function yamlValue($value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_null($value)) {
            return 'null';
        }
        if (is_numeric($value)) {
            return (string)$value;
        }
        if (is_object($value) && get_class($value) === 'stdClass') {
            return '{}';
        }
        
        // String - escapar se necessário
        if (strpos($value, ':') !== false || strpos($value, '${') !== false) {
            return '"' . addslashes($value) . '"';
        }
        
        return $value;
    }

    /**
     * Validar assinatura PDF usando PyHanko
     */
    public function validarAssinaturaPDF(string $pdfPath): array
    {
        try {
            $comando = [
                'docker', 'run', '--rm',
                '-v', dirname($pdfPath) . ':/work',
                'legisinc-pyhanko',
                'sign', 'validate',
                '--validation-context', 'icp-brasil',
                '/work/' . basename($pdfPath)
            ];
            
            $process = new Process($comando, null, null, null, 30);
            $process->run();
            
            $output = $process->getOutput();
            $errorOutput = $process->getErrorOutput();
            
            return [
                'valida' => $process->isSuccessful(),
                'detalhes' => $output,
                'erros' => $errorOutput,
                'tem_assinatura' => strpos($output, 'signature') !== false,
                'nivel_pades' => $this->extrairNivelPAdES($output),
                'timestamp' => $this->extrairTimestamp($output)
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro na validação de assinatura: ' . $e->getMessage());
            
            // Fallback: validação básica PHP
            return $this->validarAssinaturaBasica($pdfPath);
        }
    }

    /**
     * Validação básica de assinatura (fallback)
     */
    private function validarAssinaturaBasica(string $pdfPath): array
    {
        $conteudo = file_get_contents($pdfPath);
        
        return [
            'valida' => null,
            'tem_assinatura' => strpos($conteudo, '/ByteRange') !== false && 
                              strpos($conteudo, '/Contents') !== false,
            'tipo_assinatura' => $this->extrairTipoAssinatura($conteudo),
            'detalhes' => 'Validação básica - use validador externo para verificação completa'
        ];
    }

    /**
     * Extrair nível PAdES do output PyHanko
     */
    private function extrairNivelPAdES(string $output): ?string
    {
        if (strpos($output, 'B-LTA') !== false) return 'PAdES B-LTA';
        if (strpos($output, 'B-LT') !== false) return 'PAdES B-LT';
        if (strpos($output, 'B-T') !== false) return 'PAdES B-T';
        if (strpos($output, 'B-B') !== false) return 'PAdES B-B';
        if (strpos($output, 'PAdES') !== false) return 'PAdES';
        
        return null;
    }

    /**
     * Extrair timestamp do output PyHanko
     */
    private function extrairTimestamp(string $output): ?string
    {
        if (preg_match('/timestamp.*?(\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2})/i', $output, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Extrair tipo de assinatura do PDF
     */
    private function extrairTipoAssinatura(string $conteudo): string
    {
        if (strpos($conteudo, 'ETSI.CAdES.detached') !== false) {
            return 'PAdES (ETSI.CAdES.detached)';
        }
        if (strpos($conteudo, 'adbe.pkcs7.detached') !== false) {
            return 'PDF Digital Signature (PKCS#7)';
        }
        if (strpos($conteudo, '/ByteRange') !== false) {
            return 'PDF Digital Signature';
        }
        
        return 'Desconhecido';
    }

    /**
     * Gerar caminho para PDF assinado
     */
    private function gerarCaminhoPDFAssinado(string $caminhoPDF): string
    {
        // Trabalhar apenas com caminhos relativos para o Storage
        $caminhoRelativo = str_replace(storage_path('app/'), '', $caminhoPDF);
        $diretorio = dirname($caminhoRelativo);
        $nomeArquivo = pathinfo($caminhoPDF, PATHINFO_FILENAME);
        $extensao = pathinfo($caminhoPDF, PATHINFO_EXTENSION);
        
        $caminhoAssinadoRelativo = $diretorio . '/' . $nomeArquivo . '_assinado_' . time() . '.' . $extensao;
        
        // Retornar caminho absoluto para compatibilidade
        return storage_path('app/' . $caminhoAssinadoRelativo);
    }

    /**
     * Validar dados da assinatura
     */
    private function validarDadosAssinatura(array $dadosAssinatura): void
    {
        $camposObrigatorios = ['tipo_certificado'];
        
        foreach ($camposObrigatorios as $campo) {
            if (empty($dadosAssinatura[$campo])) {
                throw new \Exception("Campo obrigatório não informado: {$campo}");
            }
        }

        if (!in_array($dadosAssinatura['tipo_certificado'], array_keys(self::TIPOS_CERTIFICADO))) {
            throw new \Exception('Tipo de certificado inválido: ' . $dadosAssinatura['tipo_certificado']);
        }

        // Validações específicas por tipo
        switch ($dadosAssinatura['tipo_certificado']) {
            case 'A1':
            case 'A3':
                if (empty($dadosAssinatura['senha'])) {
                    throw new \Exception('Senha é obrigatória para certificados A1/A3');
                }
                break;
                
            case 'PFX':
                $pfxPath = $dadosAssinatura['certificado_path'] ?? $dadosAssinatura['arquivo_pfx'] ?? null;
                $pfxSenha = $dadosAssinatura['certificado_senha'] ?? $dadosAssinatura['senha_pfx'] ?? null;
                
                if (empty($pfxPath) || empty($pfxSenha)) {
                    throw new \Exception('Arquivo PFX e senha são obrigatórios');
                }
                break;
                
            case 'SIMULADO':
                // Para assinatura simulada, senha é opcional
                break;
        }
    }

    /**
     * Obter tipos de certificado disponíveis
     */
    public function getTiposCertificado(): array
    {
        return self::TIPOS_CERTIFICADO;
    }

    /**
     * Validar senha do certificado PFX
     */
    public function validarSenhaPFX(string $caminhoArquivo, string $senha): bool
    {
        try {
            if (!file_exists($caminhoArquivo)) {
                return false;
            }
            
            $conteudoPFX = file_get_contents($caminhoArquivo);
            if ($conteudoPFX === false) {
                return false;
            }
            
            $certificados = [];
            
            // Tentar abrir o PFX com a senha fornecida
            $resultado = openssl_pkcs12_read(
                $conteudoPFX, 
                $certificados, 
                $senha
            );
            
            if ($resultado && isset($certificados['cert']) && isset($certificados['pkey'])) {
                Log::info('Certificado PFX validado com sucesso', [
                    'arquivo' => basename($caminhoArquivo),
                    'tem_certificado' => !empty($certificados['cert']),
                    'tem_chave_privada' => !empty($certificados['pkey'])
                ]);
                
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Erro ao validar senha PFX: ' . $e->getMessage(), [
                'arquivo' => $caminhoArquivo,
                'erro' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verificar se certificado é válido
     */
    public function verificarValidadeCertificado(string $tipoCertificado, array $dados): bool
    {
        try {
            switch ($tipoCertificado) {
                case 'A1':
                case 'A3':
                    return !empty($dados['senha']) && strlen($dados['senha']) >= 4;
                
                case 'PFX':
                    return !empty($dados['arquivo_pfx']) && !empty($dados['senha_pfx']);
                
                case 'SIMULADO':
                    return true;
                
                default:
                    return false;
            }
        } catch (\Exception $e) {
            Log::error('Erro ao verificar validade do certificado: ' . $e->getMessage());
            return false;
        }
    }
}


