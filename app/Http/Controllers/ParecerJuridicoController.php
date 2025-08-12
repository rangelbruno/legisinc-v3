<?php

namespace App\Http\Controllers;

use App\Models\ParecerJuridico;
use App\Models\Proposicao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParecerJuridicoController extends Controller
{
    /**
     * Listar proposições para parecer jurídico
     */
    public function index(Request $request)
    {
        try {
            // Base query for propositions that need legal opinion
            $query = Proposicao::with(['autor', 'parecerJuridico.assessor'])
                ->where(function($q) {
                    $q->whereIn('status', ['protocolado', 'enviado_protocolo'])
                      ->orWhereNotNull('numero_protocolo');
                });

            // Filtros
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('ementa', 'LIKE', "%{$search}%")
                      ->orWhere('tipo', 'LIKE', "%{$search}%")
                      ->orWhere('numero_protocolo', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('status_parecer')) {
                if ($request->status_parecer === 'com_parecer') {
                    $query->where('tem_parecer_juridico', true);
                } else {
                    $query->where('tem_parecer_juridico', false);
                }
            }

            if ($request->filled('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            $proposicoes = $query->orderBy('data_protocolo', 'desc')
                                ->orderBy('id', 'desc')
                                ->paginate(15);

            $tipos = Proposicao::select('tipo')
                              ->distinct()
                              ->orderBy('tipo')
                              ->pluck('tipo');

            return view('modules.parecer-juridico.index', compact('proposicoes', 'tipos'));

        } catch (\Exception $e) {
            // Log::error('Erro ao listar proposições para parecer jurídico: ' . $e->getMessage());
            return back()->with('error', 'Erro ao carregar proposições.');
        }
    }

    /**
     * Mostrar formulário para emitir parecer
     */
    public function create(Proposicao $proposicao)
    {
        try {
            // Verificar se já existe parecer
            if ($proposicao->tem_parecer_juridico && $proposicao->parecerJuridico) {
                return redirect()->route('parecer-juridico.show', $proposicao->parecerJuridico)
                               ->with('info', 'Esta proposição já possui parecer jurídico.');
            }

            return view('modules.parecer-juridico.create', compact('proposicao'));

        } catch (\Exception $e) {
            // Log::error('Erro ao exibir formulário de parecer: ' . $e->getMessage());
            return back()->with('error', 'Erro ao carregar formulário.');
        }
    }

    /**
     * Armazenar parecer jurídico
     */
    public function store(Request $request, Proposicao $proposicao)
    {
        $validator = Validator::make($request->all(), [
            'tipo_parecer' => 'required|in:FAVORAVEL,CONTRARIO,COM_EMENDAS',
            'fundamentacao' => 'required|string|min:50',
            'conclusao' => 'required|string|min:20',
            'emendas' => 'nullable|string',
        ], [
            'tipo_parecer.required' => 'O tipo de parecer é obrigatório.',
            'tipo_parecer.in' => 'Tipo de parecer inválido.',
            'fundamentacao.required' => 'A fundamentação é obrigatória.',
            'fundamentacao.min' => 'A fundamentação deve ter pelo menos 50 caracteres.',
            'conclusao.required' => 'A conclusão é obrigatória.',
            'conclusao.min' => 'A conclusão deve ter pelo menos 20 caracteres.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Verificar se já existe parecer
            if ($proposicao->tem_parecer_juridico) {
                return back()->with('error', 'Esta proposição já possui parecer jurídico.');
            }

            // Criar parecer jurídico
            $parecer = ParecerJuridico::create([
                'proposicao_id' => $proposicao->id,
                'assessor_id' => Auth::id(),
                'tipo_parecer' => $request->tipo_parecer,
                'fundamentacao' => $request->fundamentacao,
                'conclusao' => $request->conclusao,
                'emendas' => $request->emendas,
                'data_emissao' => now(),
            ]);

            // Atualizar proposição
            $proposicao->update([
                'tem_parecer_juridico' => true,
                'parecer_id' => $parecer->id,
            ]);

            // Registrar log de tramitação
            DB::table('tramitacao_logs')->insert([
                'proposicao_id' => $proposicao->id,
                'user_id' => Auth::id(),
                'acao' => 'PARECER_EMITIDO',
                'status_anterior' => $proposicao->status,
                'status_novo' => $proposicao->status,
                'observacoes' => "Parecer jurídico {$parecer->getTipoParecerFormatado()} emitido",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('parecer-juridico.show', $parecer)
                           ->with('success', 'Parecer jurídico emitido com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Erro ao salvar parecer jurídico: ' . $e->getMessage());
            return back()->with('error', 'Erro ao salvar parecer jurídico.')->withInput();
        }
    }

    /**
     * Visualizar parecer jurídico
     */
    public function show(ParecerJuridico $parecerJuridico)
    {
        try {
            $parecerJuridico->load(['proposicao.autor', 'assessor']);
            
            return view('modules.parecer-juridico.show', compact('parecerJuridico'));

        } catch (\Exception $e) {
            // Log::error('Erro ao exibir parecer jurídico: ' . $e->getMessage());
            return back()->with('error', 'Erro ao carregar parecer.');
        }
    }

    /**
     * Mostrar formulário para editar parecer
     */
    public function edit(ParecerJuridico $parecerJuridico)
    {
        try {
            // Verificar se o usuário atual é o autor do parecer
            if ($parecerJuridico->assessor_id !== Auth::id()) {
                return back()->with('error', 'Você não tem permissão para editar este parecer.');
            }

            $parecerJuridico->load(['proposicao']);
            
            return view('modules.parecer-juridico.edit', compact('parecerJuridico'));

        } catch (\Exception $e) {
            // Log::error('Erro ao exibir formulário de edição: ' . $e->getMessage());
            return back()->with('error', 'Erro ao carregar formulário.');
        }
    }

    /**
     * Atualizar parecer jurídico
     */
    public function update(Request $request, ParecerJuridico $parecerJuridico)
    {
        // Verificar se o usuário atual é o autor do parecer
        if ($parecerJuridico->assessor_id !== Auth::id()) {
            return back()->with('error', 'Você não tem permissão para editar este parecer.');
        }

        $validator = Validator::make($request->all(), [
            'tipo_parecer' => 'required|in:FAVORAVEL,CONTRARIO,COM_EMENDAS',
            'fundamentacao' => 'required|string|min:50',
            'conclusao' => 'required|string|min:20',
            'emendas' => 'nullable|string',
        ], [
            'tipo_parecer.required' => 'O tipo de parecer é obrigatório.',
            'tipo_parecer.in' => 'Tipo de parecer inválido.',
            'fundamentacao.required' => 'A fundamentação é obrigatória.',
            'fundamentacao.min' => 'A fundamentação deve ter pelo menos 50 caracteres.',
            'conclusao.required' => 'A conclusão é obrigatória.',
            'conclusao.min' => 'A conclusão deve ter pelo menos 20 caracteres.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $parecerJuridico->update([
                'tipo_parecer' => $request->tipo_parecer,
                'fundamentacao' => $request->fundamentacao,
                'conclusao' => $request->conclusao,
                'emendas' => $request->emendas,
            ]);

            // Registrar log de tramitação
            DB::table('tramitacao_logs')->insert([
                'proposicao_id' => $parecerJuridico->proposicao_id,
                'user_id' => Auth::id(),
                'acao' => 'PARECER_ATUALIZADO',
                'status_anterior' => $parecerJuridico->proposicao->status,
                'status_novo' => $parecerJuridico->proposicao->status,
                'observacoes' => "Parecer jurídico {$parecerJuridico->getTipoParecerFormatado()} atualizado",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('parecer-juridico.show', $parecerJuridico)
                           ->with('success', 'Parecer jurídico atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Erro ao atualizar parecer jurídico: ' . $e->getMessage());
            return back()->with('error', 'Erro ao atualizar parecer jurídico.')->withInput();
        }
    }

    /**
     * Gerar PDF do parecer jurídico
     */
    public function generatePDF(ParecerJuridico $parecerJuridico)
    {
        try {
            // TODO: Implementar geração de PDF
            return back()->with('info', 'Funcionalidade de PDF em desenvolvimento.');

        } catch (\Exception $e) {
            // Log::error('Erro ao gerar PDF do parecer: ' . $e->getMessage());
            return back()->with('error', 'Erro ao gerar PDF.');
        }
    }

    /**
     * Listar pareceres do assessor atual
     */
    public function meusPareceres(Request $request)
    {
        try {
            $query = ParecerJuridico::with(['proposicao.autor'])
                                   ->where('assessor_id', Auth::id());

            // Filtros
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('proposicao', function($q) use ($search) {
                    $q->where('ementa', 'LIKE', "%{$search}%")
                      ->orWhere('tipo', 'LIKE', "%{$search}%")
                      ->orWhere('numero_protocolo', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('tipo_parecer')) {
                $query->where('tipo_parecer', $request->tipo_parecer);
            }

            $pareceres = $query->orderBy('data_emissao', 'desc')
                              ->paginate(15);

            return view('modules.parecer-juridico.meus-pareceres', compact('pareceres'));

        } catch (\Exception $e) {
            // Log::error('Erro ao listar meus pareceres: ' . $e->getMessage());
            return back()->with('error', 'Erro ao carregar pareceres.');
        }
    }
}