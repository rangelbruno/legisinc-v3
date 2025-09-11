<?php

namespace App\Http\Controllers\Parlamentar;

use App\Http\Controllers\Controller;
use App\Services\Parlamentar\ParlamentarService;
use App\Models\Parlamentar;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ParlamentarController extends Controller
{
    protected ParlamentarService $parlamentarService;
    
    public function __construct(ParlamentarService $parlamentarService)
    {
        $this->parlamentarService = $parlamentarService;
    }
    
    /**
     * Exibir lista de parlamentares
     */
    public function index(Request $request): View
    {
        try {
            $filters = [];
            
            // Aplicar filtros da requisição
            if ($request->has('partido') && $request->partido) {
                $filters['partido'] = $request->partido;
            }
            
            if ($request->has('status') && $request->status) {
                $filters['status'] = $request->status;
            }
            
            $parlamentares = $this->parlamentarService->getAll($filters);
            $estatisticas = $this->parlamentarService->getEstatisticas();
            
            // Formatir parlamentares para exibição
            $parlamentaresFormatados = $parlamentares->map(function ($parlamentar) {
                return $this->parlamentarService->formatForDisplay($parlamentar->toArray());
            });
            
            return view('modules.parlamentares.index', [
                'parlamentares' => $parlamentaresFormatados,
                'estatisticas' => $estatisticas,
                'filtros' => $filters,
                'title' => 'Parlamentares'
            ]);
            
        } catch (\Exception $e) {
            return view('modules.parlamentares.index', [
                'parlamentares' => collect([]),
                'estatisticas' => [],
                'filtros' => [],
                'error' => 'Erro ao carregar parlamentares: ' . $e->getMessage(),
                'title' => 'Parlamentares'
            ]);
        }
    }
    
    /**
     * Exibir detalhes de um parlamentar
     */
    public function show(int $id): View|RedirectResponse
    {
        try {
            $parlamentar = $this->parlamentarService->getById($id);
            $comissoes = $this->parlamentarService->getComissoes($id);
            
            $parlamentarFormatado = $this->parlamentarService->formatForDisplay($parlamentar);
            
            return view('modules.parlamentares.show', [
                'parlamentar' => $parlamentarFormatado,
                'comissoes' => $comissoes['comissoes'],
                'meta_comissoes' => $comissoes['meta'],
                'title' => 'Parlamentar - ' . $parlamentar['nome']
            ]);
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Parlamentar não encontrado.');
        } catch (\Exception $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Erro ao carregar parlamentar: ' . $e->getMessage());
        }
    }
    
    /**
     * Exibir formulário de criação
     */
    public function create(): View
    {
        // Buscar usuários parlamentares que não possuem cadastro de parlamentar vinculado
        $usuariosSemParlamentar = User::whereHas('roles', function ($q) {
                $q->whereIn('name', [User::PERFIL_PARLAMENTAR, User::PERFIL_RELATOR]);
            })
            ->whereDoesntHave('parlamentar')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'partido']);

        return view('modules.parlamentares.create', [
            'title' => 'Novo Parlamentar',
            'partidos' => $this->getPartidosOptions(),
            'cargos' => $this->getCargosOptions(),
            'statusOptions' => $this->getStatusOptions(),
            'escolaridadeOptions' => $this->getEscolaridadeOptions(),
            'usuariosSemParlamentar' => $usuariosSemParlamentar
        ]);
    }
    
    /**
     * Armazenar novo parlamentar
     */
    public function store(Request $request): RedirectResponse
    {
        // Validações personalizadas
        $rules = [
            'nome' => 'required|string|max:255',
            'nome_politico' => 'nullable|string|max:255',
            'partido' => 'required|string|max:50',
            'cargo' => 'required|string|max:100',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:parlamentars,email',
            'cpf' => 'nullable|string|max:14|unique:parlamentars,cpf',
            'data_nascimento' => 'nullable|date|before:today',
            'profissao' => 'nullable|string|max:100',
            'escolaridade' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'comissoes' => 'nullable|string',
            // Campos para integração com usuário
            'user_id' => 'nullable|exists:users,id',
            'criar_usuario' => 'boolean',
            'usuario_password' => 'nullable|string|min:8',
            'usuario_password_confirmation' => 'nullable|string|min:8|same:usuario_password',
            // Campos para certificado digital
            'upload_certificado' => 'nullable|boolean',
            'certificado_digital_create' => 'nullable|file|max:5120',
            'certificado_senha_create' => 'nullable|string|min:4',
        ];

        // Se vai criar usuário, validar email único também na tabela users
        if ($request->boolean('criar_usuario') && $request->email) {
            $rules['email'] = 'required|email|max:255|unique:parlamentars,email|unique:users,email';
        }

        // Se está vinculando a usuário existente, verificar se não tem parlamentar
        if ($request->user_id) {
            $usuarioExistente = User::find($request->user_id);
            if ($usuarioExistente && $usuarioExistente->parlamentar) {
                return redirect()->back()
                    ->withErrors(['user_id' => 'Este usuário já possui cadastro de parlamentar vinculado.'])
                    ->withInput();
            }
        }

        $validatedData = $request->validate($rules);

        // Definir status padrão para novos parlamentares
        $validatedData['status'] = 'ativo';

        try {
            DB::beginTransaction();

            // Processar upload da foto
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $nomeArquivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $foto->getClientOriginalName());
                $caminhoFoto = $foto->storeAs('public/parlamentares/fotos', $nomeArquivo);
                $validatedData['foto'] = $nomeArquivo;
            }
            
            // Processar comissões (se vier como string)
            if (isset($validatedData['comissoes']) && is_string($validatedData['comissoes'])) {
                $validatedData['comissoes'] = array_filter(explode(',', $validatedData['comissoes']));
            }

            // Se deve criar usuário junto com o parlamentar
            if ($request->boolean('criar_usuario') && $validatedData['email']) {
                if (!$validatedData['usuario_password']) {
                    throw new \Exception('Senha do usuário é obrigatória quando criar usuário está marcado.');
                }

                $userData = [
                    'name' => $validatedData['nome'],
                    'email' => $validatedData['email'],
                    'password' => Hash::make($validatedData['usuario_password']),
                    'documento' => $validatedData['cpf'] ?? null,
                    'telefone' => $validatedData['telefone'],
                    'data_nascimento' => $validatedData['data_nascimento'],
                    'profissao' => $validatedData['profissao'],
                    'partido' => $validatedData['partido'],
                    'ativo' => true,
                ];

                $user = User::create($userData);
                $user->assignRole(User::PERFIL_PARLAMENTAR);
                
                $validatedData['user_id'] = $user->id;
            } elseif ($request->user_id) {
                // Vincular a usuário existente
                $validatedData['user_id'] = $request->user_id;
            }
            
            // Remover campos que não pertencem ao model Parlamentar
            unset($validatedData['criar_usuario'], $validatedData['usuario_password'], $validatedData['usuario_password_confirmation']);
            
            // Remover campos vazios para evitar problemas
            $validatedData = array_filter($validatedData, function($value) {
                return $value !== null && $value !== '';
            });
            
            // Re-adicionar status pois pode ter sido removido
            $validatedData['status'] = 'ativo';
            
            $parlamentar = $this->parlamentarService->create($validatedData);
            
            // Processar certificado digital se fornecido
            if ($request->boolean('upload_certificado') && $request->hasFile('certificado_digital_create')) {
                try {
                    $this->processarCertificadoDigitalCreate($request, $parlamentar['id']);
                } catch (\Exception $e) {
                    \Log::warning('Erro ao processar certificado digital na criação do parlamentar', [
                        'parlamentar_id' => $parlamentar['id'],
                        'error' => $e->getMessage()
                    ]);
                    // Não falhar a criação do parlamentar por causa do certificado
                }
            }
            
            DB::commit();
            
            return redirect()->route('parlamentares.show', $parlamentar['id'])
                ->with('success', 'Parlamentar criado com sucesso!');
                
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao criar parlamentar: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Exibir formulário de edição
     */
    public function edit(int $id): View|RedirectResponse
    {
        try {
            $parlamentar = $this->parlamentarService->getById($id);
            
            return view('modules.parlamentares.edit', [
                'parlamentar' => $parlamentar,
                'title' => 'Editar Parlamentar - ' . $parlamentar['nome'],
                'partidos' => $this->getPartidosOptions(),
                'cargos' => $this->getCargosOptions(),
                'statusOptions' => $this->getStatusOptions(),
                'escolaridadeOptions' => $this->getEscolaridadeOptions()
            ]);
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Parlamentar não encontrado.');
        } catch (\Exception $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Erro ao carregar parlamentar: ' . $e->getMessage());
        }
    }
    
    /**
     * Atualizar parlamentar
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        // Debug: verificar se arquivo foi recebido
        \Log::info('ParlamentarController@update - Request recebido', [
            'has_certificado' => $request->hasFile('certificado_digital'),
            'files' => $request->allFiles(),
            'certificado_senha' => $request->has('certificado_senha') ? 'sim' : 'não'
        ]);
        
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'nome_politico' => 'nullable|string|max:255',
            'partido' => 'required|string|max:50',
            'cargo' => 'required|string|max:100',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:parlamentars,email,' . $id,
            'cpf' => 'nullable|string|max:14|unique:parlamentars,cpf,' . $id,
            'data_nascimento' => 'nullable|date|before:today',
            'profissao' => 'nullable|string|max:100',
            'escolaridade' => 'nullable|string|max:100',
            'status' => 'required|in:ativo,licenciado,inativo',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'comissoes' => 'nullable|string',
            'certificado_digital' => 'nullable|file|max:5120',
            'certificado_senha' => 'nullable|string|min:4',
        ]);


        // Debug: log dados validados
        // Log::info('Dados validados para atualização:', $validatedData);

        try {
            // Processar upload da foto
            if ($request->hasFile('foto')) {
                // Buscar parlamentar atual para deletar foto antiga
                $parlamentarAtual = $this->parlamentarService->getById($id);
                if (!empty($parlamentarAtual['foto']) && Storage::exists('public/parlamentares/fotos/' . $parlamentarAtual['foto'])) {
                    Storage::delete('public/parlamentares/fotos/' . $parlamentarAtual['foto']);
                }
                
                // Upload da nova foto
                $foto = $request->file('foto');
                $nomeArquivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $foto->getClientOriginalName());
                $caminhoFoto = $foto->storeAs('public/parlamentares/fotos', $nomeArquivo);
                $validatedData['foto'] = $nomeArquivo;
            }
            
            // Processar comissões (se vier como string)
            if (isset($validatedData['comissoes']) && is_string($validatedData['comissoes'])) {
                $validatedData['comissoes'] = array_filter(explode(',', $validatedData['comissoes']));
            }
            
            // Para atualização, manter campos vazios pois podem ser para limpar dados
            // Log::info('Dados finais para atualização:', $validatedData);
            
            // Processar certificado digital se fornecido
            try {
                if ($request->hasFile('certificado_digital')) {
                    \Log::info('Processando certificado digital', [
                        'parlamentar_id' => $id,
                        'arquivo_valido' => $request->file('certificado_digital')->isValid(),
                        'tamanho' => $request->file('certificado_digital')->getSize()
                    ]);
                    $this->processarCertificadoDigital($request, $id);
                }
            } catch (\Exception $e) {
                \Log::warning('Erro ao processar certificado digital do parlamentar', [
                    'parlamentar_id' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Não falhar a atualização do parlamentar por causa do certificado
            }
            
            $parlamentar = $this->parlamentarService->update($id, $validatedData);
            
            return redirect()->route('parlamentares.show', $id)
                ->with('success', 'Parlamentar atualizado com sucesso!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar parlamentar: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Deletar parlamentar
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Verificar se o parlamentar existe antes de deletar
            $parlamentar = $this->parlamentarService->getById($id);
            
            $this->parlamentarService->delete($id);
            
            return redirect()->route('parlamentares.index')
                ->with('success', "Parlamentar {$parlamentar['nome']} deletado com sucesso!");
                
        } catch (ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Parlamentar não encontrado.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao deletar parlamentar: ' . $e->getMessage());
        }
    }
    
    /**
     * Remover certificado digital do parlamentar
     */
    public function removerCertificado(Request $request, int $id)
    {
        try {
            // Verificar se usuário tem permissão
            $userLogado = auth()->user();
            $parlamentar = $this->parlamentarService->getById($id);
            
            if (!isset($parlamentar['user_id']) || !$parlamentar['user_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parlamentar não possui usuário vinculado'
                ], 400);
            }
            
            $usuario = User::find($parlamentar['user_id']);
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário do parlamentar não encontrado'
                ], 404);
            }
            
            // Verificar permissões
            $isAdmin = $userLogado->hasRole('ADMIN');
            $isProprioParlamentar = $userLogado->id == $usuario->id;
            
            if (!$isAdmin && !$isProprioParlamentar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sem permissão para remover este certificado'
                ], 403);
            }
            
            // Remover certificado
            if ($usuario->removerCertificadoDigital()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Certificado removido com sucesso'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover certificado'
            ], 500);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao remover certificado do parlamentar', [
                'parlamentar_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover certificado: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Exibir parlamentares por partido
     */
    public function porPartido(string $partido): View
    {
        try {
            $parlamentares = $this->parlamentarService->getByPartido($partido);
            
            $parlamentaresFormatados = $parlamentares->map(function ($parlamentar) {
                return $this->parlamentarService->formatForDisplay($parlamentar->toArray());
            });
            
            return view('modules.parlamentares.por-partido', [
                'parlamentares' => $parlamentaresFormatados,
                'partido' => strtoupper($partido),
                'title' => 'Parlamentares do ' . strtoupper($partido)
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Erro ao buscar parlamentares: ' . $e->getMessage());
        }
    }
    
    /**
     * Exibir mesa diretora
     */
    public function mesaDiretora(): View
    {
        try {
            $mesaDiretora = $this->parlamentarService->getMesaDiretora();
            
            return view('modules.parlamentares.mesa-diretora', [
                'mesaDiretora' => $mesaDiretora,
                'title' => 'Mesa Diretora'
            ]);
            
        } catch (\Exception $e) {
            return view('modules.parlamentares.mesa-diretora', [
                'mesaDiretora' => collect([]),
                'error' => 'Erro ao carregar mesa diretora: ' . $e->getMessage(),
                'title' => 'Mesa Diretora'
            ]);
        }
    }
    
    /**
     * Buscar parlamentares
     */
    public function search(Request $request): View
    {
        $termo = $request->get('q', '');
        
        try {
            if (empty($termo)) {
                return redirect()->route('parlamentares.index');
            }
            
            $parlamentares = $this->parlamentarService->search($termo);
            
            $parlamentaresFormatados = $parlamentares->map(function ($parlamentar) {
                return $this->parlamentarService->formatForDisplay($parlamentar->toArray());
            });
            
            return view('modules.parlamentares.search', [
                'parlamentares' => $parlamentaresFormatados,
                'termo' => $termo,
                'total' => $parlamentares->count(),
                'title' => 'Busca por: ' . $termo
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Erro na busca: ' . $e->getMessage());
        }
    }
    
    /**
     * Obter opções de partidos
     */
    private function getPartidosOptions(): array
    {
        return \App\Models\Partido::ativos()
            ->orderBy('sigla')
            ->pluck('nome', 'sigla')
            ->toArray();
    }
    
    /**
     * Obter opções de cargos
     */
    private function getCargosOptions(): array
    {
        return [
            'Vereador' => 'Vereador',
            'Vereadora' => 'Vereadora',
            'Presidente da Câmara' => 'Presidente da Câmara',
            'Vice-Presidente' => 'Vice-Presidente',
            '1º Secretário' => '1º Secretário',
            '2º Secretário' => '2º Secretário'
        ];
    }
    
    /**
     * Obter opções de status
     */
    private function getStatusOptions(): array
    {
        return [
            'ativo' => 'Ativo',
            'licenciado' => 'Licenciado',
            'inativo' => 'Inativo'
        ];
    }
    
    /**
     * Obter opções de escolaridade
     */
    private function getEscolaridadeOptions(): array
    {
        return [
            'Ensino Fundamental' => 'Ensino Fundamental',
            'Ensino Médio' => 'Ensino Médio',
            'Superior Incompleto' => 'Superior Incompleto',
            'Superior Completo' => 'Superior Completo',
            'Pós-Graduação' => 'Pós-Graduação',
            'Mestrado' => 'Mestrado',
            'Doutorado' => 'Doutorado'
        ];
    }

    /**
     * Exportar parlamentares para CSV
     */
    public function exportCsv(): \Symfony\Component\HttpFoundation\Response
    {
        try {
            $parlamentares = $this->parlamentarService->getAll();
            
            $csvData = [];
            $csvData[] = ['Nome', 'Partido', 'Cargo', 'Status', 'Email', 'Telefone', 'Data Nascimento', 'Profissão', 'Escolaridade'];
            
            foreach ($parlamentares as $parlamentar) {
                $csvData[] = [
                    $parlamentar['nome'],
                    $parlamentar['partido'],
                    $parlamentar['cargo'],
                    $parlamentar['status'],
                    $parlamentar['email'],
                    $parlamentar['telefone'],
                    $parlamentar['data_nascimento'],
                    $parlamentar['profissao'] ?? '',
                    $parlamentar['escolaridade'] ?? ''
                ];
            }
            
            $filename = 'parlamentares_' . date('Y-m-d_H-i-s') . '.csv';
            
            $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() use ($csvData) {
                $handle = fopen('php://output', 'w');
                foreach ($csvData as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
            });
            
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
            return $response;
            
        } catch (\Exception $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Erro ao exportar dados: ' . $e->getMessage());
        }
    }

    /**
     * Obter estatísticas avançadas
     */
    public function estatisticas(): View|RedirectResponse
    {
        try {
            $estatisticas = $this->parlamentarService->getEstatisticas();
            $parlamentares = $this->parlamentarService->getAll();
            
            // Estatísticas por idade
            $idades = $parlamentares->map(function ($parlamentar) {
                return \Carbon\Carbon::parse($parlamentar['data_nascimento'])->age;
            });
            
            $estatisticasAvancadas = [
                'idade_media' => round($idades->avg(), 1),
                'idade_min' => $idades->min(),
                'idade_max' => $idades->max(),
                'por_faixa_etaria' => [
                    '20-30' => $idades->filter(fn($idade) => $idade >= 20 && $idade < 30)->count(),
                    '30-40' => $idades->filter(fn($idade) => $idade >= 30 && $idade < 40)->count(),
                    '40-50' => $idades->filter(fn($idade) => $idade >= 40 && $idade < 50)->count(),
                    '50-60' => $idades->filter(fn($idade) => $idade >= 50 && $idade < 60)->count(),
                    '60+' => $idades->filter(fn($idade) => $idade >= 60)->count(),
                ],
                'por_genero' => [
                    'masculino' => $parlamentares->filter(fn($p) => !str_ends_with($p['cargo'], 'a'))->count(),
                    'feminino' => $parlamentares->filter(fn($p) => str_ends_with($p['cargo'], 'a'))->count(),
                ],
                'por_escolaridade' => $parlamentares->groupBy('escolaridade')->map->count()->toArray(),
                'tempo_mandato' => $parlamentares->map(function ($parlamentar) {
                    $mandatos = $parlamentar['mandatos'] ?? [];
                    return count($mandatos);
                })->sum(),
            ];
            
            return view('modules.parlamentares.estatisticas', [
                'estatisticas' => array_merge($estatisticas, $estatisticasAvancadas),
                'title' => 'Estatísticas dos Parlamentares'
            ]);
            
        } catch (\Exception $e) {
            return redirect()->route('parlamentares.index')
                ->with('error', 'Erro ao carregar estatísticas: ' . $e->getMessage());
        }
    }

    /**
     * API de busca de parlamentares para autocomplete
     */
    public function apiSearch(Request $request)
    {
        try {
            $termo = $request->get('q', '');
            
            if (empty($termo) || strlen($termo) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Termo de busca deve ter pelo menos 2 caracteres',
                    'parlamentares' => []
                ]);
            }

            $parlamentares = $this->parlamentarService->search($termo);
            
            $parlamentaresFormatados = $parlamentares->map(function ($parlamentar) {
                return [
                    'id' => $parlamentar->id,
                    'nome' => $parlamentar->nome,
                    'nome_politico' => $parlamentar->nome_politico,
                    'partido' => $parlamentar->partido,
                    'cargo' => $parlamentar->cargo,
                    'status' => $parlamentar->status,
                    'display_name' => $parlamentar->nome_politico ? 
                        "{$parlamentar->nome_politico} ({$parlamentar->nome})" : $parlamentar->nome,
                    'partido_cargo' => "{$parlamentar->partido} - {$parlamentar->cargo}"
                ];
            });

            return response()->json([
                'success' => true,
                'parlamentares' => $parlamentaresFormatados,
                'total' => $parlamentares->count(),
                'message' => $parlamentares->count() > 0 ? 
                    "Encontrados {$parlamentares->count()} parlamentares" : 
                    'Nenhum parlamentar encontrado'
            ]);

        } catch (\Exception $e) {
            // Log::error('Erro na busca de parlamentares via API', [
                //     'erro' => $e->getMessage(),
                //     'termo' => $request->get('q', '')
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
                'parlamentares' => [],
                'error' => config('app.debug') ? $e->getMessage() : 'Erro interno'
            ], 500);
        }
    }
    
    /**
     * Processar upload de certificado digital do parlamentar
     */
    private function processarCertificadoDigital(Request $request, int $parlamentarId): void
    {
        try {
            // Obter parlamentar e seu usuário vinculado
            $parlamentar = $this->parlamentarService->getById($parlamentarId);
            
            if (!isset($parlamentar['user_id']) || !$parlamentar['user_id']) {
                throw new \Exception('Este parlamentar não possui usuário vinculado para receber o certificado.');
            }
            
            $usuario = User::find($parlamentar['user_id']);
            if (!$usuario) {
                throw new \Exception('Usuário vinculado ao parlamentar não encontrado.');
            }
            
            // Validar arquivo e senha
            $arquivo = $request->file('certificado_digital');
            $senha = $request->input('certificado_senha');
            
            // Validar extensão do arquivo
            if ($arquivo) {
                $extensao = strtolower($arquivo->getClientOriginalExtension());
                if (!in_array($extensao, ['pfx', 'p12'])) {
                    throw new \Exception('O arquivo deve ter extensão .pfx ou .p12.');
                }
            }
            
            if (!$senha) {
                throw new \Exception('Senha do certificado é obrigatória.');
            }
            
            // Processar certificado diretamente usando os mesmos métodos do CertificadoDigitalController
            // Simular autenticação do usuário do parlamentar temporariamente
            $usuarioAnterior = auth()->user();
            auth()->login($usuario);
            
            try {
                // Usar diretório temporário do sistema em vez do storage
                $nomeTemp = 'cert_temp_' . time() . '_' . uniqid() . '.pfx';
                $caminhoCompletoTemp = sys_get_temp_dir() . '/' . $nomeTemp;
                
                // Salvar conteúdo do arquivo no diretório temporário do sistema
                file_put_contents($caminhoCompletoTemp, file_get_contents($arquivo->getRealPath()));
                
                $caminhoTemp = 'temp/' . $nomeTemp; // mantido para compatibilidade de log
                
                // Debug: log do processo de salvamento
                \Log::info('ParlamentarController: Salvando arquivo temporário', [
                    'nome_original' => $arquivo->getClientOriginalName(),
                    'caminho_temp' => $caminhoTemp,
                    'caminho_completo' => $caminhoCompletoTemp,
                    'arquivo_existe' => file_exists($caminhoCompletoTemp),
                    'eh_arquivo' => is_file($caminhoCompletoTemp),
                    'tamanho' => file_exists($caminhoCompletoTemp) ? filesize($caminhoCompletoTemp) : 'N/A'
                ]);
                
                // Instanciar controller para usar método de validação
                $certificadoController = app(\App\Http\Controllers\CertificadoDigitalController::class);
                $reflection = new \ReflectionClass($certificadoController);
                $validarMethod = $reflection->getMethod('validarCertificado');
                $validarMethod->setAccessible(true);
                
                // Validar certificado
                $validacao = $validarMethod->invoke($certificadoController, $caminhoCompletoTemp, $senha);
                
                if (!$validacao['valido']) {
                    @unlink($caminhoCompletoTemp); // Deletar arquivo temporário
                    throw new \Exception('Certificado inválido: ' . $validacao['erro']);
                }
                
                // Remover certificado anterior se existir
                if ($usuario->certificado_digital_path && Storage::exists($usuario->certificado_digital_path)) {
                    Storage::delete($usuario->certificado_digital_path);
                }
                
                // Gerar nome único para o arquivo final
                $nomeArquivo = 'certificado_' . $usuario->id . '_' . time() . '.pfx';
                $caminhoRelativo = 'certificados-digitais/' . $nomeArquivo;
                
                // Mover para pasta definitiva usando rename do PHP já que o arquivo foi movido fisicamente
                $destino = storage_path('app/private/' . $caminhoRelativo);
                $diretorioDestino = dirname($destino);
                
                // Criar diretório se não existir
                if (!is_dir($diretorioDestino)) {
                    mkdir($diretorioDestino, 0755, true);
                }
                
                // Copiar arquivo para destino final (mais seguro que rename entre filesystems)
                if (!copy($caminhoCompletoTemp, $destino)) {
                    @unlink($caminhoCompletoTemp);
                    throw new \Exception('Erro ao mover certificado para destino final');
                }
                
                // Deletar arquivo temporário
                @unlink($caminhoCompletoTemp);
                
                // Atualizar dados do usuário
                $usuario->update([
                    'certificado_digital_path' => $caminhoRelativo,
                    'certificado_digital_nome' => $arquivo->getClientOriginalName(),
                    'certificado_digital_upload_em' => now(),
                    'certificado_digital_validade' => $validacao['validade'] ?? null,
                    'certificado_digital_cn' => $validacao['cn'] ?? null,
                    'certificado_digital_ativo' => true,
                ]);
                
                // Verificar se deve salvar a senha do certificado
                if ($request->has('salvar_senha_certificado') && $request->input('salvar_senha_certificado') == '1') {
                    $usuario->salvarSenhaCertificado($senha);
                    \Log::info('Senha do certificado salva para parlamentar', [
                        'parlamentar_id' => $parlamentarId,
                        'usuario_id' => $usuario->id
                    ]);
                }
                
                \Log::info('Certificado digital processado com sucesso para parlamentar', [
                    'parlamentar_id' => $parlamentarId,
                    'usuario_id' => $usuario->id
                ]);
                
            } finally {
                // Restaurar autenticação anterior
                if ($usuarioAnterior) {
                    auth()->login($usuarioAnterior);
                } else {
                    auth()->logout();
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Erro ao processar certificado digital do parlamentar', [
                'parlamentar_id' => $parlamentarId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Processar upload de certificado digital na criação do parlamentar
     */
    private function processarCertificadoDigitalCreate(Request $request, int $parlamentarId): void
    {
        try {
            // Obter parlamentar recém-criado
            $parlamentar = $this->parlamentarService->getById($parlamentarId);
            
            if (!isset($parlamentar['user_id']) || !$parlamentar['user_id']) {
                throw new \Exception('Parlamentar criado não possui usuário vinculado para receber o certificado.');
            }
            
            $usuario = User::find($parlamentar['user_id']);
            if (!$usuario) {
                throw new \Exception('Usuário vinculado ao parlamentar não encontrado.');
            }
            
            // Validar arquivo e senha
            $arquivo = $request->file('certificado_digital_create');
            $senha = $request->input('certificado_senha_create');
            
            if (!$arquivo || !$senha) {
                throw new \Exception('Arquivo do certificado e senha são obrigatórios.');
            }
            
            // Validar extensão do arquivo
            $extensao = strtolower($arquivo->getClientOriginalExtension());
            if (!in_array($extensao, ['pfx', 'p12'])) {
                throw new \Exception('O arquivo deve ter extensão .pfx ou .p12.');
            }
            
            // Usar o CertificadoDigitalController para processar o upload
            $certificadoController = app(\App\Http\Controllers\CertificadoDigitalController::class);
            
            // Criar request personalizado para o controller de certificado
            $certificadoRequest = new Request();
            $certificadoRequest->files->set('certificado', $arquivo);
            $certificadoRequest->request->set('senha_teste', $senha);
            $certificadoRequest->setMethod('POST');
            
            // Simular autenticação do usuário do parlamentar temporariamente
            $usuarioAnterior = auth()->user();
            auth()->login($usuario);
            
            try {
                // Processar upload usando o controller existente
                $response = $certificadoController->upload($certificadoRequest);
                
                // Verificar se houve erro
                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    $errors = $response->getSession()->get('errors');
                    if ($errors) {
                        throw new \Exception('Erro ao processar certificado: ' . $errors->first());
                    }
                }
                
                // Verificar se deve salvar a senha do certificado
                if ($request->has('salvar_senha_certificado_create') && $request->input('salvar_senha_certificado_create') == '1') {
                    $usuario->salvarSenhaCertificado($senha);
                    \Log::info('Senha do certificado salva na criação do parlamentar', [
                        'parlamentar_id' => $parlamentarId,
                        'usuario_id' => $usuario->id
                    ]);
                }
                
                \Log::info('Certificado digital processado com sucesso na criação do parlamentar', [
                    'parlamentar_id' => $parlamentarId,
                    'usuario_id' => $usuario->id
                ]);
                
            } finally {
                // Restaurar autenticação anterior
                if ($usuarioAnterior) {
                    auth()->login($usuarioAnterior);
                } else {
                    auth()->logout();
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Erro ao processar certificado digital na criação do parlamentar', [
                'parlamentar_id' => $parlamentarId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}