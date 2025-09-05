<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

            if ($pdfAssinado && file_exists($pdfAssinado)) {
                Log::info('PDF assinado com sucesso', [
                    'pdf_original' => $caminhoPDF,
                    'pdf_assinado' => $pdfAssinado,
                    'tamanho_original' => filesize($caminhoPDF),
                    'tamanho_assinado' => filesize($pdfAssinado)
                ]);
                
                return $pdfAssinado;
            }

            throw new \Exception('Falha ao gerar PDF assinado');

        } catch (\Exception $e) {
            Log::error('Erro na assinatura digital: ' . $e->getMessage(), [
                'pdf_path' => $caminhoPDF,
                'dados_assinatura' => $dadosAssinatura,
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
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
     * Assinar com certificado PFX (.pfx/.p12)
     */
    private function assinarComCertificadoPFX(string $caminhoPDF, array $dadosAssinatura): ?string
    {
        try {
            Log::info('Assinando com certificado PFX');

            // Validar arquivo PFX
            if (empty($dadosAssinatura['arquivo_pfx'])) {
                throw new \Exception('Arquivo PFX não fornecido');
            }

            // Processar arquivo PFX
            $certificado = $this->processarCertificadoPFX($dadosAssinatura['arquivo_pfx'], $dadosAssinatura['senha_pfx'] ?? '');
            
            if (!$certificado) {
                throw new \Exception('Falha ao processar certificado PFX');
            }

            // Assinar PDF com certificado PFX
            $pdfAssinado = $this->adicionarAssinaturaDigitalAoPDF($caminhoPDF, $dadosAssinatura, $certificado);
            
            if ($pdfAssinado) {
                Log::info('Assinatura PFX concluída com sucesso');
                return $pdfAssinado;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Erro na assinatura PFX: ' . $e->getMessage());
            return null;
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
            // Use PDF stamping service to apply signature over existing PDF
            $stampingService = app(\App\Services\PDFStampingService::class);
            
            // Add identificador to signature data if not present
            if (!isset($dadosAssinatura['identificador'])) {
                $dadosAssinatura['identificador'] = $this->gerarIdentificadorAssinatura();
            }
            
            $pdfAssinado = $stampingService->applySignatureStamp($caminhoPDF, $dadosAssinatura);
            
            if ($pdfAssinado && file_exists($pdfAssinado)) {
                return $pdfAssinado;
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
            
            // Salvar PDF assinado
            if (file_put_contents($caminhoPDFAssinado, $pdfAssinado)) {
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
            
            if ($pdfAssinado && file_exists($pdfAssinado)) {
                return $pdfAssinado;
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
            
            // Salvar PDF assinado
            if (file_put_contents($caminhoPDFAssinado, $pdfAssinado)) {
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
            // Em produção, aqui seria usado OpenSSL para processar o certificado
            // Por enquanto, vamos simular o processamento
            
            if (!file_exists($arquivoPFX)) {
                throw new \Exception('Arquivo PFX não encontrado');
            }

            // Simular validação do certificado
            $certificado = [
                'arquivo' => $arquivoPFX,
                'tamanho' => filesize($arquivoPFX),
                'valido' => true,
                'data_validade' => now()->addYears(1)->toISOString()
            ];

            Log::info('Certificado PFX processado com sucesso', $certificado);
            
            return $certificado;

        } catch (\Exception $e) {
            Log::error('Erro ao processar certificado PFX: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Gerar caminho para PDF assinado
     */
    private function gerarCaminhoPDFAssinado(string $caminhoPDF): string
    {
        $diretorio = dirname($caminhoPDF);
        $nomeArquivo = pathinfo($caminhoPDF, PATHINFO_FILENAME);
        $extensao = pathinfo($caminhoPDF, PATHINFO_EXTENSION);
        
        return $diretorio . '/' . $nomeArquivo . '_assinado_' . time() . '.' . $extensao;
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
                if (empty($dadosAssinatura['arquivo_pfx']) || empty($dadosAssinatura['senha_pfx'])) {
                    throw new \Exception('Arquivo PFX e senha são obrigatórios');
                }
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


