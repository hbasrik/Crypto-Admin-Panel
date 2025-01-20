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

    public function getHistoricalData($coinId)
    {
        $response = $this->client->get("v1/cryptocurrency/quotes/historical", [
            'headers' => [
                'X-CMC_PRO_API_KEY' => env('COINMARKETCAP_API_KEY'),
                'Accept' => 'application/json',
            ],
            'query' => [
                'id' => $coinId,
                'interval' => '5m',
                'time_end' => time(),
                'time_start' => time() - (24 * 60 * 60),
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
