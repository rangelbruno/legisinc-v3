<?php

namespace App\Http\Controllers;

use App\Services\ImageUploadService;
use App\Services\Parametro\ParametroService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ImageUploadController extends Controller
{
    protected ImageUploadService $imageUploadService;
    protected ParametroService $parametroService;

    public function __construct(ImageUploadService $imageUploadService, ParametroService $parametroService)
    {
        $this->imageUploadService = $imageUploadService;
        $this->parametroService = $parametroService;
    }

    /**
     * Upload de imagem para template
     */
    public function uploadTemplateImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:10240' // Max 10MB
        ]);

        try {
            $path = $this->imageUploadService->uploadTemplateImage($request->file('image'));
            $url = $this->imageUploadService->getImageUrl($path);

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => $url,
                'message' => 'Imagem enviada com sucesso!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao fazer upload de imagem do template', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar imagem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload de imagem para proposição
     */
    public function uploadProposicaoImage(Request $request, int $proposicaoId): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:10240' // Max 10MB
        ]);

        try {
            $path = $this->imageUploadService->uploadProposicaoImage($request->file('image'), $proposicaoId);
            $url = $this->imageUploadService->getImageUrl($path);

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => $url,
                'message' => 'Imagem enviada com sucesso!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao fazer upload de imagem da proposição', [
                'proposicao_id' => $proposicaoId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar imagem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload da imagem do cabeçalho dos templates
     */
    public function uploadCabecalhoTemplate(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048' // Max 2MB
        ]);

        try {
            // Fazer upload da imagem para a pasta template no public
            $file = $request->file('image');
            $fileName = 'cabecalho.' . $file->getClientOriginalExtension();
            
            // Salvar diretamente no public/template
            $destinationPath = public_path('template');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $fileName);
            $relativePath = 'template/' . $fileName;
            
            // Salvar o caminho nos parâmetros
            $this->parametroService->salvarValor('Templates', 'Cabeçalho', 'cabecalho_imagem', $relativePath);
            
            $url = asset($relativePath);

            return response()->json([
                'success' => true,
                'path' => $relativePath,
                'url' => $url,
                'message' => 'Imagem do cabeçalho enviada com sucesso!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao fazer upload da imagem do cabeçalho', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar imagem do cabeçalho: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload múltiplo de imagens
     */
    public function uploadMultiple(Request $request): JsonResponse
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:10240',
            'folder' => 'nullable|string'
        ]);

        try {
            $folder = $request->input('folder', 'uploads');
            $paths = $this->imageUploadService->uploadMultipleToPublic($request->file('images'), $folder);
            
            $results = [];
            foreach ($paths as $path) {
                $results[] = [
                    'path' => $path,
                    'url' => $this->imageUploadService->getImageUrl($path)
                ];
            }

            return response()->json([
                'success' => true,
                'images' => $results,
                'message' => count($paths) . ' imagens enviadas com sucesso!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao fazer upload múltiplo de imagens', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar imagens: ' . $e->getMessage()
            ], 500);
        }
    }
}