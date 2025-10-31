<?php

namespace App\Http\Services\Integrations;

use Illuminate\Support\Facades\Log;

/**
 * NewsApi.org Integration
 * Documentation: https://newsapi.org/docs
 */
class NewsApiOrg extends BaseImpl
{
    protected $config;
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function fetchArticles(int $page = 1, int $count = 100)
    {
        $qs = 'apiKey='.$this->config['api_key'].'&sortBy=publishedAt&pageSize='.$count.'&page='.$page.'&q=technology';
        $res = $this->exec($this->prepUrl($this->config['url'], 'everything?'.$qs), 'GET');

        return [
            'currentPage' => $page,
            'totalPages' => $res['totalResults'] ?? 0,
            'data' => $res['articles'] ?? [],
        ];
    }

}
