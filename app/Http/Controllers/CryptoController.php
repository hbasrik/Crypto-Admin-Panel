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

        $cryptoNames = array_column($cryptoData['data'], 'name');
        $cryptoPrices = array_map(function ($crypto) {
            return $crypto['quote']['USD']['price'];
        }, $cryptoData['data']);


        return view('dashboard.index', [
            'cryptoData' => $cryptoData['data'],
            'cryptoNames' => $cryptoNames,
            'cryptoPrices' => $cryptoPrices,
        ]);
    }

    public function getChartData($id)
    {
        $historicalData = $this->cryptoService->getHistoricalData($id);

        
        $timestamps = [];
        $prices = [];
        foreach ($historicalData as $point) {
            $timestamps[] = date('H:i', strtotime($point['time'])); 
            $prices[] = $point['price']; 
        }

        return response()->json([
            'timestamps' => $timestamps,
            'prices' => $prices,
        ]);
}
