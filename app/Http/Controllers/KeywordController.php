<?php

namespace App\Http\Controllers;

use App\Models\Keyword;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KeywordController extends Controller
{
    /**
     * Get keywords for autocomplete.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->input('query', '');
        
        if (empty($query)) {
            // Return recent keywords if no query
            $keywords = Keyword::latest()
                ->take(20)
                ->get(['id', 'content']);
        } else {
            // Search keywords
            $keywords = Keyword::search($query)
                ->take(20)
                ->get(['id', 'content']);
        }
        
        return response()->json($keywords);
    }
}
