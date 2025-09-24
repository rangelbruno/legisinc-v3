<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parametro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class EditorConfigController extends Controller
{
    private const PARAMETRO_CODIGO = 'editor.exibir_botao_exportar_pdf_s3';

    public function index()
    {
        // Buscar o parâmetro ou criar um valor padrão
        $exibirBotaoPDF = $this->getOrCreateParameter();

        return view('modules.parametros.editor-config', compact('exibirBotaoPDF'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exibir_botao_pdf' => 'nullable|in:0,1'
        ]);

        $valor = $request->input('exibir_botao_pdf', '0');

        try {
            // Tentar atualizar o parâmetro no banco
            $parametro = Parametro::where('codigo', self::PARAMETRO_CODIGO)->first();

            if ($parametro) {
                $parametro->update(['valor' => $valor]);
            } else {
                // Se não existir no banco, salvar no cache como fallback
                Cache::put('editor_config_' . self::PARAMETRO_CODIGO, $valor, 86400); // 24 horas
            }

            return redirect()->route('parametros.editor.config')
                ->with('success', 'Configurações do editor atualizadas com sucesso!');

        } catch (\Exception $e) {
            // Em caso de erro com o banco, usar cache
            Cache::put('editor_config_' . self::PARAMETRO_CODIGO, $valor, 86400);

            return redirect()->route('parametros.editor.config')
                ->with('success', 'Configurações salvas temporariamente (execute as migrações para persistir).');
        }
    }

    private function getOrCreateParameter()
    {
        try {
            // Tentar buscar no banco
            $parametro = Parametro::where('codigo', self::PARAMETRO_CODIGO)->first();

            if ($parametro) {
                return $parametro;
            }

            // Se não existe no banco, verificar cache
            $valorCache = Cache::get('editor_config_' . self::PARAMETRO_CODIGO, '1');

            // Criar objeto mock para compatibilidade com a view
            return (object) [
                'codigo' => self::PARAMETRO_CODIGO,
                'nome' => 'Exibir Botão Exportar PDF para S3',
                'valor' => $valorCache,
                'descricao' => 'Controla a exibição do botão de exportar PDF para S3 no editor OnlyOffice'
            ];

        } catch (\Exception $e) {
            // Em caso de erro, retornar valor padrão
            return (object) [
                'codigo' => self::PARAMETRO_CODIGO,
                'nome' => 'Exibir Botão Exportar PDF para S3',
                'valor' => '1',
                'descricao' => 'Controla a exibição do botão de exportar PDF para S3 no editor OnlyOffice'
            ];
        }
    }
}