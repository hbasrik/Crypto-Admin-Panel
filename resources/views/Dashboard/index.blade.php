<!DOCTYPE html>
<html>

<head>
    <title>Crypto Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Crypto Dashboard</h1>
        <div id="dashboard">
            <div id="form-group-container">
                <div id="form-group" class="mb-5">
                    <label for="cryptoSelect">Select Cryptocurrency</label>
                    <select id="cryptoSelect" class="form-control">
                        <option value="">Select a Coin</option>
                        @foreach ($cryptoData as $crypto)
                        <option value="{{ $crypto['id'] }}">{{ $crypto['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div id="coinDetail">
                <div id="coin_main_detail" class="d-flex ">
                    <div class="d-flex flex-column">
                        <div id="coin_main_detail_header" class="d-flex">
                            <h2 id="coinName"></h2>
                            <p id="coinSymbol" class="d-flex align-items-center border-2"></p>
                        </div>
                        <div id="coin_main_detail_rest" class="d-flex align-items-center">
                            <h4 id="coinPrice"></h4>
                            <p id="coinChange1d"><span id="value1h"></span></p>
                        </div>
                    </div>
                    <div id="coin_volume_div">
                        <p id="coinVolume"></p>
                    </div>
                </div>


                <div id="percentageLabels">
                    <p id="coinChange1h">1H: <span id="value1h">%</span></p>
                    <p id="coinChange24h">24H: <span id="value24h">%</span></p>
                    <p id="coinChange7d">7D: <span id="value7d">%</span></p>
                    <p id="coinChange30d">30D: <span id="value30d">%</span></p>
                    <p id="coinChange90d">90D: <span id="value90d">%</span></p>
                </div>

                <div id="chart-container">
                    <canvas id="coinChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const cryptoData = JSON.parse('@json($cryptoData)');

        document.getElementById('cryptoSelect').addEventListener('change', function() {
            const coinId = this.value;
            const selectedCoin = cryptoData.find(coin => coin.id == coinId);

            if (selectedCoin) {
                document.getElementById('coinDetail').style.display = 'block';
                document.getElementById('coinName').innerText = selectedCoin.name;
                document.getElementById('coinSymbol').innerText = `${selectedCoin.symbol}/USD`;
                document.getElementById('coinPrice').innerText = `$${selectedCoin.price.toLocaleString()}`;
                document.getElementById('coinVolume').innerText = `Volume: $${selectedCoin.volume_24h.toLocaleString()}`;


                updatePercentageLabel('coinChange1h', selectedCoin.percent_change_1h);
                updatePercentageLabel('coinChange1d', selectedCoin.percent_change_24h);
                updatePercentageLabel('coinChange24h', selectedCoin.percent_change_24h);
                updatePercentageLabel('coinChange7d', selectedCoin.percent_change_7d);
                updatePercentageLabel('coinChange30d', selectedCoin.percent_change_30d);
                updatePercentageLabel('coinChange90d', selectedCoin.percent_change_90d);

                fetch(`/crypto/chart-data/${coinId}`)
                    .then(response => response.json())
                    .then(data => {
                        renderChart(data);
                    });
            } else {
                document.getElementById('coinDetail').style.display = 'none';
            }
        });


        function updatePercentageLabel(id, percentage) {
            const element = document.getElementById(id);
            element.style.display = 'block';
            const span = element.querySelector('span');
            span.innerText = `${percentage.toFixed(2)}%`;
            // span.style.color = percentage > 0 ? 'green' : percentage < 0 ? 'red' : 'black';
            span.style.backgroundColor = percentage > 0 ? 'green' : percentage < 0 ? 'red' : 'gray';
            span.style.color = 'white';
            span.style.padding = '5px';
            span.style.borderRadius = '5px';
        }

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
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Time (Last 24 Hours)',
                            },
                            ticks: {
                                maxRotation: 0,
                                autoSkip: true,
                            },
                        },
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Price (USD)',
                            },
                        },
                    },
                }
            });
        }
    </script>
</body>

</html>