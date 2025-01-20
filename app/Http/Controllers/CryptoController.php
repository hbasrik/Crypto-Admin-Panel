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

        $cryptoNames = json_encode(array_column($cryptoData['data'], 'name'));
    $cryptoPrices = json_encode(array_map(function ($crypto) {
        return $crypto['quote']['USD']['price'];
    }, $cryptoData['data']));

    return view('crypto.index', [
        'cryptoNames' => $cryptoNames, 
        'cryptoPrices' => $cryptoPrices, 
    ]);
}
