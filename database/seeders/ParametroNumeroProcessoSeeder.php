<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GrupoParametro;
use App\Models\TipoParametro;
use App\Models\Parametro;

class ParametroNumeroProcessoSeeder extends Seeder
{
    public function run(): void
    {
        // Criar ou buscar grupo de parâmetros do Protocolo
        $grupoProtocolo = GrupoParametro::firstOrCreate(
            ['codigo' => 'protocolo'],
            [
                'nome' => 'Protocolo',
                'descricao' => 'Configurações do módulo de protocolo',
                'ordem' => 10,
                'ativo' => true
            ]
        );

        // Buscar tipos de parâmetros
        $tipoTexto = TipoParametro::firstOrCreate(
            ['codigo' => 'texto'],
            [
                'nome' => 'Texto',
                'ativo' => true
            ]
        );

        $tipoSelecao = TipoParametro::firstOrCreate(
            ['codigo' => 'selecao'],
            [
                'nome' => 'Seleção',
                'ativo' => true
            ]
        );

        $tipoBoolean = TipoParametro::firstOrCreate(
            ['codigo' => 'boolean'],
            [
                'nome' => 'Sim/Não',
                'ativo' => true
            ]
        );

        // Parâmetros de número de processo
        Parametro::updateOrCreate(
            ['codigo' => 'protocolo.formato_numero_processo'],
            [
                'nome' => 'Formato do Número de Processo',
                'descricao' => 'Define o formato do número de processo. Use as variáveis: {TIPO}, {ANO}, {SEQUENCIAL}, {MES}, {DIA}',
                'grupo_parametro_id' => $grupoProtocolo->id,
                'tipo_parametro_id' => $tipoTexto->id,
                'valor' => '{TIPO}/{ANO}/{SEQUENCIAL}',
                'valor_padrao' => '{TIPO}/{ANO}/{SEQUENCIAL}',
                'configuracao' => json_encode([
                    'placeholder' => 'Ex: {TIPO}/{ANO}/{SEQUENCIAL} resulta em PL/2025/0001',
                    'help' => 'Variáveis disponíveis: {TIPO} = Tipo da proposição, {ANO} = Ano atual, {SEQUENCIAL} = Número sequencial, {MES} = Mês atual, {DIA} = Dia atual'
                ]),
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 1,
                'help_text' => 'Define como o número de processo será formatado automaticamente'
            ]
        );

        Parametro::updateOrCreate(
            ['codigo' => 'protocolo.digitos_sequencial'],
            [
                'nome' => 'Quantidade de Dígitos do Sequencial',
                'descricao' => 'Define quantos dígitos terá o número sequencial (com zeros à esquerda)',
                'grupo_parametro_id' => $grupoProtocolo->id,
                'tipo_parametro_id' => $tipoSelecao->id,
                'valor' => '4',
                'valor_padrao' => '4',
                'configuracao' => json_encode([
                    'opcoes' => [
                        '3' => '3 dígitos (001)',
                        '4' => '4 dígitos (0001)',
                        '5' => '5 dígitos (00001)',
                        '6' => '6 dígitos (000001)'
                    ]
                ]),
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 2,
                'help_text' => 'Quantidade de dígitos para o número sequencial'
            ]
        );

        Parametro::updateOrCreate(
            ['codigo' => 'protocolo.reiniciar_sequencial_anualmente'],
            [
                'nome' => 'Reiniciar Sequencial Anualmente',
                'descricao' => 'Define se o número sequencial deve reiniciar a cada ano',
                'grupo_parametro_id' => $grupoProtocolo->id,
                'tipo_parametro_id' => $tipoBoolean->id,
                'valor' => '1',
                'valor_padrao' => '1',
                'configuracao' => json_encode([
                    'true_label' => 'Sim',
                    'false_label' => 'Não'
                ]),
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 3,
                'help_text' => 'Se habilitado, o sequencial reinicia em 1 a cada novo ano'
            ]
        );

        Parametro::updateOrCreate(
            ['codigo' => 'protocolo.sequencial_por_tipo'],
            [
                'nome' => 'Sequencial Separado por Tipo',
                'descricao' => 'Define se cada tipo de proposição terá seu próprio sequencial',
                'grupo_parametro_id' => $grupoProtocolo->id,
                'tipo_parametro_id' => $tipoBoolean->id,
                'valor' => '1',
                'valor_padrao' => '1',
                'configuracao' => json_encode([
                    'true_label' => 'Sim',
                    'false_label' => 'Não'
                ]),
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 4,
                'help_text' => 'Se habilitado, cada tipo (PL, PEC, etc) terá numeração independente'
            ]
        );

        Parametro::updateOrCreate(
            ['codigo' => 'protocolo.permitir_numero_manual'],
            [
                'nome' => 'Permitir Número Manual',
                'descricao' => 'Permite que o usuário insira manualmente o número de processo',
                'grupo_parametro_id' => $grupoProtocolo->id,
                'tipo_parametro_id' => $tipoBoolean->id,
                'valor' => '0',
                'valor_padrao' => '0',
                'configuracao' => json_encode([
                    'true_label' => 'Sim',
                    'false_label' => 'Não'
                ]),
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 5,
                'help_text' => 'Se habilitado, permite inserção manual do número além do automático'
            ]
        );

        Parametro::updateOrCreate(
            ['codigo' => 'protocolo.prefixo_processo'],
            [
                'nome' => 'Prefixo do Processo',
                'descricao' => 'Prefixo opcional a ser adicionado antes do número do processo',
                'grupo_parametro_id' => $grupoProtocolo->id,
                'tipo_parametro_id' => $tipoTexto->id,
                'valor' => '',
                'valor_padrao' => '',
                'configuracao' => json_encode([
                    'placeholder' => 'Ex: PROC-',
                    'max_length' => 10
                ]),
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 6,
                'help_text' => 'Texto que aparecerá antes do número do processo (opcional)'
            ]
        );

        Parametro::updateOrCreate(
            ['codigo' => 'protocolo.sufixo_processo'],
            [
                'nome' => 'Sufixo do Processo',
                'descricao' => 'Sufixo opcional a ser adicionado após o número do processo',
                'grupo_parametro_id' => $grupoProtocolo->id,
                'tipo_parametro_id' => $tipoTexto->id,
                'valor' => '',
                'valor_padrao' => '',
                'configuracao' => json_encode([
                    'placeholder' => 'Ex: -CM',
                    'max_length' => 10
                ]),
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 7,
                'help_text' => 'Texto que aparecerá após o número do processo (opcional)'
            ]
        );

        Parametro::updateOrCreate(
            ['codigo' => 'protocolo.inserir_numero_documento'],
            [
                'nome' => 'Inserir Número no Documento',
                'descricao' => 'Define se o número de processo deve ser inserido automaticamente no documento',
                'grupo_parametro_id' => $grupoProtocolo->id,
                'tipo_parametro_id' => $tipoBoolean->id,
                'valor' => '1',
                'valor_padrao' => '1',
                'configuracao' => json_encode([
                    'true_label' => 'Sim',
                    'false_label' => 'Não'
                ]),
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 8,
                'help_text' => 'Se habilitado, o número de processo será automaticamente inserido no documento'
            ]
        );

        Parametro::updateOrCreate(
            ['codigo' => 'protocolo.posicao_numero_documento'],
            [
                'nome' => 'Posição do Número no Documento',
                'descricao' => 'Define onde o número de processo será inserido no documento',
                'grupo_parametro_id' => $grupoProtocolo->id,
                'tipo_parametro_id' => $tipoSelecao->id,
                'valor' => 'cabecalho',
                'valor_padrao' => 'cabecalho',
                'configuracao' => json_encode([
                    'opcoes' => [
                        'cabecalho' => 'No cabeçalho do documento',
                        'rodape' => 'No rodapé do documento',
                        'primeira_pagina' => 'Na primeira página (canto superior direito)',
                        'marca_dagua' => 'Como marca d\'água',
                        'nao_inserir' => 'Não inserir no documento'
                    ]
                ]),
                'obrigatorio' => true,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 9,
                'help_text' => 'Localização do número de processo no documento'
            ]
        );

        echo "Parâmetros de número de processo criados com sucesso!\n";
    }
}