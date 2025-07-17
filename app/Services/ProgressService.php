<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProgressService
{
    protected $progressFilePath;

    public function __construct()
    {
        $this->progressFilePath = base_path('docs/progress.md');
    }

    public function getProgressData()
    {
        $content = $this->getProgressFileContent();
        
        return [
            'overview' => $this->extractOverviewData($content),
            'modules' => $this->extractModulesData($content),
            'statistics' => $this->extractStatisticsData($content),
            'technical' => $this->extractTechnicalData($content),
            'lastUpdate' => $this->extractLastUpdate($content)
        ];
    }

    protected function getProgressFileContent()
    {
        if (!File::exists($this->progressFilePath)) {
            throw new \Exception('Progress file not found');
        }

        return File::get($this->progressFilePath);
    }

    protected function extractOverviewData($content)
    {
        // Extrair dados gerais do projeto
        $overview = [];
        
        // Procurar por informações de status geral
        if (preg_match('/Status Atual: \*\*(\d+) Módulos Core Implementados\*\* \((\d+)% dos (\d+) módulos totais\)/', $content, $matches)) {
            $overview['implemented_modules'] = (int) $matches[1];
            $overview['percentage'] = (int) $matches[2];
            $overview['total_modules'] = (int) $matches[3];
        }

        // Extrair estrutura base
        if (preg_match('/### Estrutura Base \((\d+)% Concluída ([^)]+)\)/', $content, $matches)) {
            $overview['base_structure'] = [
                'percentage' => (int) $matches[1],
                'status' => $matches[2]
            ];
        }

        return $overview;
    }

    protected function extractModulesData($content)
    {
        $modules = [];
        
        // Módulos implementados
        preg_match_all('/#### (\d+)\. ([^✅\n]+)([✅]?)/', $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $moduleNumber = (int) $match[1];
            $moduleName = trim($match[2]);
            $isCompleted = !empty($match[3]);
            
            $modules[] = [
                'number' => $moduleNumber,
                'name' => $moduleName,
                'status' => $isCompleted ? 'completed' : 'pending',
                'category' => $this->determineModuleCategory($moduleNumber)
            ];
        }

        // Módulos planejados
        preg_match_all('/- \[ \] \*\*(\d+)\. ([^*]+)\*\*/', $content, $plannedMatches, PREG_SET_ORDER);
        
        foreach ($plannedMatches as $match) {
            $moduleNumber = (int) $match[1];
            $moduleName = trim($match[2]);
            
            $modules[] = [
                'number' => $moduleNumber,
                'name' => $moduleName,
                'status' => 'planned',
                'category' => $this->determineModuleCategory($moduleNumber)
            ];
        }

        return $modules;
    }

    protected function extractStatisticsData($content)
    {
        $statistics = [];
        
        // Estatísticas de progresso
        if (preg_match('/### Módulos por Status.*?- ✅ \*\*Implementados:\*\* (\d+) módulos \((\d+)%\).*?- 🚧 \*\*Em Desenvolvimento:\*\* (\d+) módulos \((\d+)%\).*?- 📋 \*\*Planejados:\*\* (\d+) módulos \((\d+)%\)/s', $content, $matches)) {
            $statistics['by_status'] = [
                'implemented' => ['count' => (int) $matches[1], 'percentage' => (int) $matches[2]],
                'in_development' => ['count' => (int) $matches[3], 'percentage' => (int) $matches[4]],
                'planned' => ['count' => (int) $matches[5], 'percentage' => (int) $matches[6]]
            ];
        }

        // Funcionalidades por categoria
        if (preg_match('/### Funcionalidades por Categoria.*?- ✅ \*\*Core Business:\*\* (\d+)\/(\d+) módulos \((\d+)%\).*?- 📋 \*\*Infraestrutura:\*\* (\d+)\/(\d+) módulos \((\d+)%\).*?- 📋 \*\*Inovação:\*\* (\d+)\/(\d+) módulos \((\d+)%\)/s', $content, $matches)) {
            $statistics['by_category'] = [
                'core_business' => ['implemented' => (int) $matches[1], 'total' => (int) $matches[2], 'percentage' => (int) $matches[3]],
                'infrastructure' => ['implemented' => (int) $matches[4], 'total' => (int) $matches[5], 'percentage' => (int) $matches[6]],
                'innovation' => ['implemented' => (int) $matches[7], 'total' => (int) $matches[8], 'percentage' => (int) $matches[9]]
            ];
        }

        return $statistics;
    }

    protected function extractTechnicalData($content)
    {
        $technical = [];
        
        // Cobertura técnica
        if (preg_match('/### Cobertura Técnica.*?- ✅ \*\*Backend:\*\* (\d+)%.*?- ✅ \*\*Frontend:\*\* (\d+)%.*?- ✅ \*\*APIs:\*\* (\d+)%.*?- ✅ \*\*Database:\*\* (\d+)%.*?- ✅ \*\*Auth:\*\* (\d+)%.*?- ✅ \*\*Docker:\*\* (\d+)%/s', $content, $matches)) {
            $technical['coverage'] = [
                'backend' => (int) $matches[1],
                'frontend' => (int) $matches[2],
                'apis' => (int) $matches[3],
                'database' => (int) $matches[4],
                'auth' => (int) $matches[5],
                'docker' => (int) $matches[6]
            ];
        }

        return $technical;
    }

    protected function extractLastUpdate($content)
    {
        if (preg_match('/\*\*Última Atualização:\*\* ([0-9-]+)/', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function determineModuleCategory($moduleNumber)
    {
        if ($moduleNumber <= 9) {
            return 'core_business';
        } elseif ($moduleNumber <= 15) {
            return 'infrastructure';
        } else {
            return 'innovation';
        }
    }
} 