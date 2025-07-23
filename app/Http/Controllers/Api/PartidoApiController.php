<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partido;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PartidoApiController extends Controller
{
    /**
     * Lista todos os partidos cadastrados
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Partido::query();
            
            // Filtros opcionais
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('search')) {
                $query->buscar($request->search);
            }
            
            // Ordenação
            $orderBy = $request->get('order_by', 'nome');
            $orderDirection = $request->get('order_direction', 'asc');
            $query->orderBy($orderBy, $orderDirection);
            
            // Paginação ou todos os resultados
            if ($request->has('per_page')) {
                $partidos = $query->paginate($request->per_page);
            } else {
                $partidos = $query->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $partidos,
                'message' => 'Partidos listados com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar partidos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Busca dados de um partido específico
     */
    public function show($id): JsonResponse
    {
        try {
            $partido = Partido::with('parlamentares')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $partido,
                'message' => 'Partido encontrado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Partido não encontrado'
            ], 404);
        }
    }
    
    /**
     * Busca dados externos de partidos políticos brasileiros
     */
    public function buscarDadosExternos(Request $request): JsonResponse
    {
        try {
            $sigla = strtoupper($request->get('sigla'));
            
            if (!$sigla) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sigla do partido é obrigatória'
                ], 400);
            }
            
            // Cache por 1 hora
            $cacheKey = "partido_externo_{$sigla}";
            $dadosExternos = Cache::remember($cacheKey, 3600, function () use ($sigla) {
                return $this->consultarDadosExternos($sigla);
            });
            
            if ($dadosExternos) {
                return response()->json([
                    'success' => true,
                    'data' => $dadosExternos,
                    'message' => 'Dados encontrados com sucesso'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados não encontrados para a sigla informada'
                ], 404);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados externos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Lista de partidos brasileiros predefinidos
     */
    public function partidosBrasileiros(): JsonResponse
    {
        try {
            $partidos = $this->getPartidosBrasileiros();
            
            return response()->json([
                'success' => true,
                'data' => $partidos,
                'message' => 'Lista de partidos brasileiros'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar lista de partidos'
            ], 500);
        }
    }
    
    /**
     * Busca partido por sigla
     */
    public function buscarPorSigla(Request $request): JsonResponse
    {
        try {
            $sigla = strtoupper($request->get('sigla'));
            
            if (!$sigla) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sigla é obrigatória'
                ], 400);
            }
            
            // Primeiro busca no banco local
            $partidoLocal = Partido::where('sigla', $sigla)->first();
            
            if ($partidoLocal) {
                return response()->json([
                    'success' => true,
                    'data' => $partidoLocal,
                    'source' => 'local',
                    'message' => 'Partido encontrado no banco local'
                ]);
            }
            
            // Se não encontrar, busca na lista de partidos brasileiros
            $partidos = $this->getPartidosBrasileiros();
            $partidoExterno = collect($partidos)->firstWhere('sigla', $sigla);
            
            if ($partidoExterno) {
                return response()->json([
                    'success' => true,
                    'data' => $partidoExterno,
                    'source' => 'external',
                    'message' => 'Dados encontrados na base de partidos brasileiros'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Partido não encontrado'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar partido: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Validar se sigla já existe
     */
    public function validarSigla(Request $request): JsonResponse
    {
        try {
            $sigla = strtoupper($request->get('sigla'));
            $id = $request->get('id'); // Para edição
            
            if (!$sigla) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sigla é obrigatória'
                ], 400);
            }
            
            $query = Partido::where('sigla', $sigla);
            
            // Se for edição, exclui o próprio registro
            if ($id) {
                $query->where('id', '!=', $id);
            }
            
            $existe = $query->exists();
            
            return response()->json([
                'success' => true,
                'exists' => $existe,
                'message' => $existe ? 'Sigla já está em uso' : 'Sigla disponível'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar sigla: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Validar se número já existe
     */
    public function validarNumero(Request $request): JsonResponse
    {
        try {
            $numero = $request->get('numero');
            $id = $request->get('id'); // Para edição
            
            if (!$numero) {
                return response()->json([
                    'success' => false,
                    'message' => 'Número é obrigatório'
                ], 400);
            }
            
            $query = Partido::where('numero', $numero);
            
            // Se for edição, exclui o próprio registro
            if ($id) {
                $query->where('id', '!=', $id);
            }
            
            $existe = $query->exists();
            
            return response()->json([
                'success' => true,
                'exists' => $existe,
                'message' => $existe ? 'Número já está em uso' : 'Número disponível'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar número: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Estatísticas dos partidos
     */
    public function estatisticas(): JsonResponse
    {
        try {
            $estatisticas = [
                'total_partidos' => Partido::count(),
                'partidos_ativos' => Partido::ativos()->count(),
                'partidos_inativos' => Partido::where('status', 'inativo')->count(),
                'total_parlamentares' => Partido::withCount('parlamentares')->get()->sum('parlamentares_count'),
                'partidos_sem_parlamentares' => Partido::doesntHave('parlamentares')->count(),
                'partido_mais_parlamentares' => Partido::withCount('parlamentares')
                    ->orderBy('parlamentares_count', 'desc')
                    ->first(),
                'por_status' => Partido::selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $estatisticas,
                'message' => 'Estatísticas geradas com sucesso'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Consulta dados externos (mock - pode ser integrado com APIs reais)
     */
    private function consultarDadosExternos(string $sigla): ?array
    {
        // Mock de dados externos - em produção, integrar com APIs do TSE, Câmara, etc.
        $dadosExternos = [
            'PT' => [
                'sigla' => 'PT',
                'nome' => 'Partido dos Trabalhadores',
                'numero' => '13',
                'presidente' => 'Gleisi Hoffmann',
                'fundacao' => '1980-02-10',
                'site' => 'https://pt.org.br'
            ],
            'PSDB' => [
                'sigla' => 'PSDB',
                'nome' => 'Partido da Social Democracia Brasileira',
                'numero' => '45',
                'presidente' => 'Marconi Perillo',
                'fundacao' => '1988-06-25',
                'site' => 'https://psdb.org.br'
            ],
            'MDB' => [
                'sigla' => 'MDB',
                'nome' => 'Movimento Democrático Brasileiro',
                'numero' => '15',
                'presidente' => 'Baleia Rossi',
                'fundacao' => '1980-01-15',
                'site' => 'https://mdb.org.br'
            ],
            'PL' => [
                'sigla' => 'PL',
                'nome' => 'Partido Liberal',
                'numero' => '22',
                'presidente' => 'Valdemar Costa Neto',
                'fundacao' => '2006-10-26',
                'site' => 'https://pl.org.br'
            ]
        ];
        
        return $dadosExternos[$sigla] ?? null;
    }
    
    /**
     * Lista de partidos brasileiros com dados básicos
     */
    private function getPartidosBrasileiros(): array
    {
        return [
            ['sigla' => 'PT', 'nome' => 'Partido dos Trabalhadores', 'numero' => '13', 'presidente' => 'Gleisi Hoffmann', 'fundacao' => '1980-02-10', 'site' => 'https://pt.org.br'],
            ['sigla' => 'PSDB', 'nome' => 'Partido da Social Democracia Brasileira', 'numero' => '45', 'presidente' => 'Marconi Perillo', 'fundacao' => '1988-06-25', 'site' => 'https://psdb.org.br'],
            ['sigla' => 'MDB', 'nome' => 'Movimento Democrático Brasileiro', 'numero' => '15', 'presidente' => 'Baleia Rossi', 'fundacao' => '1980-01-15', 'site' => 'https://mdb.org.br'],
            ['sigla' => 'PL', 'nome' => 'Partido Liberal', 'numero' => '22', 'presidente' => 'Valdemar Costa Neto', 'fundacao' => '2006-10-26', 'site' => 'https://pl.org.br'],
            ['sigla' => 'PSL', 'nome' => 'Partido Social Liberal', 'numero' => '17', 'presidente' => 'Luciano Bivar', 'fundacao' => '1994-06-02', 'site' => 'https://psl.org.br'],
            ['sigla' => 'PDT', 'nome' => 'Partido Democrático Trabalhista', 'numero' => '12', 'presidente' => 'Carlos Lupi', 'fundacao' => '1979-05-17', 'site' => 'https://pdt.org.br'],
            ['sigla' => 'PP', 'nome' => 'Progressistas', 'numero' => '11', 'presidente' => 'Ciro Nogueira', 'fundacao' => '1995-08-26', 'site' => 'https://pp.org.br'],
            ['sigla' => 'PSOL', 'nome' => 'Partido Socialismo e Liberdade', 'numero' => '50', 'presidente' => 'Juliano Medeiros', 'fundacao' => '2004-09-15', 'site' => 'https://psol.org.br'],
            ['sigla' => 'UNIÃO', 'nome' => 'União Brasil', 'numero' => '44', 'presidente' => 'ACM Neto', 'fundacao' => '2021-02-08', 'site' => 'https://uniao.org.br'],
            ['sigla' => 'PCdoB', 'nome' => 'Partido Comunista do Brasil', 'numero' => '65', 'presidente' => 'Luciana Santos', 'fundacao' => '1988-06-25', 'site' => 'https://pcdob.org.br'],
            ['sigla' => 'PSB', 'nome' => 'Partido Socialista Brasileiro', 'numero' => '40', 'presidente' => 'Carlos Siqueira', 'fundacao' => '1985-07-01', 'site' => 'https://psb.org.br'],
            ['sigla' => 'REPUBLICANOS', 'nome' => 'Republicanos', 'numero' => '10', 'presidente' => 'Marcos Pereira', 'fundacao' => '2005-05-25', 'site' => 'https://republicanos.org.br'],
            ['sigla' => 'PSC', 'nome' => 'Partido Social Cristão', 'numero' => '20', 'presidente' => 'Everaldo Dias Pereira', 'fundacao' => '1985-05-29', 'site' => 'https://psc.org.br'],
            ['sigla' => 'PODE', 'nome' => 'Podemos', 'numero' => '19', 'presidente' => 'Renata Abreu', 'fundacao' => '1997-09-02', 'site' => 'https://podemos.org.br'],
            ['sigla' => 'PTB', 'nome' => 'Partido Trabalhista Brasileiro', 'numero' => '14', 'presidente' => 'Kassab', 'fundacao' => '1945-05-15', 'site' => 'https://ptb.org.br'],
            ['sigla' => 'AVANTE', 'nome' => 'Avante', 'numero' => '70', 'presidente' => 'Luís Tibé', 'fundacao' => '2013-09-25', 'site' => 'https://avante.org.br'],
            ['sigla' => 'PSD', 'nome' => 'Partido Social Democrático', 'numero' => '55', 'presidente' => 'Kassab', 'fundacao' => '2011-03-27', 'site' => 'https://psd.org.br'],
            ['sigla' => 'SOLIDARIEDADE', 'nome' => 'Solidariedade', 'numero' => '77', 'presidente' => 'Paulinho da Força', 'fundacao' => '2013-09-24', 'site' => 'https://solidariedade.org.br'],
            ['sigla' => 'NOVO', 'nome' => 'Partido Novo', 'numero' => '30', 'presidente' => 'Eduardo Ribeiro', 'fundacao' => '2011-02-15', 'site' => 'https://novo.org.br'],
            ['sigla' => 'REDE', 'nome' => 'Rede Sustentabilidade', 'numero' => '18', 'presidente' => 'Heloísa Helena', 'fundacao' => '2013-02-20', 'site' => 'https://redesustentabilidade.org.br'],
            ['sigla' => 'PV', 'nome' => 'Partido Verde', 'numero' => '43', 'presidente' => 'José Luiz Penna', 'fundacao' => '1986-09-30', 'site' => 'https://pv.org.br'],
            ['sigla' => 'CIDADANIA', 'nome' => 'Cidadania', 'numero' => '23', 'presidente' => 'Roberto Freire', 'fundacao' => '2013-03-26', 'site' => 'https://cidadania.org.br'],
            ['sigla' => 'PMN', 'nome' => 'Partido da Mobilização Nacional', 'numero' => '33', 'presidente' => 'Telma Ribeiro', 'fundacao' => '1985-10-25', 'site' => 'https://pmn.org.br'],
            ['sigla' => 'PMB', 'nome' => 'Partido da Mulher Brasileira', 'numero' => '35', 'presidente' => 'Suêd Haidar', 'fundacao' => '2015-02-05', 'site' => 'https://pmb.org.br'],
        ];
    }
}