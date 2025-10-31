<?php

namespace App\Schedules;


use App\Http\Services\ArticleGatewayService;
use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SyncArticleFromSourcesSchedule {
    public function __invoke()
    {
        Log::info('==============Running ArticleSync Schedule: STARTED==============');
        $ds = new ArticleGatewayService('NewsApiAi');
        $res = $ds->fetchArticles();

        $this->storeInDatabase($res['data'], 'NewsApiAi');
        
        Log::info('==============Running ArticleSync Schedule: ENDED==============');
    }

    
    private function storeInDatabase(mixed $articles, string $datasource): bool
    {
        if (empty($articles)) {
            return true;
        }

        try {
            DB::beginTransaction();

            // Get all existing titles and published_at combinations in one query
            $existingCombinations = DB::table('articles')
                ->whereIn('title', array_column($articles, 'title'))
                ->pluck(DB::raw("CONCAT(title, '|', published_at)"))
                ->toArray();

            $existingSet = array_flip($existingCombinations);

            // Prepare bulk insert data
            $insertData = [];
            $now = now();

            foreach ($articles as $article) {
                // Format the API datetime to match database format for comparison
                $formattedDateTime = $article['dateTimePub'] 
                    ? Carbon::parse($article['dateTimePub'])->format('Y-m-d H:i:s') 
                    : null;
                
                $key = $article['title'] . '|' . $formattedDateTime;
                
                if (!isset($existingSet[$key])) {
                    $categoryLabels = '';
                    if (isset($article['category']) && is_array($article['category'])) {
                        $categoryLabels = implode(', ', array_column($article['category'], 'label'));
                    }
                    $insertData[] = [
                        'id' => Str::uuid()->toString(),
                        'source' => $datasource,
                        'title' => $article['title'] ?? 'No Title',
                        'content' => $article['body'] ?? 'No Content',
                        'url' => $article['url'] ?? null,
                        'image_url' => $article['image'] ?? null,
                        'published_at' => $article['dateTimePub'] ? Carbon::parse($article['dateTimePub']) : null,
                        'category' => $categoryLabels,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            // Bulk insert all new articles
            if (!empty($insertData)) {
                DB::table('articles')->insert($insertData);
                Log::info("Bulk inserted " . count($insertData) . " new articles from {$datasource}");
            } else {
                Log::info("No new articles to insert from {$datasource}");
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to bulk store articles from {$datasource}. Error: " . $e->getMessage());
            return false;
        }
    }
}
