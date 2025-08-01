<?php

namespace App\Http\Controllers;

use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ImageUploadController extends Controller
{
    protected ImageUploadService $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
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