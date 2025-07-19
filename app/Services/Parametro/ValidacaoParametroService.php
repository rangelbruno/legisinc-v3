<?php

namespace App\Services\Parametro;

use App\Models\Parametro\ParametroModulo;
use App\Models\Parametro\ParametroSubmodulo;
use App\Models\Parametro\ParametroCampo;
use App\Services\Parametro\SegurancaParametroService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ValidacaoParametroService
{
    protected int $cacheTtl = 3600; // 1 hora
    protected SegurancaParametroService $segurancaService;

    public function __construct(SegurancaParametroService $segurancaService)
    {
        $this->segurancaService = $segurancaService;
    }

    /**
     * Valida um valor específico de parâmetro
     */
    public function validar(string $nomeModulo, string $nomeSubmodulo, mixed $valor): bool
    {
        $configuracoes = $this->obterConfiguracoes($nomeModulo, $nomeSubmodulo);
        
        if (empty($configuracoes)) {
            return false;
        }

        foreach ($configuracoes as $nomeCampo => $config) {
            if (!$this->validarCampo($nomeCampo, $valor, $config)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida múltiplos valores de parâmetros
     */
    public function validarMultiplos(string $nomeModulo, string $nomeSubmodulo, array $valores): array
    {
        $configuracoes = $this->obterConfiguracoes($nomeModulo, $nomeSubmodulo);
        $erros = [];

        // Verificar integridade de segurança
        $verificacaoSeguranca = $this->segurancaService->verificarIntegridade($valores);
        if (!$verificacaoSeguranca['integro']) {
            Log::warning('Problemas de segurança detectados na validação de parâmetros', [
                'modulo' => $nomeModulo,
                'submodulo' => $nomeSubmodulo,
                'problemas' => $verificacaoSeguranca['problemas'],
                'user_id' => auth()->id(),
                'ip' => request()->ip()
            ]);
            
            $erros['_security'] = $verificacaoSeguranca['problemas'];
        }

        foreach ($valores as $nomeCampo => $valor) {
            if (!isset($configuracoes[$nomeCampo])) {
                $erros[$nomeCampo] = ["Campo '{$nomeCampo}' não encontrado"];
                continue;
            }

            $config = $configuracoes[$nomeCampo];
            
            // Sanitizar valor antes da validação
            try {
                $valorSanitizado = $this->segurancaService->sanitizarValor($valor, $config['tipo']);
                $validacao = $this->validarCampo($nomeCampo, $valorSanitizado, $config);
            } catch (\Exception $e) {
                $validacao = [
                    'valido' => false,
                    'erros' => ['Erro de sanitização: ' . $e->getMessage()]
                ];
            }

            if (!$validacao['valido']) {
                $erros[$nomeCampo] = $validacao['erros'];
            }
        }

        return $erros;
    }

    /**
     * Valida um campo específico
     */
    protected function validarCampo(string $nomeCampo, mixed $valor, array $config): array|bool
    {
        $rules = $config['validacao'] ?? [];
        
        if (empty($rules)) {
            return true;
        }

        $validator = Validator::make(
            [$nomeCampo => $valor],
            [$nomeCampo => $rules]
        );

        if ($validator->fails()) {
            return [
                'valido' => false,
                'erros' => $validator->errors()->get($nomeCampo)
            ];
        }

        return ['valido' => true, 'erros' => []];
    }

    /**
     * Obtém configurações de validação para um módulo/submódulo
     */
    public function obterConfiguracoes(string $nomeModulo, string $nomeSubmodulo): array
    {
        $cacheKey = "validacao_config_{$nomeModulo}_{$nomeSubmodulo}";
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($nomeModulo, $nomeSubmodulo) {
            $modulo = ParametroModulo::where('nome', $nomeModulo)->ativos()->first();
            
            if (!$modulo) {
                return [];
            }

            $submodulo = $modulo->submodulos()
                ->where('nome', $nomeSubmodulo)
                ->ativos()
                ->first();
                
            if (!$submodulo) {
                return [];
            }

            $campos = ParametroCampo::porSubmodulo($submodulo->id)
                ->ativos()
                ->get();

            $configuracoes = [];

            foreach ($campos as $campo) {
                $configuracoes[$campo->nome] = [
                    'label' => $campo->label,
                    'tipo' => $campo->tipo_campo,
                    'obrigatorio' => $campo->obrigatorio,
                    'validacao' => $campo->getValidationRules(),
                    'opcoes' => $campo->opcoes_formatada,
                ];
            }

            return $configuracoes;
        });
    }

    /**
     * Valida dados de criação de módulo
     */
    public function validarCriacaoModulo(array $dados): array
    {
        // Verificar permissões
        if (!$this->segurancaService->validarPermissoes(auth()->id(), 'create', 'modulo')) {
            return [
                'valido' => false,
                'erros' => ['Você não tem permissão para criar módulos']
            ];
        }

        // Verificar rate limiting
        if (!$this->segurancaService->validarRateLimit(auth()->id(), 'create')) {
            return [
                'valido' => false,
                'erros' => ['Limite de operações excedido. Tente novamente em alguns minutos.']
            ];
        }

        // Sanitizar dados
        foreach ($dados as $campo => $valor) {
            try {
                $dados[$campo] = $this->segurancaService->sanitizarValor($valor, 'string');
            } catch (\Exception $e) {
                return [
                    'valido' => false,
                    'erros' => ["Erro na sanitização do campo {$campo}: " . $e->getMessage()]
                ];
            }
        }

        $validator = Validator::make($dados, [
            'nome' => 'required|string|max:255|unique:parametros_modulos,nome',
            'descricao' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return [
                'valido' => false,
                'erros' => $validator->errors()->all()
            ];
        }

        // Verificar integridade
        $verificacaoIntegridade = $this->segurancaService->verificarIntegridade($dados);
        if (!$verificacaoIntegridade['integro']) {
            $this->segurancaService->auditarOperacaoSeguranca('criar_modulo_suspeito', [
                'problemas' => $verificacaoIntegridade['problemas']
            ]);
            
            return [
                'valido' => false,
                'erros' => ['Dados suspeitos detectados']
            ];
        }

        return ['valido' => true, 'erros' => []];
    }

    /**
     * Valida dados de criação de submódulo
     */
    public function validarCriacaoSubmodulo(array $dados): array
    {
        $validator = Validator::make($dados, [
            'modulo_id' => 'required|exists:parametros_modulos,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo' => 'required|in:form,checkbox,select,toggle,custom',
            'config' => 'nullable|array',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return [
                'valido' => false,
                'erros' => $validator->errors()->all()
            ];
        }

        // Validar se já existe submódulo com mesmo nome no módulo
        $exists = ParametroSubmodulo::where('modulo_id', $dados['modulo_id'])
            ->where('nome', $dados['nome'])
            ->exists();

        if ($exists) {
            return [
                'valido' => false,
                'erros' => ['Já existe um submódulo com este nome no módulo selecionado']
            ];
        }

        return ['valido' => true, 'erros' => []];
    }

    /**
     * Valida dados de criação de campo
     */
    public function validarCriacaoCampo(array $dados): array
    {
        $validator = Validator::make($dados, [
            'submodulo_id' => 'required|exists:parametros_submodulos,id',
            'nome' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'tipo_campo' => 'required|in:text,email,number,textarea,select,checkbox,radio,file,date,datetime',
            'descricao' => 'nullable|string',
            'obrigatorio' => 'boolean',
            'valor_padrao' => 'nullable|string',
            'opcoes' => 'nullable|array',
            'validacao' => 'nullable|array',
            'placeholder' => 'nullable|string|max:255',
            'classe_css' => 'nullable|string|max:255',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return [
                'valido' => false,
                'erros' => $validator->errors()->all()
            ];
        }

        // Validar se já existe campo com mesmo nome no submódulo
        $exists = ParametroCampo::where('submodulo_id', $dados['submodulo_id'])
            ->where('nome', $dados['nome'])
            ->exists();

        if ($exists) {
            return [
                'valido' => false,
                'erros' => ['Já existe um campo com este nome no submódulo selecionado']
            ];
        }

        // Validar se opcoes são obrigatórias para campos select/radio
        if (in_array($dados['tipo_campo'], ['select', 'radio']) && empty($dados['opcoes'])) {
            return [
                'valido' => false,
                'erros' => ['Campos do tipo select/radio devem ter opções definidas']
            ];
        }

        return ['valido' => true, 'erros' => []];
    }

    /**
     * Valida regras de validação personalizadas
     */
    public function validarRegrasValidacao(array $regras): array
    {
        $regrasValidas = [
            'required', 'nullable', 'string', 'integer', 'numeric', 'boolean',
            'email', 'url', 'date', 'min', 'max', 'between', 'in', 'not_in',
            'unique', 'exists', 'regex', 'confirmed', 'different', 'same',
            'size', 'array', 'json', 'file', 'image', 'mimes', 'dimensions'
        ];

        $erros = [];

        foreach ($regras as $regra) {
            if (is_string($regra)) {
                $nomeRegra = explode(':', $regra)[0];
                if (!in_array($nomeRegra, $regrasValidas)) {
                    $erros[] = "Regra de validação '{$nomeRegra}' não é válida";
                }
            }
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros
        ];
    }

    /**
     * Obtém todas as regras de validação disponíveis
     */
    public function obterRegrasDisponiveis(): array
    {
        return [
            'required' => 'Campo obrigatório',
            'nullable' => 'Campo opcional',
            'string' => 'Deve ser texto',
            'integer' => 'Deve ser número inteiro',
            'numeric' => 'Deve ser número',
            'boolean' => 'Deve ser verdadeiro/falso',
            'email' => 'Deve ser e-mail válido',
            'url' => 'Deve ser URL válida',
            'date' => 'Deve ser data válida',
            'min:valor' => 'Valor mínimo',
            'max:valor' => 'Valor máximo',
            'between:min,max' => 'Valor entre limites',
            'in:opcoes' => 'Deve estar na lista',
            'not_in:opcoes' => 'Não deve estar na lista',
            'unique:tabela,campo' => 'Deve ser único',
            'exists:tabela,campo' => 'Deve existir',
            'regex:pattern' => 'Deve corresponder ao padrão',
            'size:tamanho' => 'Deve ter tamanho específico',
            'array' => 'Deve ser array',
            'json' => 'Deve ser JSON válido',
            'file' => 'Deve ser arquivo',
            'image' => 'Deve ser imagem',
            'mimes:tipos' => 'Tipos de arquivo permitidos',
        ];
    }

    /**
     * Sanitiza e valida um valor específico
     */
    public function sanitizarEValidar(mixed $valor, string $tipoCampo, array $regrasValidacao = []): array
    {
        try {
            // Sanitizar primeiro
            $valorSanitizado = $this->segurancaService->sanitizarValor($valor, $tipoCampo);
            
            // Depois validar
            if (!empty($regrasValidacao)) {
                $validator = Validator::make(
                    ['valor' => $valorSanitizado],
                    ['valor' => $regrasValidacao]
                );
                
                if ($validator->fails()) {
                    return [
                        'valido' => false,
                        'valor' => $valorSanitizado,
                        'erros' => $validator->errors()->get('valor')
                    ];
                }
            }
            
            return [
                'valido' => true,
                'valor' => $valorSanitizado,
                'erros' => []
            ];
            
        } catch (\Exception $e) {
            return [
                'valido' => false,
                'valor' => $valor,
                'erros' => ['Erro na sanitização/validação: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Valida operação com verificação de segurança
     */
    public function validarOperacaoSegura(string $operacao, string $recurso, array $dados = []): array
    {
        $userId = auth()->id();
        
        // Verificar se usuário está autenticado
        if (!$userId) {
            return [
                'valido' => false,
                'erros' => ['Usuário não autenticado']
            ];
        }
        
        // Verificar permissões
        if (!$this->segurancaService->validarPermissoes($userId, $operacao, $recurso)) {
            return [
                'valido' => false,
                'erros' => ['Permissão negada para esta operação']
            ];
        }
        
        // Verificar rate limiting
        if (!$this->segurancaService->validarRateLimit($userId, $operacao)) {
            return [
                'valido' => false,
                'erros' => ['Limite de operações excedido']
            ];
        }
        
        // Verificar integridade dos dados se fornecidos
        if (!empty($dados)) {
            $verificacaoIntegridade = $this->segurancaService->verificarIntegridade($dados);
            if (!$verificacaoIntegridade['integro']) {
                $this->segurancaService->auditarOperacaoSeguranca('operacao_suspeita', [
                    'operacao' => $operacao,
                    'recurso' => $recurso,
                    'problemas' => $verificacaoIntegridade['problemas']
                ]);
                
                return [
                    'valido' => false,
                    'erros' => ['Dados suspeitos detectados']
                ];
            }
        }
        
        return ['valido' => true, 'erros' => []];
    }

    /**
     * Processa valores para armazenamento seguro
     */
    public function processarValoresParaArmazenamento(array $valores, array $configuracoesCampos): array
    {
        $valoresProcessados = [];
        $erros = [];
        
        foreach ($valores as $nomeCampo => $valor) {
            $config = $configuracoesCampos[$nomeCampo] ?? null;
            
            if (!$config) {
                $erros[$nomeCampo] = ['Campo não encontrado'];
                continue;
            }
            
            // Sanitizar e validar
            $resultado = $this->sanitizarEValidar(
                $valor, 
                $config['tipo'] ?? 'string', 
                $config['validacao'] ?? []
            );
            
            if (!$resultado['valido']) {
                $erros[$nomeCampo] = $resultado['erros'];
                continue;
            }
            
            $valorProcessado = $resultado['valor'];
            
            // Criptografar campos sensíveis
            if ($this->segurancaService->isCampoSensivel($nomeCampo)) {
                try {
                    $valorProcessado = $this->segurancaService->criptografarValor($valorProcessado);
                } catch (\Exception $e) {
                    $erros[$nomeCampo] = ['Erro na criptografia'];
                    continue;
                }
            }
            
            $valoresProcessados[$nomeCampo] = $valorProcessado;
        }
        
        return [
            'valores' => $valoresProcessados,
            'erros' => $erros,
            'valido' => empty($erros)
        ];
    }

    /**
     * Recupera valores do armazenamento com descriptografia
     */
    public function recuperarValoresDoArmazenamento(array $valores, array $configuracoesCampos): array
    {
        $valoresRecuperados = [];
        
        foreach ($valores as $nomeCampo => $valor) {
            $config = $configuracoesCampos[$nomeCampo] ?? null;
            
            if (!$config) {
                $valoresRecuperados[$nomeCampo] = $valor;
                continue;
            }
            
            // Descriptografar campos sensíveis
            if ($this->segurancaService->isCampoSensivel($nomeCampo) && !empty($valor)) {
                try {
                    $valor = $this->segurancaService->descriptografarValor($valor);
                } catch (\Exception $e) {
                    Log::error('Erro ao descriptografar campo sensível', [
                        'campo' => $nomeCampo,
                        'error' => $e->getMessage()
                    ]);
                    $valor = '[ERRO NA DESCRIPTOGRAFIA]';
                }
            }
            
            $valoresRecuperados[$nomeCampo] = $valor;
        }
        
        return $valoresRecuperados;
    }

    /**
     * Mascarar valores sensíveis para exibição em logs
     */
    public function mascararValoresSensiveisParaLog(array $dados): array
    {
        $dadosMascarados = [];
        
        foreach ($dados as $campo => $valor) {
            $dadosMascarados[$campo] = $this->segurancaService->mascararValorSensivel($campo, $valor);
        }
        
        return $dadosMascarados;
    }
}