<?php

namespace App\Services;

class QRCodeService
{
    /**
     * Gerar URL de consulta para uma proposição
     */
    public function gerarUrlConsulta(int $proposicaoId): string
    {
        return route('proposicoes.consulta.publica', ['id' => $proposicaoId]);
    }
    
    /**
     * Gerar QR Code usando API do Google Charts (simples e rápido)
     */
    public function gerarQRCodeUrl(string $texto, int $tamanho = 150): string
    {
        $textoEncoded = urlencode($texto);
        return "https://chart.googleapis.com/chart?chs={$tamanho}x{$tamanho}&cht=qr&chl={$textoEncoded}";
    }
    
    /**
     * Gerar QR Code para proposição
     */
    public function gerarQRCodeProposicao(int $proposicaoId, int $tamanho = 80): string
    {
        $url = $this->gerarUrlConsulta($proposicaoId);
        return $this->gerarQRCodeUrl($url, $tamanho);
    }
}