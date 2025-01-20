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


            <canvas id="coinChart" width="400" height="200"></canvas>
        </div>
    </div>

    <script>
        const cryptoNames = @json($cryptoNames);
        const cryptoPrices = @json($cryptoPrices);

        const ctx = document.getElementById('cryptoChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: cryptoNames,
                datasets: [{
                    label: 'Crypto Prices (USD)',
                    data: cryptoPrices,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>