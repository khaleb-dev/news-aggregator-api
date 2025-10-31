<?php

namespace App\Http\Services\Integrations;

use Illuminate\Support\Facades\Log;

/**
 * The Guardian API Integration
 * Documentation: https://open-platform.theguardian.com/documentation
 */
class TheGuardian extends BaseImpl
{
    protected $config;
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function fetchArticles(int $page = 1, int $count = 100)
    {
        $qs = 'api-key='.$this->config['api_key'].'&page-size='.$count.'&page='.$page;
        $res = $this->exec($this->prepUrl($this->config['url'], 'search?'.$qs), 'GET');

        return [
            'currentPage' => $res['response']['currentPage'] ?? $page,
            'totalPages' => $res['response']['pages'] ?? 0,
            'data' => $res['response']['results'] ?? [],
        ];
    }

}
