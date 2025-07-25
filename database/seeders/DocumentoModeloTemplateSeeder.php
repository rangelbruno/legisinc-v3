<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Documento\DocumentoModelo;
use App\Models\TipoProposicao;
use App\Services\Documento\DocumentoModeloService;
use Illuminate\Support\Str;

class DocumentoModeloTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentoService = app(DocumentoModeloService::class);
        
        // Mapear tipos de proposição
        $tiposMap = [];
        $tipos = TipoProposicao::all();
        foreach ($tipos as $tipo) {
            $tiposMap[Str::slug($tipo->nome)] = $tipo->id;
        }
        
        // Templates padrão do sistema
        $templates = [
            [
                'nome' => 'Projeto de Lei Ordinária - Modelo Padrão',
                'descricao' => 'Template padrão para criação de projetos de lei ordinária',
                'tipo_proposicao_id' => $tiposMap['projeto-de-lei'] ?? null,
                'is_template' => true,
                'template_id' => 'projeto_lei_ordinaria',
                'categoria' => 'legislativo',
                'ordem' => 1,
                'icon' => 'ki-duotone ki-document-text',
                'variaveis' => [
                    'numero_projeto',
                    'ano',
                    'ementa',
                    'autor',
                    'cargo_autor',
                    'artigos',
                    'justificativa'
                ],
                'metadata' => [
                    'formato' => 'A4',
                    'orientacao' => 'portrait',
                    'margem_superior' => '3cm',
                    'margem_inferior' => '2cm',
                    'margem_esquerda' => '3cm',
                    'margem_direita' => '2cm'
                ]
            ],
            [
                'nome' => 'Projeto de Lei Complementar - Modelo Padrão',
                'descricao' => 'Template padrão para criação de projetos de lei complementar',
                'tipo_proposicao_id' => $tiposMap['projeto-de-lei-complementar'] ?? null,
                'is_template' => true,
                'template_id' => 'projeto_lei_complementar',
                'categoria' => 'legislativo',
                'ordem' => 2,
                'icon' => 'ki-duotone ki-document-text',
                'variaveis' => [
                    'numero_projeto',
                    'ano',
                    'ementa',
                    'autor',
                    'cargo_autor',
                    'artigos',
                    'justificativa'
                ]
            ],
            [
                'nome' => 'Resolução da Mesa - Modelo Padrão',
                'descricao' => 'Template padrão para resoluções da mesa diretora',
                'tipo_proposicao_id' => $tiposMap['resolucao'] ?? null,
                'is_template' => true,
                'template_id' => 'resolucao_mesa',
                'categoria' => 'administrativo',
                'ordem' => 3,
                'icon' => 'ki-duotone ki-shield-tick',
                'variaveis' => [
                    'numero_resolucao',
                    'ano',
                    'ementa',
                    'considerandos',
                    'artigos_resolucao',
                    'presidente_mesa',
                    'data_aprovacao'
                ]
            ],
            [
                'nome' => 'Requerimento - Modelo Padrão',
                'descricao' => 'Template padrão para requerimentos parlamentares',
                'tipo_proposicao_id' => $tiposMap['requerimento'] ?? null,
                'is_template' => true,
                'template_id' => 'requerimento',
                'categoria' => 'legislativo',
                'ordem' => 4,
                'icon' => 'ki-duotone ki-file-added',
                'variaveis' => [
                    'numero_requerimento',
                    'ano',
                    'autor',
                    'cargo_autor',
                    'destinatario',
                    'cargo_destinatario',
                    'assunto',
                    'justificativa',
                    'pedido'
                ]
            ],
            [
                'nome' => 'Indicação - Modelo Padrão',
                'descricao' => 'Template padrão para indicações ao poder executivo',
                'tipo_proposicao_id' => $tiposMap['indicacao'] ?? null,
                'is_template' => true,
                'template_id' => 'indicacao',
                'categoria' => 'legislativo',
                'ordem' => 5,
                'icon' => 'ki-duotone ki-send',
                'variaveis' => [
                    'numero_indicacao',
                    'ano',
                    'autor',
                    'cargo_autor',
                    'destinatario',
                    'cargo_destinatario',
                    'sugestao',
                    'justificativa',
                    'beneficios'
                ]
            ],
            [
                'nome' => 'Moção - Modelo Padrão',
                'descricao' => 'Template padrão para moções (aplausos, repúdio, pesar)',
                'tipo_proposicao_id' => $tiposMap['mocao'] ?? null,
                'is_template' => true,
                'template_id' => 'mocao',
                'categoria' => 'legislativo',
                'ordem' => 6,
                'icon' => 'ki-duotone ki-award',
                'variaveis' => [
                    'numero_mocao',
                    'ano',
                    'tipo_mocao',
                    'destinatario',
                    'motivo',
                    'autor',
                    'cargo_autor',
                    'considerandos'
                ]
            ],
            [
                'nome' => 'Emenda - Modelo Padrão',
                'descricao' => 'Template padrão para emendas a projetos',
                'tipo_proposicao_id' => $tiposMap['emenda'] ?? null,
                'is_template' => true,
                'template_id' => 'emenda',
                'categoria' => 'legislativo',
                'ordem' => 7,
                'icon' => 'ki-duotone ki-pencil',
                'variaveis' => [
                    'numero_emenda',
                    'tipo_emenda',
                    'projeto_referencia',
                    'autor',
                    'cargo_autor',
                    'texto_original',
                    'texto_proposto',
                    'justificativa'
                ]
            ],
            [
                'nome' => 'Decreto Legislativo - Modelo Padrão',
                'descricao' => 'Template padrão para decretos legislativos',
                'tipo_proposicao_id' => $tiposMap['decreto-legislativo'] ?? null,
                'is_template' => true,
                'template_id' => 'decreto_legislativo',
                'categoria' => 'legislativo',
                'ordem' => 8,
                'icon' => 'ki-duotone ki-shield-search',
                'variaveis' => [
                    'numero_decreto',
                    'ano',
                    'ementa',
                    'considerandos',
                    'artigos',
                    'presidente_camara',
                    'data_aprovacao'
                ]
            ]
        ];
        
        foreach ($templates as $templateData) {
            // Verificar se já existe
            $exists = DocumentoModelo::where('template_id', $templateData['template_id'])->exists();
            
            if (!$exists) {
                // Criar o modelo com arquivo em branco inicial
                $modelo = $documentoService->criarModelo([
                    'nome' => $templateData['nome'],
                    'descricao' => $templateData['descricao'],
                    'tipo_proposicao_id' => $templateData['tipo_proposicao_id'],
                    'is_template' => $templateData['is_template'],
                    'template_id' => $templateData['template_id'],
                    'categoria' => $templateData['categoria'],
                    'ordem' => $templateData['ordem'],
                    'icon' => $templateData['icon'],
                    'variaveis' => $templateData['variaveis'],
                    'metadata' => $templateData['metadata'] ?? null
                ]);
                
                $this->command->info("Template criado: {$templateData['nome']}");
            } else {
                $this->command->warn("Template já existe: {$templateData['nome']}");
            }
        }
        
        $this->command->info('Templates de documentos criados com sucesso!');
    }
}
