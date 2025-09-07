<?php

namespace App\Services;

use App\Models\Proposicao;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProtocoloRTFService
{
    /**
     * Atualiza o RTF original com o número de protocolo
     * substituindo [AGUARDANDO PROTOCOLO] ou ${numero_proposicao}
     */
    public function atualizarRTFComProtocolo(Proposicao $proposicao, string $numeroProtocolo): bool
    {
        try {
            // Obter o arquivo RTF atual
            $rtfPath = $proposicao->arquivo_path;
            
            // Verificar se o arquivo RTF existe
            if (!$rtfPath) {
                Log::error('RTF path não encontrado para protocolo', [
                    'proposicao_id' => $proposicao->id,
                    'arquivo_path' => $rtfPath
                ]);
                return false;
            }
            
            // Check if RTF exists using both Storage and direct file access
            $rtfFullPath = storage_path('app/' . $rtfPath);
            if (!Storage::exists($rtfPath) && !file_exists($rtfFullPath)) {
                Log::error('RTF não encontrado para atualização de protocolo', [
                    'proposicao_id' => $proposicao->id,
                    'rtf_path' => $rtfPath,
                    'full_path' => $rtfFullPath,
                    'storage_exists' => Storage::exists($rtfPath),
                    'file_exists' => file_exists($rtfFullPath)
                ]);
                return false;
            }

            // Ler o conteúdo RTF (use file_get_contents if Storage fails)
            $conteudoRTF = Storage::exists($rtfPath) ? Storage::get($rtfPath) : file_get_contents($rtfFullPath);
            
            // Substituir as variáveis de protocolo com mais padrões
            $substituicoes = [
                '[AGUARDANDO PROTOCOLO]' => $numeroProtocolo,
                '\\[AGUARDANDO PROTOCOLO\\]' => $numeroProtocolo,
                '${numero_proposicao}' => $numeroProtocolo,
                '\\$\\{numero_proposicao\\}' => $numeroProtocolo,
                '$numero_proposicao' => $numeroProtocolo,
                'numero_proposicao' => $numeroProtocolo,
                // Formatos adicionais que podem aparecer em RTF processado pelo OnlyOffice
                '$\\{numero_proposicao\\}' => $numeroProtocolo,
                '\\$\\{numero\\\_proposicao\\}' => $numeroProtocolo,
                // Padrões específicos de protocolo
                '${numero_protocolo}' => $numeroProtocolo,
                '$numero_protocolo' => $numeroProtocolo,
                'numero_protocolo' => $numeroProtocolo,
                '\\$\\{numero_protocolo\\}' => $numeroProtocolo,
                // Formatação visual comum
                'N° [AGUARDANDO PROTOCOLO]' => 'N° ' . $numeroProtocolo,
                'Nº [AGUARDANDO PROTOCOLO]' => 'Nº ' . $numeroProtocolo,
                'No [AGUARDANDO PROTOCOLO]' => 'No ' . $numeroProtocolo,
            ];
            
            $conteudoAtualizado = $conteudoRTF;
            $totalSubstituicoes = 0;
            
            foreach ($substituicoes as $buscar => $substituir) {
                $antes = substr_count($conteudoAtualizado, $buscar);
                $conteudoAtualizado = str_replace($buscar, $substituir, $conteudoAtualizado);
                $depois = substr_count($conteudoAtualizado, $buscar);
                
                if ($antes > $depois) {
                    $totalSubstituicoes += ($antes - $depois);
                    Log::info("Substituição RTF protocolo: {$buscar} -> {$substituir}", [
                        'ocorrencias' => ($antes - $depois)
                    ]);
                }
            }
            
            // Se não encontrou nenhuma substituição direta, procurar padrões RTF
            if ($totalSubstituicoes == 0) {
                // Procurar por padrões RTF específicos
                $padroes = [
                    '/AGUARDANDO.*?PROTOCOLO/i',
                    '/Proposi.*?o.*?n.*?mero.*?\d*/i',
                ];
                
                foreach ($padroes as $padrao) {
                    if (preg_match($padrao, $conteudoAtualizado)) {
                        $conteudoAtualizado = preg_replace($padrao, $numeroProtocolo, $conteudoAtualizado, 1);
                        $totalSubstituicoes++;
                        Log::info("Substituição RTF por regex: {$padrao}", [
                            'numero_protocolo' => $numeroProtocolo
                        ]);
                        break;
                    }
                }
            }
            
            // Salvar o RTF atualizado com novo nome para preservar histórico
            $novoRTFPath = str_replace('.rtf', '_protocolado_' . time() . '.rtf', $rtfPath);
            Storage::put($novoRTFPath, $conteudoAtualizado);
            
            // Atualizar o caminho no banco
            $proposicao->update([
                'arquivo_path' => $novoRTFPath,
                'arquivo_rtf_protocolado' => $novoRTFPath
            ]);
            
            Log::info('RTF atualizado com protocolo', [
                'proposicao_id' => $proposicao->id,
                'numero_protocolo' => $numeroProtocolo,
                'substituicoes' => $totalSubstituicoes,
                'novo_arquivo' => $novoRTFPath
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar RTF com protocolo', [
                'proposicao_id' => $proposicao->id,
                'numero_protocolo' => $numeroProtocolo,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Gera novo PDF a partir do RTF atualizado com protocolo
     */
    public function gerarPDFProtocolado(Proposicao $proposicao): ?string
    {
        try {
            // Usar o serviço de conversão existente
            $conversionService = app(DocumentConversionService::class);
            
            // Obter o RTF atualizado
            $rtfPath = $proposicao->arquivo_path;
            
            // Check if RTF exists using multiple possible paths
            if (!$rtfPath) {
                throw new \Exception('Caminho do RTF não definido');
            }
            
            // Try multiple possible locations for the RTF file
            $rtfFullPath = null;
            $possiblePaths = [
                storage_path('app/' . $rtfPath),
                storage_path('app/private/' . $rtfPath),
                Storage::path($rtfPath),
            ];
            
            // Also check if Storage can find it
            if (Storage::exists($rtfPath)) {
                $rtfFullPath = Storage::path($rtfPath);
            } else {
                // Try each possible path
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $rtfFullPath = $path;
                        break;
                    }
                }
            }
            
            if (!$rtfFullPath || !file_exists($rtfFullPath)) {
                Log::error('RTF protocolado não encontrado em nenhum dos caminhos', [
                    'rtf_path' => $rtfPath,
                    'tentativas' => $possiblePaths,
                    'storage_exists' => Storage::exists($rtfPath)
                ]);
                throw new \Exception('RTF protocolado não encontrado: ' . $rtfPath);
            }
            
            // Gerar PDF a partir do RTF atualizado
            $pdfOutputPath = str_replace('.rtf', '.pdf', $rtfFullPath);
            
            $result = $conversionService->convertToPDF($rtfFullPath, $pdfOutputPath);
            
            $pdfPath = $result['success'] ? $pdfOutputPath : null;
            
            if ($pdfPath) {
                // Salvar com nome específico de protocolado
                $pdfProtocoladoPath = str_replace('.pdf', '_protocolado_' . time() . '.pdf', $pdfPath);
                
                if ($pdfPath !== $pdfProtocoladoPath) {
                    Storage::copy(
                        str_replace(storage_path('app/'), '', $pdfPath),
                        str_replace(storage_path('app/'), '', $pdfProtocoladoPath)
                    );
                }
                
                Log::info('PDF protocolado gerado', [
                    'proposicao_id' => $proposicao->id,
                    'pdf_path' => $pdfProtocoladoPath
                ]);
                
                return $pdfProtocoladoPath;
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar PDF protocolado', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}