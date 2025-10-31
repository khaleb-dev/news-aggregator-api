<?php

namespace App\Schedules;


use App\Http\Services\ArticleGatewayService;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

class SyncArticleFromSourcesSchedule {
    public function __invoke()
    {
        Log::info('==============Running ArticleSync Schedule: STARTED==============');
        $ds = new ArticleGatewayService('NewsApiAi');
        $res = $ds->fetchArticles();

        Log::info($res);
        
        Log::info('==============Running ArticleSync Schedule: ENDED==============');
    }

    
    private function storeInDatabase(mixed $articles, string $datasource): bool
    {
        
        return true;
    }
}
