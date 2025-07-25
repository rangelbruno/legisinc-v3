<?php

namespace App\Models\Documento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TipoProposicao;
use App\Models\User;

class DocumentoModelo extends Model
{
    use HasFactory;

    protected $table = 'documento_modelos';

    protected $fillable = [
        'nome',
        'descricao',
        'tipo_proposicao_id',
        'document_key',
        'arquivo_path',
        'arquivo_nome',
        'arquivo_size',
        'variaveis',
        'versao',
        'icon',
        'ativo',
        'created_by',
        'is_template',
        'template_id',
        'categoria',
        'ordem',
        'metadata'
    ];

    protected $casts = [
        'variaveis' => 'array',
        'ativo' => 'boolean',
        'arquivo_size' => 'integer',
        'is_template' => 'boolean',
        'ordem' => 'integer',
        'metadata' => 'array'
    ];

    public function tipoProposicao()
    {
        return $this->belongsTo(TipoProposicao::class);
    }

    public function instancias()
    {
        return $this->hasMany(DocumentoInstancia::class, 'modelo_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function criador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorTipo($query, $tipoProposicaoId)
    {
        return $query->where('tipo_proposicao_id', $tipoProposicaoId);
    }
    
    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }
    
    public function scopeNaoTemplates($query)
    {
        return $query->where('is_template', false);
    }
    
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }
    
    public function scopeOrdenados($query)
    {
        return $query->orderBy('ordem')->orderBy('nome');
    }
    
    // Categorias disponíveis
    const CATEGORIAS = [
        'legislativo' => 'Legislativo',
        'administrativo' => 'Administrativo',
        'juridico' => 'Jurídico',
        'financeiro' => 'Financeiro',
        'geral' => 'Geral'
    ];
    
    // Templates padrão do sistema
    const TEMPLATES_PADRAO = [
        'projeto_lei_ordinaria' => [
            'nome' => 'Projeto de Lei Ordinária',
            'categoria' => 'legislativo',
            'variaveis' => ['numero', 'ano', 'ementa', 'autor', 'artigos']
        ],
        'projeto_lei_complementar' => [
            'nome' => 'Projeto de Lei Complementar',
            'categoria' => 'legislativo',
            'variaveis' => ['numero', 'ano', 'ementa', 'autor', 'artigos']
        ],
        'resolucao_mesa' => [
            'nome' => 'Resolução da Mesa',
            'categoria' => 'administrativo',
            'variaveis' => ['numero', 'ano', 'ementa', 'considerandos', 'resolucao']
        ],
        'requerimento' => [
            'nome' => 'Requerimento',
            'categoria' => 'legislativo',
            'variaveis' => ['numero', 'ano', 'autor', 'destinatario', 'assunto', 'justificativa']
        ],
        'indicacao' => [
            'nome' => 'Indicação',
            'categoria' => 'legislativo',
            'variaveis' => ['numero', 'ano', 'autor', 'destinatario', 'sugestao', 'justificativa']
        ],
        'mocao' => [
            'nome' => 'Moção',
            'categoria' => 'legislativo',
            'variaveis' => ['numero', 'ano', 'tipo_mocao', 'destinatario', 'motivo', 'autor']
        ]
    ];
}
