<!DOCTYPE html>
<html>

<head>
    <title>Crypto Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Crypto Dashboard</h1>


        <div class="form-group">
            <label for="cryptoSelect">Select Cryptocurrency</label>
            <select id="cryptoSelect" class="form-control">
                <option value="">Select a Coin</option>
                @foreach ($cryptoData as $crypto)
                <option value="{{ $crypto['id'] }}">{{ $crypto['name'] }}</option>
                @endforeach
            </select>
        </div>


        <div id="coinDetail" class="mt-4">
            <h2 id="coinName"></h2>
            <p id="coinSymbol"></p>
            <p id="coinPrice"></p>
            <p id="coinChange1h"></p>
            <p id="coinChange24h"></p>
            <p id="coinChange7d"></p>
            <p id="coinChange30d"></p>
            <p id="coinChange90d"></p>
            <p id="coinVolume"></p>


            <canvas id="coinChart" width="400" height="200"></canvas>
        </div>
    </div>

    <script>
        const cryptoData = JSON.parse('@json($cryptoData)');

        console.log(cryptoData);
        document.getElementById('cryptoSelect').addEventListener('change', function() {
            const coinId = this.value;
            const selectedCoin = cryptoData.find(coin => coin.id == coinId);

            if (selectedCoin) {
                document.getElementById('coinName').innerText = selectedCoin.name;
                document.getElementById('coinSymbol').innerText = `Symbol: ${selectedCoin.symbol}`;
                document.getElementById('coinPrice').innerText = `Price: $${selectedCoin.price.toFixed(2)}`;
                document.getElementById('coinChange1h').innerText = `Change (1h): ${selectedCoin.percent_change_1h.toFixed(2)}%`;
                document.getElementById('coinChange24h').innerText = `Change (24h): ${selectedCoin.percent_change_24h.toFixed(2)}%`;
                document.getElementById('coinChange7d').innerText = `Change (7d): ${selectedCoin.percent_change_7d.toFixed(2)}%`;
                document.getElementById('coinChange30d').innerText = `Change (30d): ${selectedCoin.percent_change_30d.toFixed(2)}%`;
                document.getElementById('coinChange90d').innerText = `Change (90d): ${selectedCoin.percent_change_90d.toFixed(2)}%`;
                document.getElementById('coinVolume').innerText = `Volume (24h): ${selectedCoin.volume_24h.toFixed(2)}`;


                fetch(`/crypto/chart-data/${coinId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log(data)
                        renderChart(data);
                    });
            } else {
                document.getElementById('coinDetail').style.display = 'none';
            }
        });



        let chartInstance;

        function renderChart(chartData) {


            const ctx = document.getElementById('coinChart').getContext('2d');

            if (chartInstance) {
                chartInstance.destroy();
            }

            chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.timestamps,
                    datasets: [{
                        label: 'Price (USD)',
                        data: chartData.prices,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        fill: true,
                    }],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });
        }
    </script>
</body>

</html>