<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AnexoController extends Controller
{
    /**
     * Download de anexo da proposição
     */
    public function download(Proposicao $proposicao, $anexoIndex)
    {
        // Verificar se o usuário tem permissão para acessar a proposição
        if (!$this->canAccessProposicao($proposicao)) {
            abort(403, 'Você não tem permissão para acessar este anexo.');
        }
        
        // Verificar se a proposição tem anexos
        if (!$proposicao->anexos || !is_array($proposicao->anexos)) {
            abort(404, 'Esta proposição não possui anexos.');
        }
        
        // Verificar se o índice do anexo é válido
        if (!isset($proposicao->anexos[$anexoIndex])) {
            abort(404, 'Anexo não encontrado.');
        }
        
        $anexo = $proposicao->anexos[$anexoIndex];
        
        // Verificar se o arquivo existe no storage
        if (!Storage::disk('public')->exists($anexo['caminho'])) {
            abort(404, 'Arquivo não encontrado no servidor.');
        }
        
        // Obter o caminho completo do arquivo
        $filePath = Storage::disk('public')->path($anexo['caminho']);
        
        // Retornar o arquivo para download
        return response()->download(
            $filePath,
            $anexo['nome_original'],
            [
                'Content-Type' => $anexo['tipo'] ?? 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $anexo['nome_original'] . '"'
            ]
        );
    }
    
    /**
     * Visualizar anexo inline (para PDFs e imagens)
     */
    public function view(Proposicao $proposicao, $anexoIndex)
    {
        // Verificar se o usuário tem permissão para acessar a proposição
        if (!$this->canAccessProposicao($proposicao)) {
            abort(403, 'Você não tem permissão para acessar este anexo.');
        }
        
        // Verificar se a proposição tem anexos
        if (!$proposicao->anexos || !is_array($proposicao->anexos)) {
            abort(404, 'Esta proposição não possui anexos.');
        }
        
        // Verificar se o índice do anexo é válido
        if (!isset($proposicao->anexos[$anexoIndex])) {
            abort(404, 'Anexo não encontrado.');
        }
        
        $anexo = $proposicao->anexos[$anexoIndex];
        
        // Verificar se o arquivo existe no storage
        if (!Storage::disk('public')->exists($anexo['caminho'])) {
            abort(404, 'Arquivo não encontrado no servidor.');
        }
        
        // Verificar se o tipo de arquivo pode ser visualizado inline
        $viewableTypes = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!in_array(strtolower($anexo['extensao']), $viewableTypes)) {
            // Se não for visualizável, fazer download
            return $this->download($proposicao, $anexoIndex);
        }
        
        // Obter o caminho completo do arquivo
        $filePath = Storage::disk('public')->path($anexo['caminho']);
        
        // Retornar o arquivo para visualização inline
        return response()->file(
            $filePath,
            [
                'Content-Type' => $anexo['tipo'] ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . $anexo['nome_original'] . '"'
            ]
        );
    }
    
    /**
     * Verificar se o usuário pode acessar a proposição
     */
    private function canAccessProposicao(Proposicao $proposicao)
    {
        $user = auth()->user();
        
        // Admin sempre pode acessar
        if ($user->hasRole('ADMIN')) {
            return true;
        }
        
        // Autor pode acessar sua própria proposição
        if ($proposicao->autor_id === $user->id) {
            return true;
        }
        
        // Legislativo pode acessar proposições enviadas para análise
        if ($user->hasRole('LEGISLATIVO') && in_array($proposicao->status, ['enviado_legislativo', 'em_revisao', 'aprovado'])) {
            return true;
        }
        
        // Protocolo pode acessar proposições aprovadas
        if ($user->hasRole('PROTOCOLO') && $proposicao->status === 'aprovado') {
            return true;
        }
        
        return false;
    }
}