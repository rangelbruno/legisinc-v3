<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Parametro\ParametroService;

class CreateTemplateModule extends Command
{
    protected $signature = 'template:create-module';
    protected $description = 'Create template header module and fields';

    public function handle(ParametroService $parametroService)
    {
        try {
            // Verificar se módulo existe
            $modulo = \App\Models\Parametro\ParametroModulo::where('nome', 'Templates')->first();
            
            if (!$modulo) {
                $this->error('Módulo Templates não encontrado!');
                return 1;
            }

            // Criar submódulo "Cabeçalho"
            $submoduloData = [
                'modulo_id' => $modulo->id,
                'nome' => 'Cabeçalho',
                'descricao' => 'Configurações do cabeçalho padrão das proposições',
                'tipo' => 'form',
                'ordem' => 1,
                'ativo' => true
            ];

            $submodulo = $parametroService->criarSubmodulo($submoduloData);
            $this->info("Submódulo criado: {$submodulo->id}");

            // Criar campos do submódulo
            $campos = [
                [
                    'nome' => 'cabecalho_imagem',
                    'label' => 'Imagem do Cabeçalho',
                    'descricao' => 'Imagem utilizada no cabeçalho das proposições',
                    'tipo_campo' => 'file',
                    'valor_padrao' => 'template/cabecalho.png',
                    'obrigatorio' => true,
                    'ordem' => 1,
                    'placeholder' => 'Selecione uma imagem PNG ou JPG',
                    'validacao' => json_encode([
                        'accepted_types' => ['image/png', 'image/jpeg', 'image/jpg'],
                        'max_size' => 2048
                    ]),
                    'opcoes' => json_encode([
                        'storage_path' => 'public/template',
                        'default_file' => 'template/cabecalho.png'
                    ]),
                    'submodulo_id' => $submodulo->id
                ],
                [
                    'nome' => 'usar_cabecalho_padrao',
                    'label' => 'Usar Cabeçalho Padrão',
                    'descricao' => 'Aplicar automaticamente o cabeçalho padrão em todas as proposições',
                    'tipo_campo' => 'checkbox',
                    'valor_padrao' => '1',
                    'obrigatorio' => false,
                    'ordem' => 2,
                    'placeholder' => 'Ativar cabeçalho automático',
                    'submodulo_id' => $submodulo->id
                ],
                [
                    'nome' => 'cabecalho_altura',
                    'label' => 'Altura do Cabeçalho',
                    'descricao' => 'Altura do cabeçalho em pixels',
                    'tipo_campo' => 'number',
                    'valor_padrao' => '150',
                    'obrigatorio' => true,
                    'ordem' => 3,
                    'placeholder' => 'Altura em pixels (ex: 150)',
                    'validacao' => json_encode([
                        'min' => 50,
                        'max' => 300
                    ]),
                    'submodulo_id' => $submodulo->id
                ],
                [
                    'nome' => 'cabecalho_posicao',
                    'label' => 'Posição do Cabeçalho',
                    'descricao' => 'Posição do cabeçalho no documento',
                    'tipo_campo' => 'select',
                    'valor_padrao' => 'topo',
                    'obrigatorio' => true,
                    'ordem' => 4,
                    'placeholder' => 'Selecione a posição',
                    'opcoes' => json_encode([
                        'topo' => 'Topo do documento',
                        'header' => 'Cabeçalho da página',
                        'marca_dagua' => 'Marca d\'água'
                    ]),
                    'submodulo_id' => $submodulo->id
                ]
            ];

            foreach ($campos as $campoData) {
                $campo = $parametroService->criarCampo($campoData);
                $this->info("Campo criado: {$campo->nome}");
            }

            $this->info('Módulo de Templates configurado com sucesso!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Erro: ' . $e->getMessage());
            return 1;
        }
    }
}