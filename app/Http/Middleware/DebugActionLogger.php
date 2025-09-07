<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class DebugActionLogger
{
    /**
     * Handle an incoming request and log user actions
     */
    public function handle(Request $request, Closure $next)
    {
        // Só ativar se debug estiver ativado na sessão
        if (!session('debug_logger_active', false)) {
            return $next($request);
        }

        $startTime = microtime(true);
        $memoryStart = memory_get_usage();

        // Capturar dados da requisição
        $requestData = [
            'session_id' => session()->getId(),
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'user_role' => Auth::user()?->roles()->first()?->name,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route_name' => Route::currentRouteName(),
            'route_action' => Route::currentRouteAction(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'timestamp' => now()->toISOString(),
        ];

        // Capturar parâmetros (filtrando dados sensíveis)
        $parameters = $this->filterSensitiveData($request->all());
        if (!empty($parameters)) {
            $requestData['parameters'] = $parameters;
        }

        // Capturar headers importantes
        $importantHeaders = [
            'accept', 'content-type', 'x-requested-with', 'x-csrf-token'
        ];
        $headers = [];
        foreach ($importantHeaders as $header) {
            if ($request->hasHeader($header)) {
                $headers[$header] = $request->header($header);
            }
        }
        $requestData['headers'] = $headers;

        // Executar a requisição
        $response = $next($request);

        // Capturar dados da resposta
        $endTime = microtime(true);
        $memoryEnd = memory_get_usage();
        
        $responseData = [
            'status_code' => $response->getStatusCode(),
            'content_type' => $response->headers->get('content-type'),
            'duration_ms' => round(($endTime - $startTime) * 1000, 2),
            'memory_usage_mb' => round(($memoryEnd - $memoryStart) / 1024 / 1024, 2),
            'response_size' => strlen($response->getContent()),
        ];

        // Detectar tipo de ação baseada na rota/método
        $actionType = $this->detectActionType($request);
        
        // Log estruturado para o debug logger
        Log::channel('debug_actions')->info('user_action', [
            'action_type' => $actionType,
            'request' => $requestData,
            'response' => $responseData,
            'is_error' => $response->getStatusCode() >= 400,
            'debug_session' => session('debug_session_id')
        ]);

        // Adicionar header para o JavaScript capturar
        $response->headers->set('X-Debug-Action-Type', $actionType);
        $response->headers->set('X-Debug-Duration', $responseData['duration_ms']);

        return $response;
    }

    /**
     * Detecta o tipo de ação baseado na requisição
     */
    private function detectActionType(Request $request): string
    {
        $routeName = Route::currentRouteName();
        $method = $request->method();
        $url = $request->path();

        // Ações específicas do sistema Legisinc
        if (str_contains($routeName, 'proposicoes')) {
            if ($method === 'POST' && str_contains($routeName, 'store')) {
                return 'proposicao_create';
            } elseif ($method === 'PUT' && str_contains($routeName, 'update')) {
                return 'proposicao_update';
            } elseif (str_contains($routeName, 'show')) {
                return 'proposicao_view';
            } elseif (str_contains($routeName, 'pdf')) {
                return 'proposicao_pdf_view';
            } elseif (str_contains($routeName, 'assinar')) {
                return 'proposicao_sign';
            } elseif (str_contains($routeName, 'protocolar')) {
                return 'proposicao_protocol';
            }
        }

        if (str_contains($routeName, 'onlyoffice')) {
            return 'onlyoffice_edit';
        }

        if (str_contains($url, 'login')) {
            return 'auth_login';
        } elseif (str_contains($url, 'logout')) {
            return 'auth_logout';
        }

        // Tipos genéricos por método HTTP
        return match($method) {
            'GET' => 'page_view',
            'POST' => 'form_submit',
            'PUT', 'PATCH' => 'data_update',
            'DELETE' => 'data_delete',
            default => 'request'
        };
    }

    /**
     * Filtra dados sensíveis dos parâmetros
     */
    private function filterSensitiveData(array $data): array
    {
        $sensitiveKeys = [
            'password', 'password_confirmation', '_token', 'csrf_token',
            'api_key', 'secret', 'private_key', 'certificate'
        ];

        $filtered = [];
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $filtered[$key] = '[FILTERED]';
            } elseif (is_array($value)) {
                $filtered[$key] = $this->filterSensitiveData($value);
            } else {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }
}