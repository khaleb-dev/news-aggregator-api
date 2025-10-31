<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Fetch Articles
     * 
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'nullable|string',
            'date' => 'nullable|date',
            'source' => 'nullable|string|in:NewsApiAi,NewsApiOrg,TheGuardian',
            // the total number of items to be fetched per page, default is 10
            'pageSize' => 'nullable|integer|min:1|max:100',
            // the page number to be fetched, default is 1
            'page' => 'nullable|integer|min:1',
        ]);

        $query = Article::query();

        if ($request->has('date')) {
            $query->whereDate('published_at', $request->date);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('source')) {
            $query->where('source', $request->source);
        }
        
        $perPage = $request->input('pageSize', 10);
        $page = $request->input('page', 1);
        $data = $query->orderByDesc('published_at')->paginate($perPage, ['*'], 'page', $page);


        return response()->json([
            'status' => 'success',
            'message' => 'Data Retrieved successfully.',
            'data' => $data
        ], 200);
    }
   
}
