<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DocumentExtractionService
{
    /**
     * Extrair texto de arquivo DOCX
     */
    public function extractTextFromDocx(string $filePath): string
    {
        if (!Storage::exists($filePath)) {
            return '';
        }
        
        $fullPath = storage_path('app/' . $filePath);
        
        try {
            $zip = new ZipArchive();
            
            if ($zip->open($fullPath) === TRUE) {
                // Extrair o document.xml que contém o texto principal
                $documentXml = $zip->getFromName('word/document.xml');
                $zip->close();
                
                if ($documentXml) {
                    return $this->extractTextFromDocumentXml($documentXml);
                }
            }
        } catch (\Exception $e) {
            // Log::warning('Erro ao extrair texto do DOCX', [
            //     'file_path' => $filePath,
            //     'error' => $e->getMessage()
            // ]);
        }
        
        return '';
    }
    
    /**
     * Extrair texto de arquivo DOCX usando caminho absoluto
     */
    public function extractTextFromDocxFile(string $absolutePath): string
    {
        if (!file_exists($absolutePath)) {
            return '';
        }
        
        try {
            $zip = new ZipArchive();
            
            if ($zip->open($absolutePath) === TRUE) {
                // Extrair o document.xml que contém o texto principal
                $documentXml = $zip->getFromName('word/document.xml');
                $zip->close();
                
                if ($documentXml) {
                    return $this->extractTextFromDocumentXml($documentXml);
                }
            }
        } catch (\Exception $e) {
            // Log::warning('Erro ao extrair texto do DOCX', [
            //     'absolute_path' => $absolutePath,
            //     'error' => $e->getMessage()
            // ]);
        }
        
        return '';
    }
    
    /**
     * Extrair texto limpo do XML do Word
     */
    private function extractTextFromDocumentXml(string $xml): string
    {
        try {
            // Usar regex simples que já sabemos que funciona
            return $this->fallbackTextExtraction($xml);
            
        } catch (\Exception $e) {
            return '';
        }
    }
    
    /**
     * Processar formatação básica do texto
     */
    private function processTextFormatting(string $text): string
    {
        // Limpar espaços excessivos
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Adicionar quebras de linha em pontos apropriados
        $text = str_replace(['. ', '.\n'], ".\n", $text);
        
        return $text;
    }
    
    /**
     * Extração de fallback usando regex
     */
    private function fallbackTextExtraction(string $xml): string
    {
        // Extrair texto entre tags <w:t>
        preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/is', $xml, $matches);
        
        if (isset($matches[1]) && !empty($matches[1])) {
            $text = implode(' ', $matches[1]);
            $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1);
            return trim($text);
        }
        
        // Se não encontrou com w:t, tentar padrão mais amplo
        preg_match_all('/>([^<]+)</is', $xml, $allMatches);
        if (isset($allMatches[1]) && !empty($allMatches[1])) {
            $text = implode(' ', $allMatches[1]);
            $text = trim(preg_replace('/\s+/', ' ', $text));
            if (strlen($text) > 10) { // Só retornar se tiver conteúdo significativo
                return $text;
            }
        }
        
        return '';
    }
    
    /**
     * Verificar se arquivo é DOCX válido
     */
    public function isValidDocx(string $filePath): bool
    {
        if (!Storage::exists($filePath)) {
            return false;
        }
        
        $fullPath = storage_path('app/' . $filePath);
        
        try {
            $zip = new ZipArchive();
            if ($zip->open($fullPath) === TRUE) {
                $hasDocument = $zip->locateName('word/document.xml') !== false;
                $zip->close();
                return $hasDocument;
            }
        } catch (\Exception $e) {
            return false;
        }
        
        return false;
    }
    
    /**
     * Obter metadados do documento DOCX
     */
    public function getDocxMetadata(string $filePath): array
    {
        if (!Storage::exists($filePath)) {
            return [];
        }
        
        $fullPath = storage_path('app/' . $filePath);
        
        try {
            $zip = new ZipArchive();
            if ($zip->open($fullPath) === TRUE) {
                $coreXml = $zip->getFromName('docProps/core.xml');
                $zip->close();
                
                if ($coreXml) {
                    return $this->parseMetadataXml($coreXml);
                }
            }
        } catch (\Exception $e) {
            // Silently fail
        }
        
        return [];
    }
    
    /**
     * Analisar XML de metadados
     */
    private function parseMetadataXml(string $xml): array
    {
        $metadata = [];
        
        try {
            $dom = new \DOMDocument();
            $dom->loadXML($xml);
            
            // Extrair informações básicas
            $creator = $dom->getElementsByTagName('creator')->item(0);
            $lastModifiedBy = $dom->getElementsByTagName('lastModifiedBy')->item(0);
            $created = $dom->getElementsByTagName('created')->item(0);
            $modified = $dom->getElementsByTagName('modified')->item(0);
            
            if ($creator) $metadata['creator'] = $creator->textContent;
            if ($lastModifiedBy) $metadata['last_modified_by'] = $lastModifiedBy->textContent;
            if ($created) $metadata['created'] = $created->textContent;
            if ($modified) $metadata['modified'] = $modified->textContent;
            
        } catch (\Exception $e) {
            // Silently fail
        }
        
        return $metadata;
    }
}