<?php

namespace App\Services\V1\Website\Product;

use GuzzleHttp\Client;

class SearchService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function search($term)
    {
        $link = $this->makeAlgoliaLink();
    }

    private function makeAlgoliaLink()
    {
        $app_id = config('services.algolia.app_id');
        return "https://{$app_id}.algolia.net";
    }

    private function makeAlgoliaHeaders()
    {
        $secret = config('services.algolia.secret');
        return [
            'X-Algolia-API-Key' => $secret,
            'X-Algolia-Application-Id' => config('services.algolia.app_id'),
        ];
    }

    private function makeAlgoliaClient()
    {
        $client = new Client([
            'base_uri' => $this->makeAlgoliaLink(),
            'headers' => $this->makeAlgoliaHeaders(),
        ]);
        return $client;
    }
}
