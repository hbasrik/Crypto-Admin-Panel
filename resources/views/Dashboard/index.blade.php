<!DOCTYPE html>
<html>

<head>
    <title>Crypto Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <p id="coinPrice"></p>
            <p id="coinChange"></p>
            <p id="coinVolume"></p>


            <canvas id="coinChart" width="400" height="200"></canvas>
        </div>
    </div>

    <script>
        const cryptoData = JSON.parse('@json($cryptoData)');


        document.getElementById('cryptoSelect').addEventListener('change', function() {
            const coinId = this.value;


            const selectedCoin = cryptoData.find(coin => coin.id == coinId);

            if (selectedCoin) {

                document.getElementById('coinName').innerText = selectedCoin.name;
                document.getElementById('coinPrice').innerText = `Price: $${selectedCoin.quote.USD.price.toFixed(2)}`;
                document.getElementById('coinChange').innerText = `Change (24h): ${selectedCoin.quote.USD.percent_change_24h.toFixed(2)}%`;
                document.getElementById('coinVolume').innerText = `Volume (24h): ${selectedCoin.quote.USD.volume_24h.toFixed(2)}`;


                document.getElementById('coinDetail').style.display = 'block';


                fetch(`/crypto/chart-data/${coinId}`)
                    .then(response => response.json())
                    .then(data => {
                        renderChart(data);
                    })
                    .catch(error => console.error('Error fetching chart data:', error));
            } else {

                document.getElementById('coinDetail').style.display = 'none';
            }
        });


        function renderChart(chartData) {

            const ctx = document.getElementById('coinChart').getContext('2d');


            if (window.myChart) {
                window.myChart.destroy();
            }

            window.myChart = new Chart(ctx, {
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
                            beginAtZero: false,
                        },
                        x: {
                            type: 'time',
                            time: {
                                unit: 'hour',
                            },
                        },
                    },
                },
            });
        }
    </script>
</body>

</html>