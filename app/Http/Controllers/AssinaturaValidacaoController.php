<?php

namespace App\Http\Controllers;

use App\Services\AssinaturaValidacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AssinaturaValidacaoController extends Controller
{
    private AssinaturaValidacaoService $validacaoService;

    public function __construct(AssinaturaValidacaoService $validacaoService)
    {
        $this->validacaoService = $validacaoService;
    }

    /**
     * Show signature validation form
     */
    public function mostrarFormulario(Request $request)
    {
        $codigo = $request->get('codigo');
        $resultado = null;

        if ($codigo) {
            $resultado = $this->validacaoService->validarAssinaturaPorCodigo($codigo);
        }

        return view('validacao.assinatura', [
            'codigo' => $codigo,
            'resultado' => $resultado
        ]);
    }

    /**
     * Validate signature by code (API endpoint)
     */
    public function validarAssinatura(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|min:19|max:19' // Format: XXXX-XXXX-XXXX-XXXX
        ]);

        $codigo = strtoupper(trim($request->codigo));
        
        // Log validation attempt
        Log::info('Tentativa de validação de assinatura', [
            'codigo' => $codigo,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $resultado = $this->validacaoService->validarAssinaturaPorCodigo($codigo);

        if (!$resultado) {
            return response()->json([
                'success' => false,
                'message' => 'Código de validação não encontrado ou inválido.',
                'codigo' => $codigo
            ], 404);
        }

        // Log successful validation
        Log::info('Validação de assinatura bem-sucedida', [
            'codigo' => $codigo,
            'proposicao_id' => $resultado['proposicao']['id'],
            'ip' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assinatura digital válida.',
            'dados' => $resultado
        ]);
    }

    /**
     * Display signature validation certificate (for printing or saving)
     */
    public function certificadoValidacao(string $codigo)
    {
        $codigo = strtoupper(trim($codigo));
        $resultado = $this->validacaoService->validarAssinaturaPorCodigo($codigo);

        if (!$resultado) {
            abort(404, 'Código de validação não encontrado.');
        }

        // Log certificate access
        Log::info('Acesso ao certificado de validação', [
            'codigo' => $codigo,
            'proposicao_id' => $resultado['proposicao']['id'],
            'ip' => request()->ip()
        ]);

        return view('validacao.certificado', [
            'codigo' => $codigo,
            'resultado' => $resultado
        ]);
    }

    /**
     * Get validation QR code image
     */
    public function qrCodeValidacao(string $codigo)
    {
        $codigo = strtoupper(trim($codigo));
        $resultado = $this->validacaoService->validarAssinaturaPorCodigo($codigo);

        if (!$resultado) {
            abort(404, 'Código de validação não encontrado.');
        }

        // Generate QR code for this specific validation URL
        $urlValidacao = $this->validacaoService->gerarUrlValidacao($codigo);
        $qrCodeBase64 = $this->validacaoService->gerarQRCodeValidacao($urlValidacao);

        if (!$qrCodeBase64) {
            abort(500, 'Erro ao gerar QR Code.');
        }

        $qrCodeData = base64_decode($qrCodeBase64);

        return response($qrCodeData)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'inline; filename="qr-validacao-' . $codigo . '.png"')
            ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
    }
}