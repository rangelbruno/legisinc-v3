<?php

namespace App\Http\Controllers;

use App\Services\OnlyOffice\OnlyOfficeService;
use Illuminate\Http\Request;

class OnlyOfficeController extends Controller
{
    public function __construct(
        private OnlyOfficeService $onlyOfficeService
    ) {}

    /**
     * Callback do ONLYOFFICE
     */
    public function callback(Request $request, string $documentKey)
    {
        $data = $request->all();
        
        \Log::info('OnlyOffice callback received', [
            'document_key' => $documentKey,
            'data' => $data
        ]);

        try {
            $resultado = $this->onlyOfficeService->processarCallback($documentKey, $data);
            return response()->json($resultado);
        } catch (\Exception $e) {
            \Log::error('OnlyOffice callback error', [
                'document_key' => $documentKey,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 1]);
        }
    }
}