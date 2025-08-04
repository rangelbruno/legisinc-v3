<?php

use App\Services\Template\TemplateProcessorService;
use App\Models\Proposicao;
use App\Models\User;
use App\Models\TipoProposicaoTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Template Processing', function () {
    it('can process system variables in templates', function () {
        $service = new TemplateProcessorService();
        
        $template = "Data atual: {data} - Ano: {ano_atual}";
        $processed = $service->processSystemVariables($template);
        
        expect($processed)->toContain(now()->format('d/m/Y'));
        expect($processed)->toContain((string) now()->year);
    });

    it('can process proposicao variables', function () {
        $proposicao = Proposicao::factory()->create([
            'tipo' => 'Projeto de Lei',
            'ementa' => 'Teste de ementa'
        ]);

        $service = new TemplateProcessorService();
        $template = "Tipo: {tipo_proposicao} - Ementa: {ementa}";
        
        $processed = $service->processProposicaoVariables($template, $proposicao);
        
        expect($processed)->toContain('Projeto de Lei');
        expect($processed)->toContain('Teste de ementa');
    });

    it('handles missing variables gracefully', function () {
        $service = new TemplateProcessorService();
        $template = "Variável inexistente: {variavel_inexistente}";
        
        $processed = $service->processSystemVariables($template);
        
        expect($processed)->toBe($template);
    });
});

describe('Document Processing Workflow', function () {
    it('can create proposicao with template', function () {
        $user = User::factory()->create();
        $template = TipoProposicaoTemplate::factory()->create();
        
        $proposicao = Proposicao::factory()->create([
            'template_id' => $template->id,
            'autor_id' => $user->id
        ]);

        expect($proposicao->template_id)->toBe($template->id);
        expect($proposicao->autor_id)->toBe($user->id);
        expect($proposicao->status)->toBe('rascunho');
    });

    it('can update proposicao status through workflow', function () {
        $proposicao = Proposicao::factory()->create(['status' => 'rascunho']);
        
        $proposicao->update(['status' => 'em_tramitacao']);
        
        expect($proposicao->fresh()->status)->toBe('em_tramitacao');
    });

    it('validates required fields in proposicao', function () {
        expect(function () {
            Proposicao::create([]);
        })->toThrow(\Illuminate\Database\QueryException::class);
    });
});

describe('Process Validation', function () {
    it('validates template content before processing', function () {
        $service = new TemplateProcessorService();
        
        $validTemplate = "Teste com {data} válida";
        $invalidTemplate = "Teste com {{{malformed}}} variável";
        
        expect($service->validateTemplate($validTemplate))->toBeTrue();
        expect($service->validateTemplate($invalidTemplate))->toBeFalse();
    });

    it('prevents processing of malicious content', function () {
        $service = new TemplateProcessorService();
        
        $maliciousTemplate = "<?php system('rm -rf /'); ?>";
        $processed = $service->processSystemVariables($maliciousTemplate);
        
        expect($processed)->not->toContain('<?php');
    });
});