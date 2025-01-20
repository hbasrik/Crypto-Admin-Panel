<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\CryptoService;

class CryptoController extends Controller
{
    protected $cryptoService;

    public function __construct(CryptoService $cryptoService)
    {
        $this->cryptoService = $cryptoService;
    }

    public function index()
    {

        $cryptoData = $this->cryptoService->getCryptoData();

        $cryptoProcessedData = array_map(function ($crypto) {
            return [
                'id' => $crypto['id'],
                'name' => $crypto['name'],
                'price' => $crypto['quote']['USD']['price'],

            ];
        }, $cryptoData['data']);

        return view('dashboard.index', [
            'cryptoData' => $cryptoProcessedData,
        ]);
    }

    public function getChartData($id)
    {
        $response = $this->cryptoService->getHistoricalData($id);

        // Parse response for chart data
        $timestamps = array_map(function ($data) {
            return date('H:i', strtotime($data['time']));
        }, $response['data']['quotes']);

        $prices = array_map(function ($data) {
            return $data['quote']['USD']['price'];
        }, $response['data']['quotes']);

        return response()->json([
            'timestamps' => $timestamps,
            'prices' => $prices,
        ]);
    }
}
