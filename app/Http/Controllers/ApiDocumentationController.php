<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiDocumentationService;

class ApiDocumentationController extends Controller
{
    protected $apiDocumentationService;

    public function __construct(ApiDocumentationService $apiDocumentationService)
    {
        $this->apiDocumentationService = $apiDocumentationService;
    }

    public function index()
    {
        $documentationData = $this->apiDocumentationService->getDocumentationData();
        
        return view('api-documentation.index', compact('documentationData'));
    }
} 