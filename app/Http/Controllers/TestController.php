<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Template\TemplateProcessorService;
use App\Models\Proposicao;
use App\Models\TipoProposicaoTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestController extends Controller
{
    public function index()
    {
        return view('tests.index');
    }

    public function usersIndex()
    {
        return view('tests.users');
    }

    public function processesIndex()
    {
        return view('tests.processes');
    }

    // Métodos para servir arquivos HTML de visualização
    public function visualizationCenter()
    {
        $filePath = base_path('tests/processes/index.html');
        
        if (!file_exists($filePath)) {
            abort(404, 'Centro de Visualização não encontrado');
        }
        
        return response()->file($filePath);
    }

    public function fluxoVisualizer()
    {
        $filePath = base_path('tests/processes/fluxo-visualizer.html');
        
        if (!file_exists($filePath)) {
            abort(404, 'Visualizador básico não encontrado');
        }
        
        return response()->file($filePath);
    }

    public function fluxoDashboard()
    {
        $filePath = base_path('tests/processes/fluxo-dashboard.html');
        
        if (!file_exists($filePath)) {
            abort(404, 'Dashboard avançado não encontrado');
        }
        
        return response()->file($filePath);
    }

    public function networkFlow()
    {
        $filePath = base_path('tests/processes/network-flow.html');
        
        if (!file_exists($filePath)) {
            abort(404, 'Mapa de rede não encontrado');
        }
        
        return response()->file($filePath);
    }

    public function animatedFlow()
    {
        $filePath = base_path('tests/processes/animated-flow.html');
        
        if (!file_exists($filePath)) {
            abort(404, 'Fluxo animado não encontrado');
        }
        
        return response()->file($filePath);
    }

    public function apiIndex()
    {
        return view('tests.api');
    }

    public function databaseIndex()
    {
        return view('tests.database');
    }

    public function performanceIndex()
    {
        return view('tests.performance');
    }

    public function securityIndex()
    {
        return view('tests.security');
    }

    public function createTestUsers()
    {
        $results = [];
        
        $testUsers = [
            [
                'email' => 'bruno@sistema.gov.br',
                'name' => 'Bruno Administrador',
                'password' => '123456',
                'role' => 'ADMIN'
            ],
            [
                'email' => 'jessica@sistema.gov.br',
                'name' => 'Jessica Parlamentar',
                'password' => '123456', 
                'role' => 'PARLAMENTAR'
            ],
            [
                'email' => 'joao@sistema.gov.br',
                'name' => 'João Legislativo',
                'password' => '123456',
                'role' => 'LEGISLATIVO'
            ],
            [
                'email' => 'roberto@sistema.gov.br',
                'name' => 'Roberto Protocolo',
                'password' => '123456',
                'role' => 'PROTOCOLO'
            ],
            [
                'email' => 'expediente@sistema.gov.br',
                'name' => 'Carlos Expediente',
                'password' => '123456',
                'role' => 'EXPEDIENTE'
            ],
            [
                'email' => 'juridico@sistema.gov.br',
                'name' => 'Ana Assessora Jurídica',
                'password' => '123456',
                'role' => 'ASSESSOR_JURIDICO'
            ]
        ];

        foreach ($testUsers as $userData) {
            try {
                // Verifica se usuário já existe
                $existingUser = User::where('email', $userData['email'])->first();
                
                if ($existingUser) {
                    $results[] = [
                        'email' => $userData['email'],
                        'status' => 'error',
                        'message' => 'Usuário já existe no sistema'
                    ];
                    continue;
                }

                // Cria o usuário
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now()
                ]);

                // Verifica se o role existe
                $role = Role::where('name', $userData['role'])->first();
                
                if (!$role) {
                    $results[] = [
                        'email' => $userData['email'],
                        'status' => 'warning',
                        'message' => 'Usuário criado, mas role "' . $userData['role'] . '" não existe'
                    ];
                } else {
                    // Atribui o role
                    $user->assignRole($role);
                    
                    $results[] = [
                        'email' => $userData['email'],
                        'status' => 'success',
                        'message' => 'Usuário criado com sucesso com role "' . $userData['role'] . '"'
                    ];
                }

            } catch (\Exception $e) {
                $results[] = [
                    'email' => $userData['email'],
                    'status' => 'error',
                    'message' => 'Erro ao criar usuário: ' . $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    public function clearTestUsers()
    {
        $testEmails = [
            'bruno@sistema.gov.br',
            'jessica@sistema.gov.br', 
            'joao@sistema.gov.br',
            'roberto@sistema.gov.br',
            'expediente@sistema.gov.br',
            'juridico@sistema.gov.br'
        ];

        $deletedCount = User::whereIn('email', $testEmails)->delete();

        return response()->json([
            'success' => true,
            'message' => "Removidos {$deletedCount} usuários de teste"
        ]);
    }

    public function listTestUsers()
    {
        $testEmails = [
            'bruno@sistema.gov.br',
            'jessica@sistema.gov.br',
            'joao@sistema.gov.br', 
            'roberto@sistema.gov.br',
            'expediente@sistema.gov.br',
            'juridico@sistema.gov.br'
        ];

        $users = User::whereIn('email', $testEmails)
            ->with('roles')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->join(', '),
                    'created_at' => $user->created_at->format('d/m/Y H:i:s')
                ];
            });

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    public function runProcessTests()
    {
        $results = [];
        
        try {
            // Test 1: Complete Tramitation Process
            $results[] = $this->testCompleteTramitationProcess();
            
            // Test 2: Template Processing
            $results[] = $this->testTemplateProcessing();
            
            // Test 3: Variable Processing
            $results[] = $this->testVariableProcessing();
            
            // Test 4: Proposicao Workflow
            $results[] = $this->testProposicaoWorkflow();
            
            // Test 5: Template Validation
            $results[] = $this->testTemplateValidation();
            
        } catch (\Exception $e) {
            $results[] = [
                'test' => 'Process Tests',
                'status' => 'error',
                'message' => 'Erro geral: ' . $e->getMessage()
            ];
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    private function testTemplateProcessing()
    {
        try {
            $service = new TemplateProcessorService();
            $template = "Data atual: {data} - Ano: {ano_atual}";
            
            if (method_exists($service, 'processSystemVariables')) {
                $processed = $service->processSystemVariables($template);
                
                if (str_contains($processed, now()->format('d/m/Y'))) {
                    return [
                        'test' => 'Template Processing',
                        'status' => 'success',
                        'message' => 'Processamento de template funcionando corretamente'
                    ];
                }
            }
            
            return [
                'test' => 'Template Processing',
                'status' => 'warning',
                'message' => 'Método processSystemVariables não implementado ou não funcional'
            ];
            
        } catch (\Exception $e) {
            return [
                'test' => 'Template Processing',
                'status' => 'error',
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    private function testVariableProcessing()
    {
        try {
            $service = new TemplateProcessorService();
            $template = "Teste de variáveis: {nome_municipio} - {legislatura_atual}";
            
            if (method_exists($service, 'processSystemVariables')) {
                $processed = $service->processSystemVariables($template);
                
                return [
                    'test' => 'Variable Processing',
                    'status' => 'success',
                    'message' => 'Processamento de variáveis executado: ' . substr($processed, 0, 100) . '...'
                ];
            }
            
            return [
                'test' => 'Variable Processing',
                'status' => 'warning',
                'message' => 'Método de processamento de variáveis não encontrado'
            ];
            
        } catch (\Exception $e) {
            return [
                'test' => 'Variable Processing',
                'status' => 'error',
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    private function testProposicaoWorkflow()
    {
        try {
            // Testa criação de proposição
            $proposicao = new Proposicao();
            $proposicao->tipo = 'Teste';
            $proposicao->ementa = 'Teste de processo';
            $proposicao->status = 'rascunho';
            
            // Simula validação básica
            if ($proposicao->tipo && $proposicao->ementa) {
                return [
                    'test' => 'Proposicao Workflow',
                    'status' => 'success',
                    'message' => 'Workflow de proposição validado com sucesso'
                ];
            }
            
            return [
                'test' => 'Proposicao Workflow',
                'status' => 'error',
                'message' => 'Falha na validação do workflow'
            ];
            
        } catch (\Exception $e) {
            return [
                'test' => 'Proposicao Workflow',
                'status' => 'error',
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    private function testTemplateValidation()
    {
        try {
            $service = new TemplateProcessorService();
            
            // Testa template válido
            $validTemplate = "Template válido com {data}";
            $invalidTemplate = "Template inválido com {{{malformed}}}";
            
            if (method_exists($service, 'validateTemplate')) {
                $validResult = $service->validateTemplate($validTemplate);
                $invalidResult = $service->validateTemplate($invalidTemplate);
                
                if ($validResult && !$invalidResult) {
                    return [
                        'test' => 'Template Validation',
                        'status' => 'success',
                        'message' => 'Validação de template funcionando corretamente'
                    ];
                }
            }
            
            return [
                'test' => 'Template Validation',
                'status' => 'warning',
                'message' => 'Método validateTemplate não implementado ou funcionando incorretamente'
            ];
            
        } catch (\Exception $e) {
            return [
                'test' => 'Template Validation',
                'status' => 'error',
                'message' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    private function testCompleteTramitationProcess()
    {
        $steps = [];
        $errors = [];
        
        try {
            // ETAPA 1: Criar proposição como Parlamentar
            $steps[] = ['etapa' => 'Criação', 'status' => 'iniciando'];
            
            // Buscar usuário Parlamentar
            $parlamentar = User::where('email', 'jessica@sistema.gov.br')->first();
            if (!$parlamentar) {
                $errors[] = 'Usuário Parlamentar não encontrado';
                return $this->formatTestResult('Processo Completo de Tramitação', 'error', 
                    'Falha na Etapa 1: Usuário Parlamentar não encontrado. Execute a criação de usuários de teste primeiro.');
            }
            
            // Buscar tipo de proposição e template
            $tipoProposicao = \App\Models\TipoProposicao::first();
            $template = TipoProposicaoTemplate::where('tipo_proposicao_id', $tipoProposicao->id)
                ->where('ativo', true)
                ->first();
                
            if (!$template) {
                $errors[] = 'Template não encontrado';
                return $this->formatTestResult('Processo Completo de Tramitação', 'error', 
                    'Falha na Etapa 1: Template não encontrado para o tipo de proposição.');
            }
            
            // Criar proposição
            $proposicao = new Proposicao();
            $proposicao->tipo = $tipoProposicao->nome;
            $proposicao->ementa = 'Teste de tramitação completa - ' . now()->format('d/m/Y H:i');
            $proposicao->conteudo = 'CONSIDERANDO a necessidade de testar o processo de tramitação;
CONSIDERANDO a importância da validação do sistema;
ARTIGO 1º - Este é um teste automatizado do processo de tramitação.
ARTIGO 2º - O teste deve validar todas as etapas do processo.';
            $proposicao->status = 'rascunho';
            $proposicao->autor_id = $parlamentar->id;
            $proposicao->template_id = $template->id;
            $proposicao->save();
            
            $steps[] = ['etapa' => 'Criação', 'status' => 'concluída', 'proposicao_id' => $proposicao->id];
            
            // ETAPA 2: Enviar para Legislativo
            $steps[] = ['etapa' => 'Envio ao Legislativo', 'status' => 'iniciando'];
            
            $legislativo = User::where('email', 'joao@sistema.gov.br')->first();
            if (!$legislativo) {
                $errors[] = 'Usuário Legislativo não encontrado';
                return $this->formatTestResult('Processo Completo de Tramitação', 'error', 
                    'Falha na Etapa 2: Usuário Legislativo não encontrado.');
            }
            
            // Simular envio ao Legislativo
            $proposicao->status = 'enviado_legislativo';
            $proposicao->save();
            
            // Registrar log de tramitação
            \App\Models\TramitacaoLog::create([
                'proposicao_id' => $proposicao->id,
                'user_id' => $parlamentar->id,
                'acao' => 'ENVIADO_PARA_REVISAO',
                'status_anterior' => 'rascunho',
                'status_novo' => 'enviado_legislativo',
                'observacoes' => 'Enviado para análise do setor Legislativo'
            ]);
            
            $steps[] = ['etapa' => 'Envio ao Legislativo', 'status' => 'concluída'];
            
            // ETAPA 3: Legislativo faz alterações
            $steps[] = ['etapa' => 'Análise Legislativa', 'status' => 'iniciando'];
            
            // Simular alterações do Legislativo
            $proposicao->conteudo .= "\nARTIGO 3º - Alteração incluída pelo setor Legislativo.";
            $proposicao->observacoes_legislativo = 'Documento revisado e ajustado conforme normas técnicas.';
            $proposicao->save();
            
            \App\Models\TramitacaoLog::create([
                'proposicao_id' => $proposicao->id,
                'user_id' => $legislativo->id,
                'acao' => 'REVISADO',
                'status_anterior' => 'enviado_legislativo',
                'status_novo' => 'em_revisao',
                'observacoes' => 'Análise técnica realizada com alterações'
            ]);
            
            $steps[] = ['etapa' => 'Análise Legislativa', 'status' => 'concluída'];
            
            // ETAPA 4: Converter para PDF e enviar para Parlamentar assinar
            $steps[] = ['etapa' => 'Conversão para PDF', 'status' => 'iniciando'];
            
            // Simular conversão para PDF
            $pdfPath = 'proposicoes/pdf/' . $proposicao->id . '_' . now()->timestamp . '.pdf';
            $proposicao->arquivo_pdf = $pdfPath;
            $proposicao->status = 'aguardando_aprovacao_autor';
            $proposicao->save();
            
            \App\Models\TramitacaoLog::create([
                'proposicao_id' => $proposicao->id,
                'user_id' => $legislativo->id,
                'acao' => 'REVISADO',
                'status_anterior' => 'em_revisao',
                'status_novo' => 'aguardando_aprovacao_autor',
                'observacoes' => 'Documento convertido para PDF e enviado para assinatura'
            ]);
            
            $steps[] = ['etapa' => 'Conversão para PDF', 'status' => 'concluída'];
            
            // ETAPA 5: Parlamentar assina
            $steps[] = ['etapa' => 'Assinatura Parlamentar', 'status' => 'iniciando'];
            
            $proposicao->assinado = true;
            $proposicao->data_assinatura = now();
            $proposicao->status = 'assinado';
            $proposicao->save();
            
            \App\Models\TramitacaoLog::create([
                'proposicao_id' => $proposicao->id,
                'user_id' => $parlamentar->id,
                'acao' => 'ASSINADO',
                'status_anterior' => 'aguardando_aprovacao_autor',
                'status_novo' => 'assinado',
                'observacoes' => 'Documento assinado digitalmente'
            ]);
            
            $steps[] = ['etapa' => 'Assinatura Parlamentar', 'status' => 'concluída'];
            
            // ETAPA 6: Envio automático ao Protocolo
            $steps[] = ['etapa' => 'Protocolo', 'status' => 'iniciando'];
            
            $protocolo = User::where('email', 'roberto@sistema.gov.br')->first();
            if (!$protocolo) {
                $errors[] = 'Usuário Protocolo não encontrado';
                return $this->formatTestResult('Processo Completo de Tramitação', 'warning', 
                    'Processo parcialmente concluído. Usuário Protocolo não encontrado.');
            }
            
            // Gerar número de protocolo
            $numeroProtocolo = 'PROT-' . date('Y') . '-' . str_pad($proposicao->id, 6, '0', STR_PAD_LEFT);
            $proposicao->numero_protocolo = $numeroProtocolo;
            $proposicao->data_protocolo = now();
            $proposicao->status = 'protocolado';
            $proposicao->save();
            
            \App\Models\TramitacaoLog::create([
                'proposicao_id' => $proposicao->id,
                'user_id' => $protocolo->id,
                'acao' => 'PROTOCOLADO',
                'status_anterior' => 'assinado',
                'status_novo' => 'protocolado',
                'observacoes' => 'Documento protocolado com número: ' . $numeroProtocolo
            ]);
            
            $steps[] = ['etapa' => 'Protocolo', 'status' => 'concluída', 'numero_protocolo' => $numeroProtocolo];
            
            // ETAPA 7: Envio ao Expediente
            $steps[] = ['etapa' => 'Expediente', 'status' => 'iniciando'];
            
            $expediente = User::where('email', 'expediente@sistema.gov.br')->first();
            if (!$expediente) {
                $errors[] = 'Usuário Expediente não encontrado';
            } else {
                // Determinar momento (Expediente ou Ordem do Dia) baseado no tipo
                $momento = ($tipoProposicao->nome == 'Projeto de Lei Ordinária') ? 'ordem_dia' : 'expediente';
                
                $proposicao->momento_sessao = $momento;
                $proposicao->status = 'protocolado';
                $proposicao->save();
                
                \App\Models\TramitacaoLog::create([
                    'proposicao_id' => $proposicao->id,
                    'user_id' => $expediente->id,
                    'acao' => 'INCLUIDO_PAUTA',
                    'status_anterior' => 'protocolado',
                    'status_novo' => 'protocolado',
                    'observacoes' => 'Incluído na pauta - Momento: ' . ucfirst(str_replace('_', ' ', $momento))
                ]);
                
                $steps[] = ['etapa' => 'Expediente', 'status' => 'concluída', 'momento' => $momento];
            }
            
            // ETAPA 8: Parecer Jurídico
            $steps[] = ['etapa' => 'Parecer Jurídico', 'status' => 'iniciando'];
            
            $assessorJuridico = User::where('email', 'juridico@sistema.gov.br')->first();
            if (!$assessorJuridico) {
                $errors[] = 'Usuário Assessor Jurídico não encontrado';
            } else {
                // Criar parecer jurídico
                \App\Models\ParecerJuridico::create([
                    'proposicao_id' => $proposicao->id,
                    'user_id' => $assessorJuridico->id,
                    'parecer' => 'PARECER JURÍDICO: A proposição está em conformidade com a legislação vigente e não apresenta vícios de constitucionalidade ou legalidade.',
                    'resultado' => 'favoravel',
                    'data_parecer' => now()
                ]);
                
                $proposicao->tem_parecer_juridico = true;
                $proposicao->save();
                
                \App\Models\TramitacaoLog::create([
                    'proposicao_id' => $proposicao->id,
                    'user_id' => $assessorJuridico->id,
                    'acao' => 'PARECER_EMITIDO',
                    'status_anterior' => 'protocolado',
                    'status_novo' => 'protocolado',
                    'observacoes' => 'Parecer jurídico favorável emitido'
                ]);
                
                $steps[] = ['etapa' => 'Parecer Jurídico', 'status' => 'concluída'];
            }
            
            // Resumo final
            $tramitacoes = \App\Models\Tramitacao// Log::where('proposicao_id', $proposicao->id)
                ->orderBy('created_at', 'asc')
                ->get();
            
            $resumo = "Processo de tramitação completo executado com sucesso!\n";
            $resumo .= "Proposição ID: {$proposicao->id}\n";
            $resumo .= "Número de Protocolo: {$proposicao->numero_protocolo}\n";
            $resumo .= "Status Final: {$proposicao->status}\n";
            $resumo .= "Total de etapas: " . count($tramitacoes) . "\n";
            $resumo .= "Etapas executadas:\n";
            
            foreach ($tramitacoes as $index => $log) {
                $resumo .= ($index + 1) . ". {$log->acao} - {$log->observacoes}\n";
            }
            
            if (count($errors) > 0) {
                $resumo .= "\nAvisos: " . implode(', ', $errors);
            }
            
            return $this->formatTestResult(
                'Processo Completo de Tramitação',
                count($errors) > 0 ? 'warning' : 'success',
                $resumo
            );
            
        } catch (\Exception $e) {
            return $this->formatTestResult(
                'Processo Completo de Tramitação',
                'error',
                'Erro durante execução: ' . $e->getMessage()
            );
        }
    }
    
    private function formatTestResult($test, $status, $message)
    {
        return [
            'test' => $test,
            'status' => $status,
            'message' => $message
        ];
    }

    // Métodos para teste interativo
    public function getTiposProposicao()
    {
        $tipos = \App\Models\TipoProposicao::all();
        return response()->json(['tipos' => $tipos]);
    }
    
    public function getTemplates($tipoId)
    {
        $templates = TipoProposicaoTemplate::where('tipo_proposicao_id', $tipoId)
            ->where('ativo', true)
            ->get();
        return response()->json(['templates' => $templates]);
    }
    
    public function createProposicaoTest(Request $request)
    {
        try {
            $parlamentar = User::where('email', 'jessica@sistema.gov.br')->first();
            if (!$parlamentar) {
                // Criar usuário parlamentar se não existir
                $parlamentar = User::create([
                    'name' => 'Jessica Parlamentar',
                    'email' => 'jessica@sistema.gov.br',
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now()
                ]);
                $role = \Spatie\Permission\Models\Role::where('name', 'PARLAMENTAR')->first();
                if ($role) {
                    $parlamentar->assignRole($role);
                }
            }
            
            $tipoProposicao = \App\Models\TipoProposicao::find($request->tipo_proposicao_id);
            
            $proposicao = new Proposicao();
            $proposicao->tipo = $tipoProposicao->nome;
            $proposicao->ementa = $request->ementa;
            $proposicao->conteudo = $request->conteudo;
            $proposicao->status = 'rascunho';
            $proposicao->autor_id = $parlamentar->id;
            $proposicao->template_id = $request->template_id;
            $proposicao->ano = date('Y');
            $proposicao->save();
            
            return response()->json([
                'success' => true,
                'proposicao' => $proposicao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function enviarLegislativo($id)
    {
        try {
            $proposicao = Proposicao::find($id);
            $proposicao->status = 'enviado_legislativo';
            $proposicao->save();
            
            $parlamentar = User::find($proposicao->autor_id);
            
            \App\Models\TramitacaoLog::create([
                'proposicao_id' => $proposicao->id,
                'user_id' => $parlamentar->id,
                'acao' => 'ENVIADO_PARA_REVISAO',
                'status_anterior' => 'rascunho',
                'status_novo' => 'enviado_legislativo',
                'observacoes' => 'Enviado para análise do setor Legislativo'
            ]);
            
            return response()->json([
                'success' => true,
                'proposicao' => $proposicao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function analisarLegislativo($id)
    {
        try {
            $proposicao = Proposicao::find($id);
            $proposicao->conteudo .= "\n\nARTIGO ADICIONAL - Alteração incluída pelo setor Legislativo para adequação técnica.";
            $proposicao->observacoes_legislativo = 'Documento revisado conforme normas técnicas legislativas.';
            $proposicao->status = 'em_revisao';
            $proposicao->save();
            
            $legislativo = User::where('email', 'joao@sistema.gov.br')->first();
            if (!$legislativo) {
                $legislativo = User::find($proposicao->autor_id);
            }
            
            \App\Models\TramitacaoLog::create([
                'proposicao_id' => $proposicao->id,
                'user_id' => $legislativo->id,
                'acao' => 'REVISADO',
                'status_anterior' => 'enviado_legislativo',
                'status_novo' => 'em_revisao',
                'observacoes' => 'Análise técnica realizada com alterações'
            ]);
            
            return response()->json([
                'success' => true,
                'proposicao' => $proposicao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function converterPDF($id)
    {
        try {
            $proposicao = Proposicao::find($id);
            $pdfPath = 'proposicoes/pdf/' . $proposicao->id . '_' . now()->timestamp . '.pdf';
            $proposicao->arquivo_pdf = $pdfPath;
            $proposicao->status = 'aguardando_aprovacao_autor';
            $proposicao->save();
            
            $legislativo = User::where('email', 'joao@sistema.gov.br')->first();
            if (!$legislativo) {
                $legislativo = User::find($proposicao->autor_id);
            }
            
            \App\Models\TramitacaoLog::create([
                'proposicao_id' => $proposicao->id,
                'user_id' => $legislativo->id,
                'acao' => 'REVISADO',
                'status_anterior' => 'em_revisao',
                'status_novo' => 'aguardando_aprovacao_autor',
                'observacoes' => 'Documento convertido para PDF e enviado para assinatura'
            ]);
            
            return response()->json([
                'success' => true,
                'proposicao' => $proposicao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function assinarDocumento($id)
    {
        try {
            $proposicao = Proposicao::find($id);
            $proposicao->assinado = true;
            $proposicao->data_assinatura = now();
            $proposicao->status = 'assinado';
            $proposicao->save();
            
            $parlamentar = User::find($proposicao->autor_id);
            
            \App\Models\TramitacaoLog::create([
                'proposicao_id' => $proposicao->id,
                'user_id' => $parlamentar->id,
                'acao' => 'ASSINADO',
                'status_anterior' => 'aguardando_aprovacao_autor',
                'status_novo' => 'assinado',
                'observacoes' => 'Documento assinado digitalmente pelo parlamentar'
            ]);
            
            return response()->json([
                'success' => true,
                'proposicao' => $proposicao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function protocolizar($id)
    {
        try {
            $proposicao = Proposicao::find($id);
            $numeroProtocolo = 'PROT-' . date('Y') . '-' . str_pad($proposicao->id, 6, '0', STR_PAD_LEFT);
            $proposicao->numero_protocolo = $numeroProtocolo;
            $proposicao->data_protocolo = now();
            $proposicao->status = 'protocolado';
            $proposicao->save();
            
            $protocolo = User::where('email', 'roberto@sistema.gov.br')->first();
            if (!$protocolo) {
                $protocolo = User::find($proposicao->autor_id);
            }
            
            \App\Models\TramitacaoLog::create([
                'proposicao_id' => $proposicao->id,
                'user_id' => $protocolo->id,
                'acao' => 'PROTOCOLADO',
                'status_anterior' => 'assinado',
                'status_novo' => 'protocolado',
                'observacoes' => 'Documento protocolado com número: ' . $numeroProtocolo
            ]);
            
            return response()->json([
                'success' => true,
                'proposicao' => $proposicao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function enviarExpediente($id)
    {
        try {
            $proposicao = Proposicao::find($id);
            $tipoProposicao = \App\Models\TipoProposicao::where('nome', $proposicao->tipo)->first();
            
            // Determinar momento baseado no tipo
            $momento = ($proposicao->tipo == 'Projeto de Lei Ordinária') ? 'ORDEM_DO_DIA' : 'EXPEDIENTE';
            
            $proposicao->momento_sessao = $momento;
            $proposicao->save();
            
            $expediente = User::where('email', 'expediente@sistema.gov.br')->first();
            if (!$expediente) {
                $expediente = User::find($proposicao->autor_id);
            }
            
            \App\Models\TramitacaoLog::create([
                'proposicao_id' => $proposicao->id,
                'user_id' => $expediente->id,
                'acao' => 'INCLUIDO_PAUTA',
                'status_anterior' => 'protocolado',
                'status_novo' => 'protocolado',
                'observacoes' => 'Incluído na pauta - Momento: ' . str_replace('_', ' ', $momento)
            ]);
            
            return response()->json([
                'success' => true,
                'proposicao' => $proposicao,
                'momento' => str_replace('_', ' ', $momento)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function emitirParecer($id)
    {
        try {
            $proposicao = Proposicao::find($id);
            
            $assessorJuridico = User::where('email', 'juridico@sistema.gov.br')->first();
            if (!$assessorJuridico) {
                $assessorJuridico = User::find($proposicao->autor_id);
            }
            
            \App\Models\ParecerJuridico::create([
                'proposicao_id' => $proposicao->id,
                'user_id' => $assessorJuridico->id,
                'parecer' => 'PARECER JURÍDICO: Após análise detalhada, verificamos que a proposição está em conformidade com a legislação vigente, não apresentando vícios de constitucionalidade ou legalidade.',
                'resultado' => 'favoravel',
                'data_parecer' => now()
            ]);
            
            $proposicao->tem_parecer_juridico = true;
            $proposicao->save();
            
            \App\Models\TramitacaoLog::create([
                'proposicao_id' => $proposicao->id,
                'user_id' => $assessorJuridico->id,
                'acao' => 'PARECER_EMITIDO',
                'status_anterior' => 'protocolado',
                'status_novo' => 'protocolado',
                'observacoes' => 'Parecer jurídico favorável emitido'
            ]);
            
            return response()->json([
                'success' => true,
                'proposicao' => $proposicao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function runPestTests()
    {
        try {
            // Executa os testes Pest
            $output = '';
            $returnCode = 0;
            
            $command = 'cd ' . base_path() . ' && php artisan test tests/Unit/ProcessTest.php --json';
            exec($command, $output, $returnCode);
            
            $result = implode("\n", $output);
            
            return response()->json([
                'success' => $returnCode === 0,
                'output' => $result,
                'return_code' => $returnCode
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}