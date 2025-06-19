<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 3);
        $page = $request->input('page', 1);

        $article = Article::paginate($perPage, ['*'], 'page', $page);

        return $this->success([
            'article' => $article->items(),
            'pagination' => [
                'current_page' => $article->currentPage(),
                'has_next' => $article->hasMorePages(),
                'has_previous' => $article->currentPage() > 1,
                'per_page' => $article->perPage(),
                'total' => $article->total(),
                'last_page' => $article->lastPage(),
            ]
        ]);
    }
}
