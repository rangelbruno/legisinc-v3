<?php

namespace App\Services\Template;

use App\Models\Proposicao;

/**
 * Versão simplificada do AssinaturaQRService sem dependência de biblioteca externa
 * Foco em resolver a visualização de assinatura no PDF
 */
class AssinaturaQRServiceSimples
{
    /**
     * Gera o HTML da assinatura digital para PDF
     */
    public function gerarHTMLAssinatura(Proposicao $proposicao): ?string
    {
        try {
            // Verificar se proposição tem assinatura digital
            if (! $proposicao->assinatura_digital || ! $proposicao->data_assinatura) {
                return null;
            }

            // Apenas exibir após protocolo (seguindo regra do sistema)
            if (! $proposicao->numero_protocolo) {
                return null;
            }

            $autor = $proposicao->autor;
            $autorNome = $autor ? $autor->name : 'Autor não identificado';
            $dataAssinatura = $proposicao->data_assinatura->format('d/m/Y H:i:s');

            // Decodificar informações da assinatura se for JSON
            $infoAssinatura = '';
            if ($proposicao->assinatura_digital) {
                $dadosAssinatura = json_decode($proposicao->assinatura_digital, true);
                if (is_array($dadosAssinatura)) {
                    $infoAssinatura = $dadosAssinatura['assinatura'] ?? 'Assinatura válida';
                } else {
                    $infoAssinatura = $proposicao->assinatura_digital;
                }
            }

            // Gerar identificador de autenticidade
            $identificador = $this->gerarIdentificadorSimples($proposicao);

            return $this->gerarHTMLAssinaturaFormatado(
                $autorNome,
                $dataAssinatura,
                $infoAssinatura,
                $identificador,
                $proposicao->numero_protocolo
            );

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar HTML da assinatura', [
                'proposicao_id' => $proposicao->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Gerar identificador simples para autenticidade
     */
    private function gerarIdentificadorSimples(Proposicao $proposicao): string
    {
        $base = $proposicao->id.'_'.$proposicao->data_assinatura->timestamp;
        $hash = hash('md5', $base);

        return strtoupper(substr($hash, 0, 16));
    }

    /**
     * Gerar HTML formatado da assinatura para PDF
     */
    private function gerarHTMLAssinaturaFormatado(
        string $autorNome,
        string $dataAssinatura,
        string $infoAssinatura,
        string $identificador,
        string $numeroProtocolo
    ): string {
        return '
        <div class="assinatura-digital-pdf" style="
            width: 100%; 
            border: 3px solid #28a745; 
            padding: 20px; 
            margin: 30px 0; 
            background-color: #f0f8f0; 
            font-family: Arial, sans-serif; 
            page-break-inside: avoid; 
            text-align: center;
            border-radius: 10px;
        ">
            <h3 style="
                color: #28a745; 
                margin: 0 0 15px 0; 
                font-size: 16pt; 
                font-weight: bold;
                text-transform: uppercase;
            ">
                🏆 DOCUMENTO ASSINADO DIGITALMENTE
            </h3>
            
            <div style="
                font-size: 13pt; 
                line-height: 1.6; 
                color: #333; 
                margin-bottom: 15px;
            ">
                <strong>Assinado por:</strong> '.$autorNome.'<br>
                <strong>Cargo:</strong> Vereador<br>
                <strong>Data/Hora:</strong> '.$dataAssinatura.'<br>
                <strong>Protocolo:</strong> '.$numeroProtocolo.'
            </div>
            
            <div style="
                border-top: 2px solid #28a745; 
                padding-top: 15px; 
                font-size: 11pt; 
                color: #666;
            ">
                <strong>Identificador de Autenticidade:</strong> '.$identificador.'<br>
                <em>Documento assinado digitalmente conforme art. 4º, II da Lei 14.063/2020</em>
            </div>
        </div>';
    }

    /**
     * Gerar texto simples para substituição em templates
     */
    public function gerarTextoAssinatura(Proposicao $proposicao): string
    {
        if (! $proposicao->assinatura_digital || ! $proposicao->data_assinatura) {
            return '';
        }

        if (! $proposicao->numero_protocolo) {
            return '';
        }

        $autor = $proposicao->autor;
        $autorNome = $autor ? $autor->name : 'Autor não identificado';
        $dataAssinatura = $proposicao->data_assinatura->format('d/m/Y H:i:s');
        $identificador = $this->gerarIdentificadorSimples($proposicao);

        return "DOCUMENTO ASSINADO DIGITALMENTE\n".
               "Assinado por: {$autorNome}\n".
               "Cargo: Vereador\n".
               "Data/Hora: {$dataAssinatura}\n".
               "Protocolo: {$proposicao->numero_protocolo}\n".
               "Identificador: {$identificador}\n".
               'Conforme art. 4º, II da Lei 14.063/2020';
    }
}
