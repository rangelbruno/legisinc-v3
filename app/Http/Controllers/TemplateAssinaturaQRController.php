<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Parametro\ParametroService;
use App\Models\Parametro\ParametroModulo;
use App\Models\Parametro\ParametroSubmodulo;
use App\Models\Parametro\ParametroCampo;
use App\Models\Parametro\ParametroValor;
use Illuminate\Support\Facades\Log;

class TemplateAssinaturaQRController extends Controller
{
    protected $parametroService;

    public function __construct()
    {
        // Inicializamos o serviço dentro do método para evitar problemas de dependency injection
    }

    /**
     * Exibir a página de configuração de assinatura e QR Code
     */
    public function index()
    {
        try {
            // Buscar o módulo Templates
            $modulo = ParametroModulo::where('nome', 'Templates')->first();
            
            if (!$modulo) {
                Log::error('Módulo Templates não encontrado');
                return redirect()->back()->with('error', 'Módulo Templates não encontrado. Execute o seeder ParametrosTemplatesSeeder.');
            }

            // Buscar o submódulo 'Assinatura e QR Code'
            $submodulo = ParametroSubmodulo::where('modulo_id', $modulo->id)
                ->where('nome', 'Assinatura e QR Code')
                ->first();

            if (!$submodulo) {
                return redirect()->back()->with('error', 'Submódulo "Assinatura e QR Code" não encontrado. Execute o seeder TemplateProposicaoParametroSeeder.');
            }

            // Obter configurações diretamente do banco
            $configuracoes = $this->obterConfiguracoesDiretas($submodulo->id);

            return view('modules.parametros.templates.assinatura-qrcode', [
                'moduloId' => $modulo->id,
                'configuracoes' => $configuracoes
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao carregar configurações de assinatura e QR Code', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Salvar as configurações de assinatura e QR Code
     */
    public function store(Request $request)
    {
        try {
            // Validar dados
            $request->validate([
                'assinatura_posicao' => 'required|in:rodape_esquerda,rodape_centro,rodape_direita,final_documento_esquerda,final_documento_centro,final_documento_direita,pagina_separada',
                'assinatura_texto' => 'nullable|string|max:1000',
                'qrcode_posicao' => 'required|in:rodape_esquerda,rodape_centro,rodape_direita,cabecalho_esquerda,cabecalho_direita,final_documento_esquerda,final_documento_centro,final_documento_direita,lateral_direita,desabilitado',
                'qrcode_tamanho' => 'required|integer|min:50|max:300',
                'qrcode_texto' => 'nullable|string|max:500',
                'qrcode_url_formato' => 'required|string|max:500',
                'assinatura_apenas_protocolo' => 'nullable|boolean',
                'qrcode_apenas_protocolo' => 'nullable|boolean',
            ]);

            // Buscar módulo e submódulo
            $modulo = ParametroModulo::where('nome', 'Templates')->first();
            if (!$modulo) {
                throw new \Exception('Módulo Templates não encontrado');
            }

            $submodulo = ParametroSubmodulo::where('modulo_id', $modulo->id)
                ->where('nome', 'Assinatura e QR Code')
                ->first();
            if (!$submodulo) {
                throw new \Exception('Submódulo "Assinatura e QR Code" não encontrado');
            }

            // Salvar configurações diretamente
            $this->salvarConfiguracoesDiretas($submodulo->id, $request);

            Log::info('Configurações de assinatura e QR Code salvas com sucesso', [
                'user_id' => auth()->id(),
                'configuracoes' => $request->only([
                    'assinatura_posicao', 'assinatura_texto', 'qrcode_posicao', 
                    'qrcode_tamanho', 'qrcode_texto', 'qrcode_url_formato',
                    'assinatura_apenas_protocolo', 'qrcode_apenas_protocolo'
                ])
            ]);

            return redirect()
                ->route('parametros.templates.assinatura-qrcode')
                ->with('success', 'Configurações de assinatura e QR Code salvas com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações de assinatura e QR Code', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Erro ao salvar as configurações: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Obter configurações para uso em outros controllers/services
     */
    public function getConfiguracoes()
    {
        // Obter módulo e submódulo
        $modulo = ParametroModulo::where('nome', 'Templates')->first();
        if (!$modulo) return [];
        
        $submodulo = ParametroSubmodulo::where('modulo_id', $modulo->id)
            ->where('nome', 'Assinatura e QR Code')
            ->first();
        if (!$submodulo) return [];
        
        return $this->obterConfiguracoesDiretas($submodulo->id);
    }

    /**
     * Obter configurações diretamente do banco de dados
     */
    private function obterConfiguracoesDiretas($submoduloId)
    {
        $campos = ParametroCampo::where('submodulo_id', $submoduloId)
            ->with('valores')
            ->get();

        $configuracoes = [];
        
        foreach ($campos as $campo) {
            $valor = $campo->valores->first() ? $campo->valores->first()->valor : null;
            
            // Definir valores padrão se não existir
            switch ($campo->nome) {
                case 'assinatura_posicao':
                    $configuracoes[$campo->nome] = $valor ?: 'rodape_direita';
                    break;
                case 'assinatura_texto':
                    $configuracoes[$campo->nome] = $valor ?: "Documento assinado digitalmente por:\n{autor_nome}\n{autor_cargo}\nEm {data_assinatura}";
                    break;
                case 'qrcode_posicao':
                    $configuracoes[$campo->nome] = $valor ?: 'rodape_esquerda';
                    break;
                case 'qrcode_tamanho':
                    $configuracoes[$campo->nome] = $valor ?: '100';
                    break;
                case 'qrcode_texto':
                    $configuracoes[$campo->nome] = $valor ?: "Consulte este documento online:\nProtocolo: {numero_protocolo}";
                    break;
                case 'qrcode_url_formato':
                    $configuracoes[$campo->nome] = $valor ?: '{base_url}/proposicoes/consulta/{numero_protocolo}';
                    break;
                case 'assinatura_apenas_protocolo':
                    $configuracoes[$campo->nome] = $valor ?: '1';
                    break;
                case 'qrcode_apenas_protocolo':
                    $configuracoes[$campo->nome] = $valor ?: '1';
                    break;
                default:
                    $configuracoes[$campo->nome] = $valor;
            }
        }

        return $configuracoes;
    }

    /**
     * Salvar configurações diretamente no banco de dados
     */
    private function salvarConfiguracoesDiretas($submoduloId, Request $request)
    {
        $campos = ParametroCampo::where('submodulo_id', $submoduloId)->get();

        foreach ($campos as $campo) {
            $valor = null;
            
            switch ($campo->nome) {
                case 'assinatura_posicao':
                    $valor = $request->input('assinatura_posicao');
                    break;
                case 'assinatura_texto':
                    $valor = $request->input('assinatura_texto', '');
                    break;
                case 'qrcode_posicao':
                    $valor = $request->input('qrcode_posicao');
                    break;
                case 'qrcode_tamanho':
                    $valor = $request->input('qrcode_tamanho');
                    break;
                case 'qrcode_texto':
                    $valor = $request->input('qrcode_texto', '');
                    break;
                case 'qrcode_url_formato':
                    $valor = $request->input('qrcode_url_formato');
                    break;
                case 'assinatura_apenas_protocolo':
                    $valor = $request->has('assinatura_apenas_protocolo') ? '1' : '0';
                    break;
                case 'qrcode_apenas_protocolo':
                    $valor = $request->has('qrcode_apenas_protocolo') ? '1' : '0';
                    break;
            }

            if ($valor !== null) {
                ParametroValor::updateOrCreate(
                    ['campo_id' => $campo->id],
                    [
                        'valor' => $valor,
                        'tipo_valor' => in_array($campo->nome, ['assinatura_apenas_protocolo', 'qrcode_apenas_protocolo']) ? 'boolean' : 'string',
                        'user_id' => auth()->id()
                    ]
                );
            }
        }
    }
}