<?php

namespace App\Models;

use App\Services\Template\TemplateProcessorService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proposicao extends Model
{
    protected $table = 'proposicoes';

    protected $fillable = [
        'tipo',
        'ementa',
        'conteudo',
        'arquivo_path',
        'arquivo_pdf_path',
        'anexos',
        'total_anexos',
        'autor_id',
        'status',
        'ano',
        'modelo_id',
        'template_id',
        'ultima_modificacao',
        'observacoes_edicao',
        'observacoes_retorno',
        'data_retorno_legislativo',
        'confirmacao_leitura',
        'assinatura_digital',
        'certificado_digital',
        'data_assinatura',
        'ip_assinatura',
        'data_aprovacao_autor',
        // Campos de protocolo
        'numero_protocolo',
        'numero_sequencial',
        'data_protocolo',
        'funcionario_protocolo_id',
        'comissoes_destino',
        'observacoes_protocolo',
        'verificacoes_realizadas',
        // Campos do novo fluxo
        'enviado_revisao_em',
        'revisor_id',
        'revisado_em',
        'pdf_path',
        'pdf_assinado_path',
        'momento_sessao',
        'tem_parecer',
        'parecer_id',
        // Campos temporariamente comentados até migração ser executada:
        'numero',
        'variaveis_template',
        // 'conteudo_processado'
    ];

    protected $casts = [
        'ultima_modificacao' => 'datetime',
        'data_retorno_legislativo' => 'datetime',
        'data_assinatura' => 'datetime',
        'data_aprovacao_autor' => 'datetime',
        'data_protocolo' => 'datetime',
        'enviado_revisao_em' => 'datetime',
        'revisado_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'confirmacao_leitura' => 'boolean',
        'tem_parecer' => 'boolean',
        'comissoes_destino' => 'array',
        'verificacoes_realizadas' => 'array',
        'variaveis_template' => 'array',
        'anexos' => 'array',
        'total_anexos' => 'integer',
    ];

    /**
     * Relacionamentos sempre carregados por padrão
     */
    protected $with = ['autor', 'tipoProposicao'];

    /**
     * Relacionamentos disponíveis para eager loading
     */
    public static array $availableIncludes = [
        'revisor', 'template', 'funcionarioProtocolo', 'parecerJuridico',
        'logstramitacao', 'itensPauta',
    ];

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(TipoProposicaoTemplate::class, 'template_id');
    }

    public function tipoProposicao(): BelongsTo
    {
        return $this->belongsTo(TipoProposicao::class, 'tipo', 'codigo');
    }

    public function funcionarioProtocolo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'funcionario_protocolo_id');
    }

    /**
     * Relacionamento com o revisor (usuário que revisou)
     */
    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisor_id');
    }

    /**
     * Relacionamento com o parecer jurídico
     */
    public function parecer(): BelongsTo
    {
        return $this->belongsTo(ParecerJuridico::class, 'parecer_id');
    }

    /**
     * Relacionamento com o parecer jurídico (alias)
     */
    public function parecerJuridico(): BelongsTo
    {
        return $this->belongsTo(ParecerJuridico::class, 'parecer_id');
    }

    /**
     * Relacionamento com os logs de tramitação
     */
    public function logstramitacao()
    {
        return $this->hasMany(TramitacaoLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Relacionamento com itens de pauta
     */
    public function itensPauta()
    {
        return $this->hasMany(ItemPauta::class);
    }

    /**
     * Scope para filtrar por autor
     */
    public function scopeDoAutor($query, $autorId)
    {
        return $query->where('autor_id', $autorId);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopeComStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para proposições que usam template
     */
    public function scopeComTemplate($query)
    {
        return $query->whereNotNull('template_id');
    }

    /**
     * Scope para proposições enviadas para revisão
     */
    public function scopeEmRevisao($query)
    {
        return $query->where('status', 'EM_REVISAO');
    }

    /**
     * Scope para proposições revisadas
     */
    public function scopeRevisadas($query)
    {
        return $query->where('status', 'REVISADO')->orWhere('status', 'AGUARDANDO_ASSINATURA');
    }

    /**
     * Scope para proposições assinadas
     */
    public function scopeAssinadas($query)
    {
        return $query->where('status', 'ASSINADO');
    }

    /**
     * Scope para proposições protocoladas
     */
    public function scopeProtocoladas($query)
    {
        return $query->where('status', 'PROTOCOLADO');
    }

    /**
     * Scope para proposições com parecer
     */
    public function scopeComParecer($query)
    {
        return $query->where('tem_parecer', true);
    }

    /**
     * Scope para proposições sem parecer
     */
    public function scopeSemParecer($query)
    {
        return $query->where('tem_parecer', false);
    }

    /**
     * Scope para proposições do expediente
     */
    public function scopeExpediente($query)
    {
        return $query->where('momento_sessao', 'EXPEDIENTE');
    }

    /**
     * Scope para proposições da ordem do dia
     */
    public function scopeOrdemDia($query)
    {
        return $query->where('momento_sessao', 'ORDEM_DO_DIA');
    }

    /**
     * Get formatted tipo
     */
    public function getTipoFormatadoAttribute()
    {
        $tipos = [
            'projeto_lei_ordinaria' => 'Projeto de Lei Ordinária',
            'projeto_lei_complementar' => 'Projeto de Lei Complementar',
            'indicacao' => 'Indicação',
            'projeto_decreto_legislativo' => 'Projeto de Decreto Legislativo',
            'projeto_resolucao' => 'Projeto de Resolução',
            'mocao' => 'Moção',
        ];

        return $tipos[$this->tipo] ?? $this->tipo;
    }

    /**
     * Verificar se proposição usa template
     */
    public function getUsaTemplateAttribute(): bool
    {
        return ! is_null($this->template_id);
    }

    /**
     * Obter variáveis do template (temporariamente usando sessão)
     */
    public function getVariaveisTemplateAttribute($value = null): array
    {
        // Tentar primeiro do campo do banco (se existir dados)
        if (! empty($this->attributes['variaveis_template'])) {
            try {
                $dadosBanco = json_decode($this->attributes['variaveis_template'], true);
                if (is_array($dadosBanco) && ! empty($dadosBanco)) {
                    return $dadosBanco;
                }
            } catch (\Exception $e) {
                // Log::warning('Erro ao decodificar variaveis_template do banco', [
                //     'proposicao_id' => $this->id,
                //     'error' => $e->getMessage()
                // ]);
            }
        }

        // Fallback: usar sessão como antes
        $sessionKey = 'proposicao_'.$this->id.'_variaveis_template';

        return session($sessionKey, []);
    }

    /**
     * Processar template com dados atuais
     */
    public function processarTemplate(): string
    {
        if (! $this->usa_template || ! $this->template) {
            return $this->conteudo ?? '';
        }

        $templateProcessor = app(TemplateProcessorService::class);

        return $templateProcessor->processarTemplate(
            $this->template,
            $this,
            $this->variaveis_template ?? []
        );
    }

    /**
     * Atualizar variáveis do template (temporariamente usando sessão)
     */
    public function atualizarVariaveisTemplate(array $variaveis): void
    {
        // Usar sessão temporariamente até migração ser executada
        $sessionKey = 'proposicao_'.$this->id.'_variaveis_template';
        session([$sessionKey => $variaveis]);

        // Atualizar apenas campos existentes
        $this->update([
            'ultima_modificacao' => now(),
        ]);
    }

    /**
     * Gerar número sequencial da proposição (temporariamente usando sessão)
     */
    public function gerarNumero(): string
    {
        // Usar sessão temporariamente até migração ser executada
        $sessionKey = 'proposicao_'.$this->id.'_numero';
        $numeroExistente = session($sessionKey);

        if ($numeroExistente) {
            return $numeroExistente;
        }

        // Gerar número baseado no ID e ano
        $proximoSequencial = $this->id;
        $numero = sprintf('%04d/%d', $proximoSequencial, $this->ano ?? date('Y'));

        // Salvar na sessão temporariamente
        session([$sessionKey => $numero]);

        return $numero;
    }

    /**
     * Validar se pode ser editada
     */
    public function podeSerEditada(): bool
    {
        return in_array($this->status, ['RASCUNHO', 'EM_REVISAO']);
    }

    /**
     * Validar se pode ser excluída
     */
    public function podeSerExcluida(): bool
    {
        return in_array($this->status, ['RASCUNHO']);
    }

    /**
     * Validar se pode ser enviada para revisão
     */
    public function podeSerEnviadaParaRevisao(): bool
    {
        return $this->status === 'RASCUNHO';
    }

    /**
     * Validar se pode ser assinada
     */
    public function podeSerAssinada(): bool
    {
        return $this->status === 'AGUARDANDO_ASSINATURA';
    }

    /**
     * Verificar se foi assinada
     */
    public function foiAssinada(): bool
    {
        return ! empty($this->pdf_assinado_path);
    }

    /**
     * Verificar se foi protocolada
     */
    public function foiProtocolada(): bool
    {
        return ! empty($this->numero_protocolo);
    }

    /**
     * Verificar se tem parecer jurídico
     */
    public function temParecer(): bool
    {
        return $this->tem_parecer && ! empty($this->parecer_id);
    }

    /**
     * Verificar se está em pauta
     */
    public function estaEmPauta(): bool
    {
        return $this->itensPauta()->exists();
    }

    /**
     * Obter cor do status para exibição
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'RASCUNHO' => 'warning',
            'EM_REVISAO' => 'info',
            'REVISADO' => 'primary',
            'AGUARDANDO_ASSINATURA' => 'warning',
            'ASSINADO' => 'success',
            'PROTOCOLADO' => 'dark',
            'COM_PARECER' => 'secondary',
            'EM_PAUTA' => 'primary',
            'EM_VOTACAO' => 'info',
            'APROVADO' => 'success',
            'REJEITADO' => 'danger',
            // Status antigos para compatibilidade
            'rascunho', 'em_edicao' => 'warning',
            'enviado_legislativo' => 'info',
            'aprovado_legislativo', 'aprovado_assinatura' => 'success',
            'devolvido_correcao', 'retornado_legislativo' => 'danger',
            'protocolado' => 'primary',
            'assinado' => 'success',
            'em_tramitacao' => 'info',
            'arquivado' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Obter texto formatado do status
     */
    public function getStatusFormatado(): string
    {
        return match ($this->status) {
            'RASCUNHO' => 'Rascunho',
            'EM_REVISAO' => 'Em Revisão',
            'REVISADO' => 'Revisado',
            'AGUARDANDO_ASSINATURA' => 'Aguardando Assinatura',
            'ASSINADO' => 'Assinado',
            'PROTOCOLADO' => 'Protocolado',
            'COM_PARECER' => 'Com Parecer',
            'EM_PAUTA' => 'Em Pauta',
            'EM_VOTACAO' => 'Em Votação',
            'APROVADO' => 'Aprovado',
            'REJEITADO' => 'Rejeitado',
            default => 'Status não definido'
        };
    }

    /**
     * Obter cor do momento da sessão
     */
    public function getCorMomentoSessao(): string
    {
        return match ($this->momento_sessao) {
            'EXPEDIENTE' => 'info',
            'ORDEM_DO_DIA' => 'primary',
            'NAO_CLASSIFICADO' => 'secondary',
            default => 'light'
        };
    }

    /**
     * Obter texto formatado do momento da sessão
     */
    public function getMomentoSessaoFormatado(): string
    {
        return match ($this->momento_sessao) {
            'EXPEDIENTE' => 'Expediente',
            'ORDEM_DO_DIA' => 'Ordem do Dia',
            'NAO_CLASSIFICADO' => 'Não Classificado',
            default => 'Não definido'
        };
    }

    /**
     * Boot do model
     */
    protected static function boot()
    {
        parent::boot();

        // Ao criar proposição, definir ano atual
        static::creating(function ($proposicao) {
            if (! $proposicao->ano) {
                $proposicao->ano = date('Y');
            }
        });

        // Temporariamente desabilitado até migração ser executada
        // static::saving(function ($proposicao) {
        //     if ($proposicao->usa_template && $proposicao->isDirty(['template_id'])) {
        //         // Processar template e salvar na sessão temporariamente
        //         $conteudoProcessado = $proposicao->processarTemplate();
        //         $sessionKey = 'proposicao_' . $proposicao->id . '_conteudo_processado';
        //         session([$sessionKey => $conteudoProcessado]);
        //     }
        // });
    }

    // ========== SCOPED RELATIONSHIPS ==========

    /**
     * Relacionamento scoped: logs de tramitação recentes (últimos 5)
     */
    public function tramitacaoRecente()
    {
        return $this->logstramitacao()->latest()->limit(5);
    }

    /**
     * Relacionamento scoped: proposições do mesmo autor
     */
    public function proposicoesDoMesmoAutor()
    {
        return $this->hasMany(Proposicao::class, 'autor_id', 'autor_id')
            ->where('id', '!=', $this->id);
    }

    /**
     * Relacionamento scoped: proposições similares (mesmo tipo e ano)
     */
    public function proposicoesSimilares()
    {
        return $this->proposicoesDoMesmoAutor()
            ->where('tipo', $this->tipo)
            ->where('ano', $this->ano);
    }

    /**
     * Relacionamento scoped: proposições ativas (não arquivadas/canceladas)
     */
    public function scopeAtivas($query)
    {
        return $query->whereNotIn('status', ['ARQUIVADO', 'CANCELADO']);
    }

    /**
     * Relacionamento scoped: proposições pendentes de análise
     */
    public function scopePendentesAnalise($query)
    {
        return $query->whereIn('status', [
            'RASCUNHO', 'EM_REVISAO', 'AGUARDANDO_ASSINATURA',
        ]);
    }

    /**
     * Relacionamento scoped: proposições finalizadas
     */
    public function scopeFinalizadas($query)
    {
        return $query->whereIn('status', [
            'PROTOCOLADO', 'APROVADO', 'REJEITADO',
        ]);
    }

    /**
     * Relacionamento scoped: proposições do ano atual
     */
    public function scopeAnoAtual($query)
    {
        return $query->where('ano', date('Y'));
    }

    /**
     * Relacionamento scoped: proposições com template
     */
    public function scopeComTemplateAtivo($query)
    {
        return $query->whereHas('template');
    }

    /**
     * Relacionamento scoped: proposições modificadas recentemente (última semana)
     */
    public function scopeModificadasRecentemente($query)
    {
        return $query->where('ultima_modificacao', '>=', now()->subWeek());
    }

    /**
     * Relacionamento scoped: proposições por tipo específico
     */
    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Relacionamento scoped: proposições com anexos
     */
    public function scopeComAnexos($query)
    {
        return $query->where('total_anexos', '>', 0);
    }
}
