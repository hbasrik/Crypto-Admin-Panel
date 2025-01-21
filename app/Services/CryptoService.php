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

        $endTime = strtotime('now');
        $endTime = strtotime(date('Y-m-d H:00:00', $endTime));

        $startTime = $endTime - (24 * 60 * 60);
        $startTime = strtotime(date('Y-m-d H:00:00', $startTime));

        $response = $this->client->get("v1/cryptocurrency/quotes/historical", [
            'headers' => [
                'X-CMC_PRO_API_KEY' => env('COINMARKETCAP_API_KEY'),
                'Accept' => 'application/json',
            ],
            'query' => [
                'id' => $id,
                'interval' => '30m',
                'time_start' => date('Y-m-d\TH:i:s', $startTime),
                'time_end' => date('Y-m-d\TH:i:s', $endTime),
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['data']['quotes'] ?? [];
    }
}
