<?php

namespace App\Http\Controllers;

use App\Services\ProductSearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Handle search requests.
     *
     * @param Request $request
     * @param ProductSearchService $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, ProductSearchService $service)
    {
        $term = $request->input('q', '');
        // 5 similar + 1 primary = 6 results
        $limit = 6;
        $results = $service->search($term, $limit);
        return response()->json($results);
    }
}
?>
