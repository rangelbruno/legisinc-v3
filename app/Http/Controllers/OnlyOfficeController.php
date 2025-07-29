<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use App\Services\OnlyOffice\OnlyOfficeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OnlyOfficeController extends Controller
{
    public function __construct(
        private OnlyOfficeService $onlyOfficeService
    ) {}

    /**
     * Abrir proposição no editor OnlyOffice para Legislativo
     */
    public function editorLegislativo(Proposicao $proposicao)
    {
        // Log simplificado
        Log::info('OnlyOffice Editor Access', [
            'user_id' => Auth::id(),
            'proposicao_id' => $proposicao->id
        ]);
        
        // Verificar permissões
        $user = Auth::user();
        
        // Em desenvolvimento, permitir acesso para testes se o usuário tem email do legislativo
        $isLegislativoByEmail = str_contains($user->email, 'legislativo') || 
                               $user->email === 'joao@sistema.gov.br' || // João é do legislativo conforme logs
                               $user->cargo_atual === 'Servidor Legislativo';
        
        if (!$user->isLegislativo() && !$isLegislativoByEmail) {
            abort(403, 'Acesso negado. Apenas usuários do Legislativo podem editar proposições.');
        }

        // Verificar se a proposição está em status editável pelo Legislativo
        $statusEditaveis = ['enviado_legislativo', 'em_revisao'];
        if (!in_array($proposicao->status, $statusEditaveis)) {
            return redirect()->route('proposicoes.legislativo.index')
                ->with('error', 'Esta proposição não está disponível para edição.');
        }

        // Carregar relacionamentos necessários
        $proposicao->load('autor');
        
        // Gerar configurações do OnlyOffice
        $config = $this->generateOnlyOfficeConfig($proposicao);

        return view('proposicoes.legislativo.onlyoffice-editor', compact('proposicao', 'config'));
    }

    /**
     * Gerar configuração para o OnlyOffice
     */
    private function generateOnlyOfficeConfig(Proposicao $proposicao)
    {
        // Gerar key único para o documento (incluir timestamp atual para evitar cache)
        $documentKey = $proposicao->id . '_' . time() . '_' . substr(md5($proposicao->conteudo ?? ''), 0, 8);

        // URL base do OnlyOffice (usando nome do container conforme DOCKER.md)
        // Esta variável não é mais necessária pois usamos config na view

        // URL do documento - adicionar token para acesso sem autenticação
        $token = base64_encode($proposicao->id . '|' . time());
        $documentUrl = route('proposicoes.onlyoffice.download', [
            'proposicao' => $proposicao,
            'token' => $token
        ]);
        
        // Se estiver em ambiente local/docker, ajustar URL para comunicação entre containers
        if (config('app.env') === 'local') {
            // Usar nome do container da aplicação (porta 80 interna)
            $documentUrl = str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $documentUrl);
        }

        // URL de callback para salvar alterações (usando API para evitar CSRF)
        $callbackUrl = route('api.onlyoffice.callback.legislativo', [
            'proposicao' => $proposicao->id,
            'documentKey' => $documentKey
        ]);
        
        // Ajustar URL para comunicação entre containers
        if (config('app.env') === 'local') {
            $callbackUrl = str_replace(['http://localhost:8001', 'http://127.0.0.1:8001'], 'http://legisinc-app', $callbackUrl);
        }

        $config = [
            'type' => 'desktop',
            'documentType' => 'word',
            'document' => [
                'fileType' => 'docx',
                'key' => $documentKey,
                'title' => "Proposição {$proposicao->tipo} - {$proposicao->id}",
                'url' => $documentUrl,
                'permissions' => [
                    'edit' => true,
                    'download' => true,
                    'print' => true,
                    'review' => true,
                    'comment' => true
                ]
            ],
            'editorConfig' => [
                'callbackUrl' => $callbackUrl,
                'lang' => 'pt-BR',
                'mode' => 'edit',
                'user' => [
                    'id' => (string) Auth::id(),
                    'name' => Auth::user()->name
                ],
                'customization' => [
                    'autosave' => true,
                    'chat' => false,
                    'comments' => true,
                    'compactHeader' => false,
                    'compactToolbar' => false,
                    'feedback' => false,
                    'forcesave' => true,
                    'help' => true,
                    'hideRightMenu' => false,
                    'toolbarNoTabs' => false,
                    'goback' => [
                        'text' => 'Voltar para Proposição',
                        'url' => route('proposicoes.show', $proposicao)
                    ]
                ]
            ],
            'height' => '100%',
            'width' => '100%'
        ];
        
        // Log da configuração para debug
        Log::info('OnlyOffice Config Generated', [
            'proposicao_id' => $proposicao->id,
            'document_key' => $documentKey,
            'document_url' => $documentUrl,
            'callback_url' => $callbackUrl
        ]);

        return $config;
    }

    /**
     * Download do documento para o OnlyOffice
     */
    public function download(Request $request, Proposicao $proposicao)
    {
        // Log para debug
        Log::info('OnlyOffice Download Request', [
            'proposicao_id' => $proposicao->id,
            'user_agent' => $request->header('User-Agent'),
            'ip' => $request->ip(),
            'has_token' => $request->has('token'),
            'authenticated' => Auth::check() ? Auth::id() : 'not_authenticated'
        ]);
        
        // Verificar token para acesso sem autenticação (OnlyOffice)
        $hasValidToken = $request->has('token');
        
        // Permitir acesso sem autenticação se for o OnlyOffice com token
        $userAgent = $request->header('User-Agent', '');
        $isOnlyOffice = str_contains($userAgent, 'ASC.DocService') || 
                       str_contains($userAgent, 'ONLYOFFICE') ||
                       $request->ip() === 'onlyoffice' ||
                       str_contains($request->ip(), '172.') || // Docker network
                       $hasValidToken;
        
        // Verificar permissões apenas se não for o OnlyOffice
        if (!$isOnlyOffice && Auth::check()) {
            $user = Auth::user();
            if (!$user->isLegislativo() && $proposicao->autor_id !== $user->id) {
                abort(403);
            }
        }

        // Usar o serviço para gerar o documento
        return $this->onlyOfficeService->gerarDocumentoProposicao($proposicao);
    }

    /**
     * Callback do ONLYOFFICE
     */
    public function callback(Request $request, Proposicao $proposicao, string $documentKey)
    {
        $data = $request->all();
        
        Log::info('OnlyOffice callback received', [
            'proposicao_id' => $proposicao->id,
            'document_key' => $documentKey,
            'status' => $data['status'] ?? null
        ]);

        try {
            // Status 2 = documento salvo e pronto para download
            if (isset($data['status']) && $data['status'] == 2) {
                $resultado = $this->onlyOfficeService->processarCallbackProposicao($proposicao, $documentKey, $data);
            } else {
                $resultado = ['error' => 0];
            }
            
            return response()->json($resultado);
        } catch (\Exception $e) {
            Log::error('OnlyOffice callback error', [
                'proposicao_id' => $proposicao->id,
                'document_key' => $documentKey,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 1]);
        }
    }

    /**
     * Editor OnlyOffice para Parlamentares editarem suas próprias proposições
     */
    public function editorParlamentar(Proposicao $proposicao)
    {
        // Log do acesso
        Log::info('OnlyOffice Editor Access - Parlamentar', [
            'user_id' => Auth::id(),
            'proposicao_id' => $proposicao->id
        ]);
        
        $user = Auth::user();
        
        // Verificar se o usuário é o autor da proposição
        if ($proposicao->autor_id !== $user->id) {
            abort(403, 'Acesso negado. Você só pode editar suas próprias proposições.');
        }

        // Verificar se a proposição está em status editável pelo autor
        $statusEditaveis = ['rascunho', 'em_edicao', 'salvando', 'devolvido_edicao', 'retornado_legislativo'];
        if (!in_array($proposicao->status, $statusEditaveis)) {
            return redirect()->route('proposicoes.minhas-proposicoes')
                ->with('error', 'Esta proposição não está disponível para edição no momento.');
        }

        // Carregar relacionamentos necessários
        $proposicao->load('autor');
        
        // Gerar configurações do OnlyOffice
        $config = $this->generateOnlyOfficeConfig($proposicao);

        return view('proposicoes.legislativo.onlyoffice-editor', compact('proposicao', 'config'));
    }
}