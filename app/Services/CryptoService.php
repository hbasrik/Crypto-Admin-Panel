<?php

namespace App\Services;

use GuzzleHttp\Client;

class CryptoService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://pro-api.coinmarketcap.com/',
            'verify' => false,
        ]);
    }

    public function getCryptoData()
    {
        $response = $this->client->get('v1/cryptocurrency/listings/latest', [
            'headers' => [
                'X-CMC_PRO_API_KEY' => env('COINMARKETCAP_API_KEY'),
                'Accept' => 'application/json',
            ],
            'query' => [
                'limit' => 100,
                'convert' => 'USD',
            ],

        ]);

        return json_decode($response->getBody(), true);
    }

    public function getHistoricalData($id)
    {
        $response = $this->client->get("v1/cryptocurrency/quotes/historical", [
            'headers' => [
                'X-CMC_PRO_API_KEY' => env('COINMARKETCAP_API_KEY'),
                'Accept' => 'application/json',
            ],
            'query' => [
                'id' => $id,
                'interval' => '5m',
            ],
        ]);

        return json_decode($response->getBody(), true)['data']['quotes'];
    }
}
