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
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request): JsonResponse
    {     
        $data = Article::find();

        return response()->json([
            'status' => 'success',
            'message' => 'Data Retrieved successfully.',
            'data' => $data
        ], 200);
    }
   
}
