<?php

namespace App\Http\Middleware;

use App\Services\Parametro\ParametroService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidacaoParametrosMiddleware
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$parametros): Response
    {
        foreach ($parametros as $parametro) {
            if (!$this->validarParametro($parametro, $request)) {
                return response()->json([
                    'erro' => "Parâmetro '{$parametro}' inválido ou não encontrado"
                ], 400);
            }
        }

        return $next($request);
    }

    /**
     * Valida um parâmetro específico
     */
    protected function validarParametro(string $parametro, Request $request): bool
    {
        // Formato esperado: modulo.submodulo.campo
        $partes = explode('.', $parametro);
        
        if (count($partes) !== 3) {
            return false;
        }

        [$modulo, $submodulo, $campo] = $partes;
        
        // Obter valor do request
        $valor = $request->input($campo);
        
        if ($valor === null) {
            return true; // Não validar se não há valor
        }

        try {
            return $this->parametroService->validar($modulo, $submodulo, $valor);
        } catch (\Exception $e) {
            return false;
        }
    }
}