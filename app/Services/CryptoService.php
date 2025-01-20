<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

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
                'limit' => 10, // Fetch top 10 cryptocurrencies
                'convert' => 'USD',
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getHistoricalData($id)
    {
        try {
            $response = $this->client->get('v1/cryptocurrency/quotes/historical', [
                'headers' => [
                    'X-CMC_PRO_API_KEY' => env('COINMARKETCAP_API_KEY'),
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'id' => $id,
                    'interval' => '5m',
                ],
            ]);

            Log::info('Historical Data API Response: ' . $response->getBody()->getContents());

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('Error fetching historical data: ' . $e->getMessage());

            return [];
        }
    }
}
