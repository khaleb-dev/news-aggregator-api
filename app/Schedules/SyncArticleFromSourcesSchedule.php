<?php

namespace App\Schedules;


use App\Http\Services\ArticleGatewayService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SyncArticleFromSourcesSchedule {
    public function __invoke()
    {
        Log::info('==============Running ArticleSync Schedule: STARTED==============');
        $newsSources = ['NewsApiAi', 'NewsApiOrg', 'TheGuardian'];

        foreach ($newsSources as $provider) {
            Log::info("Fetching articles from {$provider}");
            
            $ds = new ArticleGatewayService($provider);

            // I am limiting to first page with total of 100 records on each call for demo;
            $page = 1;
            $count = 100;
            $res = $ds->fetchArticles($page, $count);

            $this->storeInDatabase($res['data'], $provider);
        }
        
        Log::info('==============Running ArticleSync Schedule: ENDED==============');
    }

    
    private function storeInDatabase(mixed $articles, string $datasource): bool
    {
        if (empty($articles)) {
            Log::info('=********==No Data found in ' . $datasource . '==********=');
            return true;
        }

        try {
            Log::info('==============Storing data from ' . $datasource . '==============');

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
                // Use the same field logic for both comparison and insertion
                $dateField = $article['dateTimePub'] ?? $article['publishedAt'] ?? $article['webPublicationDate'] ?? null;
                
                $formattedDateTime = $dateField 
                    ? Carbon::parse($dateField)->format('Y-m-d H:i:s') 
                    : null;
                $title = $article['title'] ?? $article['webTitle'] ?? 'No Title';
                $key = $title . '|' . $formattedDateTime;
                
                if (!isset($existingSet[$key])) {
                    $categoryLabels = '';
                    if (isset($article['category']) && is_array($article['category'])) {
                        $categoryLabels = implode(', ', array_column($article['category'], 'label'));
                    }

                    $authors = '';
                    if (isset($article['authors']) && is_array($article['authors'])) {
                        $authors = implode(', ', array_column($article['authors'], 'name'));
                    } else {
                        $authors = $article['author'] ?? 'Unknown';
                    }

                    $insertData[] = [
                        'id' => Str::uuid()->toString(),
                        'source' => $datasource,
                        'title' => $title,
                        'content' => $article['body'] ?? $article['content'] ?? 'No Content',
                        'url' => $article['url'] ?? $article['webUrl'] ?? null,
                        'image_url' => $article['image'] ?? $article['urlToImage'] ?? null,
                        'published_at' => $dateField ? Carbon::parse($dateField) : null,
                        'category' => $categoryLabels,
                        'authors' => $authors ?? 'Unknown',
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
