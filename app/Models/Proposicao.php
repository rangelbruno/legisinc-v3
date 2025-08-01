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
        'data_protocolo',
        'funcionario_protocolo_id',
        'comissoes_destino',
        'observacoes_protocolo',
        'verificacoes_realizadas'
        // Campos temporariamente comentados até migração ser executada:
        // 'numero',
        // 'variaveis_template',
        // 'conteudo_processado'
    ];

    protected $casts = [
        'ultima_modificacao' => 'datetime',
        'data_retorno_legislativo' => 'datetime',
        'data_assinatura' => 'datetime',
        'data_aprovacao_autor' => 'datetime',
        'data_protocolo' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'confirmacao_leitura' => 'boolean',
        'comissoes_destino' => 'array',
        'verificacoes_realizadas' => 'array'
        // 'variaveis_template' => 'array'
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
            'mocao' => 'Moção'
        ];

        return $tipos[$this->tipo] ?? $this->tipo;
    }

    /**
     * Verificar se proposição usa template
     */
    public function getUsaTemplateAttribute(): bool
    {
        return !is_null($this->template_id);
    }

    /**
     * Obter variáveis do template (temporariamente usando sessão)
     */
    public function getVariaveisTemplateAttribute($value = null): array
    {
        // Usar sessão temporariamente até migração ser executada
        $sessionKey = 'proposicao_' . $this->id . '_variaveis_template';
        return session($sessionKey, []);
    }

    /**
     * Processar template com dados atuais
     */
    public function processarTemplate(): string
    {
        if (!$this->usa_template || !$this->template) {
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
        $sessionKey = 'proposicao_' . $this->id . '_variaveis_template';
        session([$sessionKey => $variaveis]);
        
        // Atualizar apenas campos existentes
        $this->update([
            'ultima_modificacao' => now()
        ]);
    }

    /**
     * Gerar número sequencial da proposição (temporariamente usando sessão)
     */
    public function gerarNumero(): string
    {
        // Usar sessão temporariamente até migração ser executada
        $sessionKey = 'proposicao_' . $this->id . '_numero';
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
        return in_array($this->status, ['rascunho', 'em_edicao']);
    }

    /**
     * Validar se pode ser excluída
     */
    public function podeSerExcluida(): bool
    {
        return in_array($this->status, ['rascunho', 'em_edicao']);
    }

    /**
     * Obter cor do status para exibição
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
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
     * Boot do model
     */
    protected static function boot()
    {
        parent::boot();

        // Ao criar proposição, definir ano atual
        static::creating(function ($proposicao) {
            if (!$proposicao->ano) {
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
}
