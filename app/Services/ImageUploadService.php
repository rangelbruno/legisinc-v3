<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Upload de imagem para pasta public
     * 
     * @param UploadedFile $image
     * @param string $folder Pasta dentro de public onde salvar a imagem
     * @return string Caminho relativo da imagem salva
     */
    public function uploadToPublic(UploadedFile $image, string $folder = 'uploads'): string
    {
        // Gerar nome único para o arquivo
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        
        // Garantir que a pasta existe
        $folderPath = public_path($folder);
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }
        
        // Mover arquivo para a pasta public
        $image->move($folderPath, $filename);
        
        // Retornar caminho relativo
        return $folder . '/' . $filename;
    }
    
    /**
     * Upload múltiplo de imagens
     * 
     * @param array $images Array de UploadedFile
     * @param string $folder Pasta dentro de public onde salvar as imagens
     * @return array Array com caminhos relativos das imagens salvas
     */
    public function uploadMultipleToPublic(array $images, string $folder = 'uploads'): array
    {
        $paths = [];
        
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $paths[] = $this->uploadToPublic($image, $folder);
            }
        }
        
        return $paths;
    }
    
    /**
     * Upload de imagem para templates/proposições
     * 
     * @param UploadedFile $image
     * @return string Caminho relativo da imagem salva
     */
    public function uploadTemplateImage(UploadedFile $image): string
    {
        return $this->uploadToPublic($image, 'template/images');
    }
    
    /**
     * Upload de imagem para proposições
     * 
     * @param UploadedFile $image
     * @param int $proposicaoId ID da proposição
     * @return string Caminho relativo da imagem salva
     */
    public function uploadProposicaoImage(UploadedFile $image, int $proposicaoId): string
    {
        $folder = 'proposicoes/' . $proposicaoId;
        return $this->uploadToPublic($image, $folder);
    }
    
    /**
     * Deletar imagem da pasta public
     * 
     * @param string $path Caminho relativo da imagem
     * @return bool
     */
    public function deleteFromPublic(string $path): bool
    {
        $fullPath = public_path($path);
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }
    
    /**
     * Verificar se imagem existe
     * 
     * @param string $path Caminho relativo da imagem
     * @return bool
     */
    public function imageExists(string $path): bool
    {
        return file_exists(public_path($path));
    }
    
    /**
     * Obter URL completa da imagem
     * 
     * @param string $path Caminho relativo da imagem
     * @return string
     */
    public function getImageUrl(string $path): string
    {
        return asset($path);
    }
}