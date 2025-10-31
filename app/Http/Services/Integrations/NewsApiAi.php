<?php

namespace App\Http\Services\Integrations;


use Illuminate\Support\Facades\Log;

/**
 * NewsApi.ai Integration
 * Documentation: https://newsapi.ai/documentation
 */
class NewsApiAi extends BaseImpl
{
    protected $config;
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function fetchArticles(int $page = 1, int $count = 100)
    {
        $payload = [
            'apiKey' => $this->config['api_key'],
            'action' => 'getArticles',
            'sourceLocationUri' => [
                'http://en.wikipedia.org/wiki/Germany',
                'http://en.wikipedia.org/wiki/United_States',
                'http://en.wikipedia.org/wiki/Canada',
                'http://en.wikipedia.org/wiki/United_Kingdom'
            ],
            'resultType' => 'articles',
            // 'dataType' => ['news', 'pr'],
            'includeArticleCategories' => true,
            'includeArticleImage' => true,
            'articlesPage' => $page,
            'articlesCount' => $count,
            'articlesSortBy' => 'date',
            'articlesSortByAsc' => false,
        ];
        
        $res = $this->exec($this->prepUrl($this->config['url'], 'article/getArticles'), 'POST', $payload);

        return [
            'currentPage' => $res['articles']['page'],
            'totalPages' => $res['articles']['pages'],
            'data' => $res['articles']['results'] ?? [],
        ];
    }

}
