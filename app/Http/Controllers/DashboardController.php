<?php

namespace App\Http\Controllers;

// use App\Models\Projeto; // REMOVED - migrated to Proposições
use App\Models\User;
use App\Models\Proposicao;
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

        // Obter o perfil principal do usuário
        $userRole = $user->getRoleNames()->first();
        
        // Redirecionar para o dashboard específico baseado no perfil
        switch ($userRole) {
            case User::PERFIL_ADMIN:
                return $this->dashboardAdmin();
                
            case User::PERFIL_PARLAMENTAR:
                return $this->dashboardParlamentar();
                
            case User::PERFIL_LEGISLATIVO:
                return $this->dashboardLegislativo();
                
            case User::PERFIL_PROTOCOLO:
                return $this->dashboardProtocolo();
                
            case 'EXPEDIENTE':
                return $this->dashboardExpediente();
                
            case 'ASSESSOR_JURIDICO':
                return $this->dashboardAssessorJuridico();
                
            case User::PERFIL_RELATOR:
                return $this->dashboardRelator();
                
            case User::PERFIL_ASSESSOR:
                return $this->dashboardAssessor();
                
            case User::PERFIL_CIDADAO_VERIFICADO:
                return $this->dashboardCidadao();
                
            case User::PERFIL_PUBLICO:
            default:
                return $this->dashboardPublico();
        }
    }

    /**
     * Dashboard do Parlamentar
     */
    private function dashboardParlamentar()
    {
        try {
            $userId = Auth::id();
            
            $estatisticas = [
                'em_elaboracao' => Proposicao::where('autor_id', $userId)
                    ->whereIn('status', ['rascunho', 'em_elaboracao'])
                    ->count(),
                    
                'aguardando_assinatura' => Proposicao::where('autor_id', $userId)
                    ->where('status', 'aprovado_assinatura')
                    ->count(),
                    
                'devolvidas_correcao' => Proposicao::where('autor_id', $userId)
                    ->where('status', 'devolvido_correcao')
                    ->count(),
                    
                'em_tramitacao' => Proposicao::where('autor_id', $userId)
                    ->whereIn('status', ['protocolado', 'em_tramitacao'])
                    ->count(),
                    
                'total_proposicoes' => Proposicao::where('autor_id', $userId)->count(),
            ];

            $proposicoes_recentes = Proposicao::where('autor_id', $userId)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();

            $proposicoes_urgentes = Proposicao::where('autor_id', $userId)
                ->whereIn('status', ['aprovado_assinatura', 'devolvido_correcao'])
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get();

            return view('dashboard.parlamentar', compact('estatisticas', 'proposicoes_recentes', 'proposicoes_urgentes'));
            
        } catch (\Exception $e) {
            // Log::error('Erro no dashboard parlamentar: ' . $e->getMessage());
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
            
            $totalProposicoes = Proposicao::count();
            
            $estatisticas = [
                'total_proposicoes' => $totalProposicoes,
                'aguardando_revisao' => Proposicao::where('status', 'enviado_legislativo')->count(),
                'em_revisao' => Proposicao::where('status', 'em_revisao')->count(),
                'minhas_revisoes' => 0, // Campo revisor_id não existe ainda
                'aprovadas_hoje' => Proposicao::where('status', 'aprovado_assinatura')
                    ->whereDate('updated_at', today())
                    ->count(),
                'devolvidas_hoje' => Proposicao::where('status', 'devolvido_correcao')
                    ->whereDate('updated_at', today())
                    ->count(),
            ];

            $proposicoes_para_revisao = Proposicao::whereIn('status', ['enviado_legislativo', 'em_revisao'])
                ->orderBy('created_at', 'asc')
                ->limit(5)
                ->get();

            $minhas_revisoes = collect(); // Campo revisor_id não existe ainda

            return view('dashboard.legislativo', compact('estatisticas', 'proposicoes_para_revisao', 'minhas_revisoes'));
            
        } catch (\Exception $e) {
            // Log::error('Erro no dashboard legislativo: ' . $e->getMessage());
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
            
            $totalProposicoes = Proposicao::count();
            
            $aguardandoProtocolo = Proposicao::where('status', 'enviado_protocolo')->count();
            $protocoladasHoje = Proposicao::where('status', 'protocolado')
                ->whereDate('data_protocolo', today())
                ->count();
            $protocoladasMes = Proposicao::where('status', 'protocolado')
                ->whereMonth('data_protocolo', now()->month)
                ->whereYear('data_protocolo', now()->year)
                ->count();
            $porFuncionarioMes = Proposicao::where('funcionario_protocolo_id', $userId)
                ->whereMonth('data_protocolo', now()->month)
                ->whereYear('data_protocolo', now()->year)
                ->count();
            
            $estatisticas = [
                'total_proposicoes' => $totalProposicoes,
                'aguardando_protocolo' => $aguardandoProtocolo,
                'protocoladas_hoje' => $protocoladasHoje,
                'protocoladas_mes' => $protocoladasMes,
                'por_funcionario_mes' => $porFuncionarioMes,
                'tempo_medio_protocolo' => 15, // Mock: 15 minutos médio
                'eficiencia_protocolo' => $protocoladasHoje > 0 ? 8 : 0, // protocolos por hora
            ];

            // Alertas operacionais específicos do protocolo
            $alertas_protocolo = collect([
                (object)[
                    'tipo' => 'warning',
                    'titulo' => 'Proposições aguardando há mais de 24h',
                    'descricao' => 'Documentos pendentes de protocolo há mais de 1 dia',
                    'count' => Proposicao::where('status', 'enviado_protocolo')
                        ->where('data_assinatura', '<=', now()->subDays(1))
                        ->count()
                ],
                (object)[
                    'tipo' => 'info',
                    'titulo' => 'Backlog de protocolo',
                    'descricao' => 'Total de proposições aguardando numeração',
                    'count' => $aguardandoProtocolo
                ]
            ])->filter(function($alerta) {
                return $alerta->count > 0;
            });

            // Numeração por tipo
            $numeracao_tipos = collect([
                (object)['tipo' => 'Projeto de Lei', 'sigla' => 'PL', 'proximo' => 45, 'ano' => date('Y')],
                (object)['tipo' => 'Moção', 'sigla' => 'MOC', 'proximo' => 12, 'ano' => date('Y')],
                (object)['tipo' => 'Requerimento', 'sigla' => 'REQ', 'proximo' => 89, 'ano' => date('Y')],
                (object)['tipo' => 'Indicação', 'sigla' => 'IND', 'proximo' => 23, 'ano' => date('Y')],
            ]);

            $proposicoes_para_protocolo = Proposicao::where('status', 'enviado_protocolo')
                ->orderBy('data_assinatura', 'asc')
                ->limit(5)
                ->get();

            $protocolos_recentes = Proposicao::where('funcionario_protocolo_id', $userId)
                ->where('status', 'protocolado')
                ->orderBy('data_protocolo', 'desc')
                ->limit(3)
                ->get();

            return view('dashboard.protocolo', compact('estatisticas', 'proposicoes_para_protocolo', 'protocolos_recentes', 'alertas_protocolo', 'numeracao_tipos'));
            
        } catch (\Exception $e) {
            // Log::error('Erro no dashboard protocolo: ' . $e->getMessage());
            return view('dashboard');
        }
    }

    /**
     * Dashboard do Expediente
     */
    private function dashboardExpediente()
    {
        try {
            $userId = Auth::id();
            
            // Estatísticas específicas do Expediente
            $estatisticas = [
                'total_proposicoes' => Proposicao::where('status', 'protocolado')->count(),
                'proposicoes_protocoladas' => Proposicao::where('status', 'protocolado')->count(),
                'aguardando_protocolo' => Proposicao::where('status', 'enviado_protocolo')->count(),
                'protocoladas_hoje' => Proposicao::where('status', 'protocolado')
                    ->whereDate('data_protocolo', today())
                    ->count(),
                'protocoladas_mes' => Proposicao::where('status', 'protocolado')
                    ->whereMonth('data_protocolo', now()->month)
                    ->whereYear('data_protocolo', now()->year)
                    ->count(),
                'por_funcionario_mes' => Proposicao::where('status', 'protocolado')
                    ->whereMonth('data_protocolo', now()->month)
                    ->whereYear('data_protocolo', now()->year)
                    ->where('funcionario_protocolo_id', Auth::id())
                    ->count(),
                'aguardando_pauta' => Proposicao::where('status', 'protocolado')
                    ->whereNotNull('momento_sessao')
                    ->where('momento_sessao', '!=', 'NAO_CLASSIFICADO')
                    ->count(),
                'expediente' => Proposicao::where('status', 'protocolado')
                    ->where('momento_sessao', 'EXPEDIENTE')
                    ->count(),
                'ordem_dia' => Proposicao::where('status', 'protocolado')
                    ->where('momento_sessao', 'ORDEM_DO_DIA')
                    ->count(),
                'nao_classificadas' => Proposicao::where('status', 'protocolado')
                    ->whereIn('momento_sessao', ['NAO_CLASSIFICADO', null])
                    ->count(),
                'tempo_medio_protocolo' => 15, // Tempo médio estimado em minutos
                'eficiencia_protocolo' => 4, // Proposições por hora
                'sessoes_hoje' => 0, // Implementar quando houver modelo de sessões
                'pautas_organizadas' => 0, // Implementar quando houver modelo de pautas
            ];

            // Proposições não classificadas que precisam de atenção
            $proposicoes_nao_classificadas = Proposicao::where('status', 'protocolado')
                ->whereIn('momento_sessao', ['NAO_CLASSIFICADO', null])
                ->with(['autor', 'tipoProposicao'])
                ->orderBy('data_protocolo', 'asc')
                ->limit(5)
                ->get();

            // Proposições do Expediente
            $proposicoes_expediente = Proposicao::where('status', 'protocolado')
                ->where('momento_sessao', 'EXPEDIENTE')
                ->with(['autor'])
                ->orderBy('data_protocolo', 'asc')
                ->limit(5)
                ->get();

            // Proposições da Ordem do Dia
            $proposicoes_ordem_dia = Proposicao::where('status', 'protocolado')
                ->where('momento_sessao', 'ORDEM_DO_DIA')
                ->with(['autor'])
                ->orderBy('data_protocolo', 'asc')
                ->limit(5)
                ->get();

            // Alertas do expediente
            $alertas = collect([
                (object)[
                    'tipo' => 'warning',
                    'titulo' => 'Proposições não classificadas',
                    'descricao' => 'Proposições que precisam ser classificadas por momento da sessão',
                    'count' => $estatisticas['nao_classificadas']
                ],
                (object)[
                    'tipo' => 'info',
                    'titulo' => 'Aguardando inclusão em pauta',
                    'descricao' => 'Proposições prontas para serem incluídas em pauta de sessão',
                    'count' => $estatisticas['aguardando_pauta']
                ]
            ])->filter(function($alerta) {
                return $alerta->count > 0;
            });

            // Se não houver view específica, usar a do protocolo temporariamente
            if (!view()->exists('dashboard.expediente')) {
                return view('dashboard.protocolo', [
                    'estatisticas' => $estatisticas,
                    'proposicoes_para_protocolo' => $proposicoes_nao_classificadas,
                    'protocolos_recentes' => $proposicoes_expediente,
                    'alertas_protocolo' => $alertas,
                    'numeracao_tipos' => collect()
                ]);
            }

            return view('dashboard.expediente', compact(
                'estatisticas',
                'proposicoes_nao_classificadas',
                'proposicoes_expediente',
                'proposicoes_ordem_dia',
                'alertas'
            ));
            
        } catch (\Exception $e) {
            // Log::error('Erro no dashboard expediente: ' . $e->getMessage());
            return view('dashboard');
        }
    }

    /**
     * Dashboard do Assessor Jurídico
     */
    private function dashboardAssessorJuridico()
    {
        try {
            $userId = Auth::id();
            
            // Por enquanto usar o dashboard do legislativo
            return $this->dashboardLegislativo();
            
        } catch (\Exception $e) {
            // Log::error('Erro no dashboard assessor jurídico: ' . $e->getMessage());
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
            $totalProposicoes = Proposicao::count();
            
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
                
                // Métricas executivas vazias
                $metricas_executivas = [
                    'parlamentares_ativos' => 0,
                    'sessoes_hoje' => 0,
                    'usuarios_online' => 0,
                    'proposicoes_hoje' => 0,
                    'taxa_aprovacao' => 0,
                    'tempo_medio_tramitacao' => 0,
                ];
                
                // Alertas e dados vazios
                $alertas_criticos = collect();
                $proposicoes_recentes = collect();
                $estatisticas_por_tipo = collect();
                $estatisticas_por_status = collect();
                $performance_parlamentar = collect();
                $atividade_sistema = collect();
            } else {
                $estatisticas = [
                    'total_proposicoes' => $totalProposicoes,
                    'em_elaboracao' => Proposicao::whereIn('status', ['rascunho', 'em_elaboracao'])->count(),
                    'em_revisao' => Proposicao::whereIn('status', ['enviado_legislativo', 'em_revisao'])->count(),
                    'aguardando_assinatura' => Proposicao::where('status', 'aprovado_assinatura')->count(),
                    'aguardando_protocolo' => Proposicao::where('status', 'enviado_protocolo')->count(),
                    'em_tramitacao' => Proposicao::whereIn('status', ['protocolado', 'em_tramitacao'])->count(),
                ];

                // Métricas executivas avançadas
                $parlamentaresAtivos = User::whereHas('roles', function($q) {
                    $q->where('name', User::PERFIL_PARLAMENTAR);
                })->where('ultimo_acesso', '>=', now()->subDays(30))->count();
                
                $proposicoesHoje = Proposicao::whereDate('created_at', today())->count();
                $proposicoesAprovadas = Proposicao::where('status', 'aprovado')->count();
                $taxaAprovacao = $totalProposicoes > 0 ? round(($proposicoesAprovadas / $totalProposicoes) * 100, 1) : 0;
                
                $metricas_executivas = [
                    'parlamentares_ativos' => $parlamentaresAtivos,
                    'sessoes_hoje' => 0, // Campo sessões não implementado ainda
                    'usuarios_online' => User::where('ultimo_acesso', '>=', now()->subMinutes(5))->count(),
                    'proposicoes_hoje' => $proposicoesHoje,
                    'taxa_aprovacao' => $taxaAprovacao,
                    'tempo_medio_tramitacao' => 15, // Mock: 15 dias médio
                ];

                // Alertas críticos
                $alertas_criticos = collect([
                    (object)[
                        'tipo' => 'warning',
                        'titulo' => 'Proposições com prazo próximo',
                        'descricao' => 'Existem proposições com prazo de análise vencendo em 2 dias',
                        'count' => Proposicao::where('status', 'em_revisao')->where('created_at', '<=', now()->subDays(28))->count()
                    ],
                    (object)[
                        'tipo' => 'info',
                        'titulo' => 'Backlog de revisão',
                        'descricao' => 'Proposições aguardando revisão legislativa',
                        'count' => $estatisticas['em_revisao']
                    ]
                ])->filter(function($alerta) {
                    return $alerta->count > 0;
                });

                $proposicoes_recentes = Proposicao::with(['autor'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

                $estatisticas_por_tipo = Proposicao::selectRaw('tipo, count(*) as total')
                    ->groupBy('tipo')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get();

                $estatisticas_por_status = Proposicao::selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get();

                // Performance parlamentar (Top 5)
                $performance_parlamentar = User::whereHas('roles', function($q) {
                    $q->where('name', User::PERFIL_PARLAMENTAR);
                })
                ->withCount(['proposicoesAutor as total_proposicoes'])
                ->orderBy('total_proposicoes', 'desc')
                ->limit(5)
                ->get();

                // Atividade do sistema (últimos 7 dias)
                $atividade_sistema = collect();
                for ($i = 6; $i >= 0; $i--) {
                    $data = now()->subDays($i);
                    $atividade_sistema->push((object)[
                        'data' => $data->format('d/m'),
                        'proposicoes' => Proposicao::whereDate('created_at', $data)->count(),
                        'logins' => User::whereDate('ultimo_acesso', $data)->count(),
                    ]);
                }
            }

            return view('dashboard.admin', compact(
                'estatisticas', 
                'metricas_executivas',
                'alertas_criticos',
                'proposicoes_recentes', 
                'estatisticas_por_tipo', 
                'estatisticas_por_status',
                'performance_parlamentar',
                'atividade_sistema'
            ));
            
        } catch (\Exception $e) {
            // Se houver erro (tabela não existe, etc), retornar dashboard padrão
            // Log::error('Erro no dashboard admin: ' . $e->getMessage());
            return view('dashboard');
        }
    }

    /**
     * Dashboard do Relator
     */
    private function dashboardRelator()
    {
        try {
            $userId = Auth::id();
            
            $estatisticas = [
                'para_relatar' => 0, // Campo relator_id não existe ainda
                'em_analise' => 0, // Campo relator_id não existe ainda 
                'pareceres_emitidos' => 0, // Campo relator_id não existe ainda
                'total_relatorias' => 0, // Campo relator_id não existe ainda
            ];

            $proposicoes_para_parecer = collect(); // Campo relator_id não existe ainda
            $pareceres_recentes = collect(); // Campo relator_id não existe ainda

            return view('dashboard.relator', compact('estatisticas', 'proposicoes_para_parecer', 'pareceres_recentes'));
            
        } catch (\Exception $e) {
            // Log::error('Erro no dashboard relator: ' . $e->getMessage());
            return view('dashboard');
        }
    }

    /**
     * Dashboard do Assessor
     */
    private function dashboardAssessor()
    {
        try {
            $userId = Auth::id();
            
            // Buscar parlamentar vinculado ao assessor (usando campo temporário)
            $parlamentarVinculado = null; // Campo assessor_id não existe ainda
            
            if ($parlamentarVinculado) {
                $estatisticas = [
                    'em_elaboracao' => Proposicao::where('autor_id', $parlamentarVinculado->id)
                        ->whereIn('status', ['rascunho', 'em_elaboracao'])
                        ->count(),
                        
                    'aguardando_revisao' => Proposicao::where('autor_id', $parlamentarVinculado->id)
                        ->where('status', 'aguardando_revisao_assessor')
                        ->count(),
                        
                    'enviadas_parlamentar' => Proposicao::where('autor_id', $parlamentarVinculado->id)
                        ->where('assessor_id', $userId)
                        ->count(),
                        
                    'total_assessoradas' => Proposicao::where('assessor_id', $userId)->count(),
                ];

                $proposicoes_em_elaboracao = Proposicao::where('autor_id', $parlamentarVinculado->id)
                    ->whereIn('status', ['rascunho', 'em_elaboracao', 'aguardando_revisao_assessor'])
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();
            } else {
                $estatisticas = [
                    'em_elaboracao' => 0,
                    'aguardando_revisao' => 0,
                    'enviadas_parlamentar' => 0,
                    'total_assessoradas' => 0,
                ];
                
                $proposicoes_em_elaboracao = collect();
            }

            return view('dashboard.assessor', compact('estatisticas', 'proposicoes_em_elaboracao', 'parlamentarVinculado'));
            
        } catch (\Exception $e) {
            // Log::error('Erro no dashboard assessor: ' . $e->getMessage());
            return view('dashboard');
        }
    }

    /**
     * Dashboard do Cidadão Verificado
     */
    private function dashboardCidadao()
    {
        try {
            $userId = Auth::id();
            
            $estatisticas = [
                'propostas_enviadas' => 0, // Campo cidadao_id não existe ainda
                'em_analise' => 0, // Campo cidadao_id não existe ainda
                'aprovadas' => 0, // Campo cidadao_id não existe ainda
                    
                'votos_realizados' => 0, // Implementar quando houver sistema de votação
            ];

            $minhas_propostas = collect(); // Campo cidadao_id não existe ainda

            $proposicoes_publicas = Proposicao::where('visibilidade', 'publica')
                ->whereIn('status', ['protocolado', 'em_tramitacao'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return view('dashboard.cidadao', compact('estatisticas', 'minhas_propostas', 'proposicoes_publicas'));
            
        } catch (\Exception $e) {
            // Log::error('Erro no dashboard cidadão: ' . $e->getMessage());
            return view('dashboard');
        }
    }

    /**
     * Dashboard Público
     */
    private function dashboardPublico()
    {
        try {
            $estatisticas = [
                'proposicoes_tramitando' => Proposicao::whereIn('status', ['protocolado', 'em_tramitacao'])
                    ->where('visibilidade', 'publica')
                    ->count(),
                    
                'aprovadas_mes' => 0, // Campos de visibilidade e datas específicas não existem ainda
                'rejeitadas_mes' => 0, // Campos de visibilidade e datas específicas não existem ainda
                'total_publicas' => Proposicao::count(), // Usar contagem total por enquanto
            ];

            $proposicoes_recentes = Proposicao::with(['autor'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $proposicoes_populares = collect(); // Campo visualizacoes não existe ainda

            return view('dashboard.publico', compact('estatisticas', 'proposicoes_recentes', 'proposicoes_populares'));
            
        } catch (\Exception $e) {
            // Log::error('Erro no dashboard público: ' . $e->getMessage());
            return view('dashboard');
        }
    }
}