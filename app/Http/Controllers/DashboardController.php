<?php

namespace App\Http\Controllers;

// use App\Models\Projeto; // REMOVED - migrated to Proposições
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard principal baseado no perfil do usuário
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Por enquanto, sempre retornar dashboard padrão para evitar problemas
        // TODO: Reativar dashboards específicos após configurar roles
        return view('dashboard');
    }

    /**
     * Dashboard do Parlamentar
     */
    private function dashboardParlamentar()
    {
        try {
            $userId = Auth::id();
            
            $estatisticas = [
                'em_elaboracao' => Projeto::where('autor_id', $userId)
                    ->whereIn('status', ['rascunho', 'em_elaboracao'])
                    ->count(),
                    
                'aguardando_assinatura' => Projeto::where('autor_id', $userId)
                    ->where('status', 'aprovado_assinatura')
                    ->count(),
                    
                'devolvidas_correcao' => Projeto::where('autor_id', $userId)
                    ->where('status', 'devolvido_correcao')
                    ->count(),
                    
                'em_tramitacao' => Projeto::where('autor_id', $userId)
                    ->whereIn('status', ['protocolado', 'em_tramitacao'])
                    ->count(),
                    
                'total_proposicoes' => Projeto::where('autor_id', $userId)->count(),
            ];

            $proposicoes_recentes = Projeto::where('autor_id', $userId)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();

            $proposicoes_urgentes = Projeto::where('autor_id', $userId)
                ->whereIn('status', ['aprovado_assinatura', 'devolvido_correcao'])
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get();

            return view('dashboard.parlamentar', compact('estatisticas', 'proposicoes_recentes', 'proposicoes_urgentes'));
            
        } catch (\Exception $e) {
            \Log::error('Erro no dashboard parlamentar: ' . $e->getMessage());
            return view('dashboard');
        }
    }

    /**
     * Dashboard do Legislativo
     */
    private function dashboardLegislativo()
    {
        try {
            $userId = Auth::id();
            
            $estatisticas = [
                'aguardando_revisao' => Projeto::where('status', 'enviado_legislativo')->count(),
                'em_revisao' => Projeto::where('status', 'em_revisao')->count(),
                'minhas_revisoes' => Projeto::where('revisor_id', $userId)
                    ->where('status', 'em_revisao')
                    ->count(),
                'aprovadas_hoje' => Projeto::where('status', 'aprovado_assinatura')
                    ->whereDate('data_revisao', today())
                    ->count(),
                'devolvidas_hoje' => Projeto::where('status', 'devolvido_correcao')
                    ->whereDate('data_revisao', today())
                    ->count(),
            ];

            $proposicoes_para_revisao = Projeto::whereIn('status', ['enviado_legislativo', 'em_revisao'])
                ->orderBy('created_at', 'asc')
                ->limit(5)
                ->get();

            $minhas_revisoes = Projeto::where('revisor_id', $userId)
                ->where('status', 'em_revisao')
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get();

            return view('dashboard.legislativo', compact('estatisticas', 'proposicoes_para_revisao', 'minhas_revisoes'));
            
        } catch (\Exception $e) {
            \Log::error('Erro no dashboard legislativo: ' . $e->getMessage());
            return view('dashboard');
        }
    }

    /**
     * Dashboard do Protocolo
     */
    private function dashboardProtocolo()
    {
        try {
            $userId = Auth::id();
            
            $estatisticas = [
                'aguardando_protocolo' => Projeto::where('status', 'enviado_protocolo')->count(),
                'protocoladas_hoje' => Projeto::where('status', 'protocolado')
                    ->whereDate('data_protocolo', today())
                    ->count(),
                'protocoladas_mes' => Projeto::where('status', 'protocolado')
                    ->whereMonth('data_protocolo', now()->month)
                    ->whereYear('data_protocolo', now()->year)
                    ->count(),
                'por_funcionario_mes' => Projeto::where('funcionario_protocolo_id', $userId)
                    ->whereMonth('data_protocolo', now()->month)
                    ->whereYear('data_protocolo', now()->year)
                    ->count(),
            ];

            $proposicoes_para_protocolo = Projeto::where('status', 'enviado_protocolo')
                ->orderBy('data_assinatura', 'asc')
                ->limit(5)
                ->get();

            $protocolos_recentes = Projeto::where('funcionario_protocolo_id', $userId)
                ->where('status', 'protocolado')
                ->orderBy('data_protocolo', 'desc')
                ->limit(3)
                ->get();

            return view('dashboard.protocolo', compact('estatisticas', 'proposicoes_para_protocolo', 'protocolos_recentes'));
            
        } catch (\Exception $e) {
            \Log::error('Erro no dashboard protocolo: ' . $e->getMessage());
            return view('dashboard');
        }
    }

    /**
     * Dashboard do Admin
     */
    private function dashboardAdmin()
    {
        try {
            // Verificar se a tabela existe e tem dados
            $totalProposicoes = Projeto::count();
            
            if ($totalProposicoes === 0) {
                // Se não há proposições, usar dados vazios
                $estatisticas = [
                    'total_proposicoes' => 0,
                    'em_elaboracao' => 0,
                    'em_revisao' => 0,
                    'aguardando_assinatura' => 0,
                    'aguardando_protocolo' => 0,
                    'em_tramitacao' => 0,
                ];
                
                $proposicoes_recentes = collect();
                $estatisticas_por_tipo = collect();
                $estatisticas_por_status = collect();
            } else {
                $estatisticas = [
                    'total_proposicoes' => $totalProposicoes,
                    'em_elaboracao' => Projeto::whereIn('status', ['rascunho', 'em_elaboracao'])->count(),
                    'em_revisao' => Projeto::whereIn('status', ['enviado_legislativo', 'em_revisao'])->count(),
                    'aguardando_assinatura' => Projeto::where('status', 'aprovado_assinatura')->count(),
                    'aguardando_protocolo' => Projeto::where('status', 'enviado_protocolo')->count(),
                    'em_tramitacao' => Projeto::whereIn('status', ['protocolado', 'em_tramitacao'])->count(),
                ];

                $proposicoes_recentes = Projeto::with(['autor'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                $estatisticas_por_tipo = Projeto::selectRaw('tipo, count(*) as total')
                    ->groupBy('tipo')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get();

                $estatisticas_por_status = Projeto::selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get();
            }

            return view('dashboard.admin', compact('estatisticas', 'proposicoes_recentes', 'estatisticas_por_tipo', 'estatisticas_por_status'));
            
        } catch (\Exception $e) {
            // Se houver erro (tabela não existe, etc), retornar dashboard padrão
            \Log::error('Erro no dashboard admin: ' . $e->getMessage());
            return view('dashboard');
        }
    }
}