<?php

namespace App\Http\Services;


use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class ArticleGatewayService {
    
    protected $service;

    public function __construct(string $provider)
    {
        $providers = ['NewsApiAi', 'NewsApiOrg', 'TheGuardian'];

        if (!in_array($provider, $providers, true)) {
            throw new InvalidArgumentException("Invalid provider: {$provider}");
        }

        switch ($provider) {
            case 'NewsApiAi':
                $config = [
                    'url' => env('NEWSAPI_AI_BASE_URL'),
                    'api_key' => env('NEWSAPI_AI_API_KEY')
                ];
                break;

            case 'NewsApiOrg':
                $config = [
                    'url' => env('NEWSAPI_ORG_BASE_URL'),
                    'api_key' => env('NEWSAPI_ORG_API_KEY')
                ];
                break;

            case 'TheGuardian':
                $config = [
                    'url' => env('THEGUARDIAN_BASE_URL'),
                    'api_key' => env('THEGUARDIAN_API_KEY')
                ];
                break;

            default:
                throw new InvalidArgumentException("Invalid provider: {$provider}");
                break;
        }

        // Dynamically initialize the appropriate service class
        $serviceClass = __NAMESPACE__ . '\\Integrations\\' . $provider;

        if (!class_exists($serviceClass)) {
            throw new InvalidArgumentException("Service class {$serviceClass} not found.");
        }

        $this->service = new $serviceClass($config);
    }


    /**
     * Fetch Articles
     */
    public function fetchArticles(int $page = 1, int $count = 100)
    {
        return $this->service->fetchArticles($page, $count);
    }
    
}
