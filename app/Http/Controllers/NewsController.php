<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Fetch News Articles
     * 
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request): JsonResponse
    {     
        return response()->json([
            'status' => 'success',
            'message' => 'Data Retrieved successfully.',
            'data' => []
        ], 200);
    }
   
}
