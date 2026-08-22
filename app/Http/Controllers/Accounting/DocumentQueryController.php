<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Services\DocumentQueryService;
use Illuminate\Http\Request;

class DocumentQueryController extends Controller
{
    protected DocumentQueryService $queryService;

    public function __construct(DocumentQueryService $queryService)
    {
        $this->queryService = $queryService;
    }

    public function index(Request $request)
    {
        $typeVc = $request->input('type_vc', 'V');
        $month = $request->input('month', date('n'));
        $year = $request->input('year', session('working_year', date('Y')));

        $documents = $this->queryService->getFilteredDocuments($typeVc, $month, $year);

        return view('accounting.document_query_results', compact('documents', 'typeVc', 'month', 'year'));
    }
}