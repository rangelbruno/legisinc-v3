<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Proposicao;

class CheckAssinaturaPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar se usuário está autenticado
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Não autenticado'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();
        $proposicaoParam = $request->route('proposicao');

        // Se já é uma instância de Proposicao (Laravel Model Binding), usar diretamente
        if ($proposicaoParam instanceof Proposicao) {
            $proposicao = $proposicaoParam;
            $proposicaoId = $proposicao->id;
        } else {
            $proposicaoId = $proposicaoParam;
            
            // Log para debug
            Log::info('CheckAssinaturaPermission - Debug', [
                'proposicao_id' => $proposicaoId,
                'tipo_proposicao_id' => gettype($proposicaoId),
                'is_numeric' => is_numeric($proposicaoId),
                'is_array' => is_array($proposicaoId),
                'is_object' => is_object($proposicaoId),
                'route_name' => $request->route()->getName(),
                'url' => $request->url()
            ]);
            
            // Se não há ID de proposição, permitir acesso
            if (!$proposicaoId) {
                return $next($request);
            }
            
            // Buscar proposição
            $proposicao = null;
            
            if (is_numeric($proposicaoId)) {
                $proposicao = Proposicao::find($proposicaoId);
            } elseif (is_array($proposicaoId)) {
                $proposicao = Proposicao::find($proposicaoId['id'] ?? $proposicaoId[0] ?? null);
            }
        }
        
        if (!$proposicao) {
            Log::error('CheckAssinaturaPermission - Proposição não encontrada', [
                'proposicao_id' => $proposicaoId,
                'tipo' => gettype($proposicaoId)
            ]);
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Proposição não encontrada'], 404);
            }
            abort(404, 'Proposição não encontrada.');
        }

        // Verificar se usuário tem permissão para assinar
        if (!$this->podeAssinar($user, $proposicao)) {
            Log::warning('CheckAssinaturaPermission - Acesso negado', [
                'user_id' => $user->id,
                'user_roles' => $user->roles->pluck('name')->toArray(),
                'proposicao_id' => $proposicao->id,
                'proposicao_autor_id' => $proposicao->autor_id,
                'proposicao_parlamentar_id' => $proposicao->parlamentar_id ?? 'N/A'
            ]);
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Você não tem permissão para assinar esta proposição'], 403);
            }
            abort(403, 'Você não tem permissão para assinar esta proposição.');
        }

        // Verificar se proposição está disponível para assinatura
        if (!$this->proposicaoDisponivelParaAssinatura($proposicao)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Esta proposição não está disponível para assinatura'], 403);
            }
            abort(403, 'Esta proposição não está disponível para assinatura.');
        }

        return $next($request);
    }

    /**
     * Verificar se usuário pode assinar a proposição
     */
    private function podeAssinar($user, Proposicao $proposicao): bool
    {
        Log::info('CheckAssinaturaPermission - Verificando permissão', [
            'user_id' => $user->id,
            'user_roles' => $user->roles->pluck('name')->toArray(),
            'proposicao_id' => $proposicao->id,
            'proposicao_autor_id' => $proposicao->autor_id,
            'proposicao_parlamentar_id' => $proposicao->parlamentar_id ?? 'N/A'
        ]);

        // Admin pode assinar qualquer proposição
        if ($user->hasRole('ADMIN')) {
            Log::info('CheckAssinaturaPermission - Usuário é ADMIN, permitindo acesso');
            return true;
        }

        // Parlamentar pode assinar suas próprias proposições
        if ($user->hasRole('PARLAMENTAR')) {
            // Verificar se é autor da proposição
            if ($proposicao->autor_id == $user->id) {
                Log::info('CheckAssinaturaPermission - Usuário é autor da proposição, permitindo acesso');
                return true;
            }
            
            // Verificar se é parlamentar da proposição (caso tenha campo parlamentar_id)
            if (isset($proposicao->parlamentar_id) && $proposicao->parlamentar_id == $user->id) {
                Log::info('CheckAssinaturaPermission - Usuário é parlamentar da proposição, permitindo acesso');
                return true;
            }
        }

        // Assessor pode assinar proposições do parlamentar que assessora
        if ($user->hasRole('ASSESSOR')) {
            if (isset($proposicao->parlamentar_id) && $proposicao->parlamentar_id == $user->parlamentar_id) {
                Log::info('CheckAssinaturaPermission - Usuário é assessor do parlamentar, permitindo acesso');
                return true;
            }
        }

        // Verificar permissões específicas
        if ($user->hasPermissionTo('proposicoes.assinar')) {
            Log::info('CheckAssinaturaPermission - Usuário tem permissão específica, permitindo acesso');
            return true;
        }

        Log::warning('CheckAssinaturaPermission - Usuário não tem permissão para assinar');
        return false;
    }

    /**
     * Verificar se proposição está disponível para assinatura
     */
    private function proposicaoDisponivelParaAssinatura(Proposicao $proposicao): bool
    {
        // Status que permitem assinatura
        $statusPermitidos = ['aprovado', 'aprovado_assinatura', 'aguardando_assinatura'];
        
        if (!in_array($proposicao->status, $statusPermitidos)) {
            return false;
        }

        // Verificar se já não foi assinada
        if ($proposicao->status === 'assinado') {
            return false;
        }

        // Verificar se existe PDF para assinatura
        if (!$this->existePDFParaAssinatura($proposicao)) {
            return false;
        }

        return true;
    }

    /**
     * Verificar se existe PDF para assinatura
     */
    private function existePDFParaAssinatura(Proposicao $proposicao): bool
    {
        // Verificar se existe PDF gerado pelo sistema
        if ($proposicao->arquivo_pdf_path) {
            $caminho = storage_path('app/' . $proposicao->arquivo_pdf_path);
            if (file_exists($caminho)) {
                return true;
            }
        }

        // Verificar se existe PDF no diretório de assinatura
        $diretorioPDFs = storage_path("app/proposicoes/pdfs/{$proposicao->id}");
        if (is_dir($diretorioPDFs)) {
            $pdfs = glob($diretorioPDFs . '/*.pdf');
            if (!empty($pdfs)) {
                return true;
            }
        }

        // Verificar se existe PDF do OnlyOffice (diretório antigo)
        $diretorioPDFsOnlyOffice = storage_path("app/private/proposicoes/pdfs/{$proposicao->id}");
        if (is_dir($diretorioPDFsOnlyOffice)) {
            $pdfs = glob($diretorioPDFsOnlyOffice . '/*.pdf');
            if (!empty($pdfs)) {
                return true;
            }
        }

        // Verificar se existe arquivo DOCX/RTF que pode ser convertido para PDF
        if ($this->existeDocxParaConversao($proposicao)) {
            return true;
        }

        // Verificar se existe arquivo RTF que pode ser convertido
        if ($this->existeRtfParaConversao($proposicao)) {
            return true;
        }

        return false;
    }

    /**
     * Verificar se existe arquivo DOCX que pode ser convertido para PDF
     */
    private function existeDocxParaConversao(Proposicao $proposicao): bool
    {
        // Verificar arquivo_path do banco
        if ($proposicao->arquivo_path) {
            $caminhoCompleto = storage_path('app/' . $proposicao->arquivo_path);
            if (file_exists($caminhoCompleto) && str_ends_with($caminhoCompleto, '.docx')) {
                return true;
            }
        }

        // Buscar arquivos DOCX nos diretórios padrão
        $diretorios = [
            storage_path("app/proposicoes"),
            storage_path("app/private/proposicoes"),
            storage_path("app/public/proposicoes")
        ];

        foreach ($diretorios as $diretorio) {
            if (is_dir($diretorio)) {
                $pattern = $diretorio . "/proposicao_{$proposicao->id}_*.docx";
                $encontrados = glob($pattern);
                if (!empty($encontrados)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verificar se existe arquivo RTF que pode ser convertido para PDF
     */
    private function existeRtfParaConversao(Proposicao $proposicao): bool
    {
        // Verificar arquivo_path do banco
        if ($proposicao->arquivo_path) {
            $caminhoCompleto = storage_path('app/' . $proposicao->arquivo_path);
            if (file_exists($caminhoCompleto) && str_ends_with($caminhoCompleto, '.rtf')) {
                return true;
            }
        }

        // Buscar arquivos RTF nos diretórios padrão
        $diretorios = [
            storage_path("app/proposicoes"),
            storage_path("app/private/proposicoes"),
            storage_path("app/public/proposicoes")
        ];

        foreach ($diretorios as $diretorio) {
            if (is_dir($diretorio)) {
                $pattern = $diretorio . "/proposicao_{$proposicao->id}_*.rtf";
                $encontrados = glob($pattern);
                if (!empty($encontrados)) {
                    return true;
                }
            }
        }

        return false;
    }
}
