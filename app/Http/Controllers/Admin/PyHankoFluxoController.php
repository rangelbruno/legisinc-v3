<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PyHankoFluxoController extends Controller
{
    /**
     * Exibir página do fluxo PyHanko
     */
    public function index()
    {
        // Verificar se usuário é admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado');
        }

        // Coletar informações do sistema para exibir na página
        $systemInfo = $this->coletarInformacoesSistema();
        
        return view('admin.pyhanko-fluxo.index', [
            'title' => 'Fluxo Assinatura Digital PyHanko',
            'systemInfo' => $systemInfo
        ]);
    }

    /**
     * Testar status do PyHanko
     */
    public function testarStatus()
    {
        try {
            // Verificar se imagem PyHanko existe
            $imagemExists = $this->verificarImagemPyHanko();
            
            // Testar binário PyHanko
            $binarioOk = $this->testarBinarioPyHanko();
            
            // Verificar arquivos de configuração
            $configExists = $this->verificarConfiguracoes();
            
            // Verificar scripts de teste
            $scriptsExists = $this->verificarScriptsTeste();
            
            return response()->json([
                'status' => 'success',
                'dados' => [
                    'imagem_pyhanko' => $imagemExists,
                    'binario_funcionando' => $binarioOk,
                    'configuracoes' => $configExists,
                    'scripts_teste' => $scriptsExists,
                    'timestamp' => now()->format('d/m/Y H:i:s')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao testar status PyHanko: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao verificar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Executar script de teste PyHanko
     */
    public function executarTeste(Request $request)
    {
        $request->validate([
            'tipo_teste' => 'required|in:funcional,compose,blindado'
        ]);

        try {
            $tipoTeste = $request->tipo_teste;
            $nomeScript = $this->obterNomeScript($tipoTeste);
            
            if (!file_exists($nomeScript)) {
                throw new \Exception("Script de teste não encontrado: {$nomeScript}");
            }

            // Executar script em background e retornar ID do processo
            $comando = "cd /home/bruno/legisinc && {$nomeScript} 2>&1";
            $output = [];
            $returnCode = 0;
            
            exec($comando, $output, $returnCode);
            
            return response()->json([
                'status' => $returnCode === 0 ? 'success' : 'warning',
                'output' => implode("\n", $output),
                'return_code' => $returnCode,
                'tipo_teste' => $tipoTeste
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao executar teste PyHanko: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao executar teste: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Coletar informações do sistema
     */
    private function coletarInformacoesSistema(): array
    {
        return [
            'versao_sistema' => 'v2.2 Final',
            'pyhanko_version' => $this->obterVersaoPyHanko(),
            'docker_disponivel' => $this->verificarDockerDisponivel(),
            'arquitetura_atual' => 'Container Efêmero',
            'profiles_configurados' => $this->verificarProfiles(),
            'ultima_atualizacao' => '08/09/2025'
        ];
    }

    /**
     * Verificar se imagem PyHanko existe
     */
    private function verificarImagemPyHanko(): array
    {
        try {
            $comando = 'docker images legisinc-pyhanko --format "{{.Repository}}:{{.Tag}} {{.Size}} {{.CreatedAt}}" 2>/dev/null';
            $output = shell_exec($comando);
            
            if (empty($output)) {
                return [
                    'existe' => false,
                    'detalhes' => 'Imagem não encontrada'
                ];
            }
            
            return [
                'existe' => true,
                'detalhes' => trim($output),
                'status' => 'ok'
            ];
            
        } catch (\Exception $e) {
            return [
                'existe' => false,
                'detalhes' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Testar binário PyHanko
     */
    private function testarBinarioPyHanko(): array
    {
        try {
            $comando = 'docker run --rm legisinc-pyhanko --version 2>/dev/null';
            $output = shell_exec($comando);
            
            if (empty($output)) {
                return [
                    'funcionando' => false,
                    'versao' => null,
                    'detalhes' => 'Binário não responde'
                ];
            }
            
            return [
                'funcionando' => true,
                'versao' => trim($output),
                'status' => 'ok'
            ];
            
        } catch (\Exception $e) {
            return [
                'funcionando' => false,
                'detalhes' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verificar configurações
     */
    private function verificarConfiguracoes(): array
    {
        $arquivos = [
            'pyhanko.yml' => 'docker/pyhanko/pyhanko.yml',
            'Dockerfile' => 'docker/pyhanko/Dockerfile',
            'docker-compose.yml' => 'docker-compose.yml'
        ];
        
        $status = [];
        foreach ($arquivos as $nome => $caminho) {
            $caminhoCompleto = base_path($caminho);
            $status[$nome] = [
                'existe' => file_exists($caminhoCompleto),
                'caminho' => $caminho,
                'tamanho' => file_exists($caminhoCompleto) ? filesize($caminhoCompleto) : 0
            ];
        }
        
        return $status;
    }

    /**
     * Verificar scripts de teste
     */
    private function verificarScriptsTeste(): array
    {
        $scripts = [
            'funcional' => 'scripts/teste-pyhanko-funcional.sh',
            'compose' => 'scripts/teste-pyhanko-compose-run.sh',
            'blindado' => 'scripts/teste-pyhanko-blindado-v22.sh'
        ];
        
        $status = [];
        foreach ($scripts as $nome => $caminho) {
            $caminhoCompleto = base_path($caminho);
            $status[$nome] = [
                'existe' => file_exists($caminhoCompleto),
                'executavel' => file_exists($caminhoCompleto) ? is_executable($caminhoCompleto) : false,
                'caminho' => $caminho
            ];
        }
        
        return $status;
    }

    /**
     * Obter versão do PyHanko
     */
    private function obterVersaoPyHanko(): ?string
    {
        try {
            $comando = 'docker run --rm legisinc-pyhanko --version 2>/dev/null';
            $output = shell_exec($comando);
            return $output ? trim($output) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Verificar se Docker está disponível
     */
    private function verificarDockerDisponivel(): bool
    {
        try {
            $output = shell_exec('which docker 2>/dev/null');
            return !empty($output);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verificar profiles configurados
     */
    private function verificarProfiles(): array
    {
        try {
            $comando = 'cd /home/bruno/legisinc && docker compose config --profiles 2>/dev/null';
            $output = shell_exec($comando);
            return $output ? explode("\n", trim($output)) : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obter nome do script de teste
     */
    private function obterNomeScript(string $tipo): string
    {
        $scripts = [
            'funcional' => base_path('scripts/teste-pyhanko-funcional.sh'),
            'compose' => base_path('scripts/teste-pyhanko-compose-run.sh'),
            'blindado' => base_path('scripts/teste-pyhanko-blindado-v22.sh')
        ];
        
        return $scripts[$tipo] ?? '';
    }
}