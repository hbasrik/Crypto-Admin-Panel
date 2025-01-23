<link href="{{ secure_asset('css/dashboard.css') }}" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-left d-flex">Market</h1>
    <div id="dashboard">
        <div id="form_group_container" class="d-flex justify-content-between">
            <div id="form_group" class="form_drop">
                <label for="cryptoSearchInput">Search Cryptocurrency</label>
                <input type="text" id="cryptoSearchInput" class="form-control" placeholder="Search Here">
                <ul id="searchResults" class="dropdown-menu"></ul>
            </div>

            <div id="favorites_container" class="mb-3 form_drop d-flex flex-column justify-end">
                <label for="favoritesDropdown">Your Favorites</label>
                <select id="favoritesDropdown" class="form-control">
                    <option value="">Select Coin</option>
                </select>
            </div>
        </div>

        <div id="coinDetail">
            <div class="d-flex">
                <div>
                    <p id="coinSymbol" class="d-flex align-items-center"></p>
                </div>

                <div class="align-center">
                    <button id="favoriteBtn" class="favorite">
                        <span id="favoriteIcon" class="empty-star">★</span>
                    </button>
                </div>
            </div>
            <div id="coin_main_detail" class="d-flex">



                <div id="coin_main_detail_header" class="d-grid">
                    <div>
                        <h2 id="coinName"></h2>
                    </div>


                </div>


                <div id="coin_volume_div" class="d-flex">
                    <p id="coinVolume"></p>

                </div>

            </div>
            <div id="coin_main_detail_rest" class="d-grid align-items-center ">
                <p id="coinPrice"></p>
                <p id="coinChange1d"><span id="value1h"></span></p>
            </div>
            <div id="percentageLabels">
                <p id="coinChange1h">1H <span id="value1h"></span></p>
                <p id="coinChange24h">24H <span id="value24h"></span></p>
                <p id="coinChange7d">7D <span id="value7d"></span></p>
                <p id="coinChange30d">30D <span id="value30d"></span></p>
                <p id="coinChange90d">90D <span id="value90d"></span></p>
            </div>

            <div id="chart_container">
                <canvas id="coinChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    const cryptoData = JSON.parse('@json($cryptoData)');
    const favorites = JSON.parse(localStorage.getItem('favorites')) || [];
    const searchInput = document.getElementById('cryptoSearchInput');
    const searchResults = document.getElementById('searchResults');
    const favoritesDropdown = document.getElementById('favoritesDropdown');
    const coinDetail = document.getElementById('coinDetail');
    const favoriteBtn = document.getElementById('favoriteBtn');
    const favoriteIcon = document.getElementById('favoriteIcon');

    function updateFavoritesDropdown() {
        favoritesDropdown.innerHTML = `<option value="">Select Coin</option>`;
        favorites.forEach(fav => {
            favoritesDropdown.innerHTML += `<option value="${fav.id}">${fav.name} (${fav.symbol})</option>`;
        });
    }
    updateFavoritesDropdown();

    searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();

        const filteredResults = cryptoData.filter(coin =>
            coin.name.toLowerCase().includes(query) || coin.symbol.toLowerCase().includes(query)
        );

        if (filteredResults.length > 0 && query) {
            searchResults.style.display = 'block';
            searchResults.innerHTML = filteredResults
                .map(coin => `<li data-id="${coin.id}" data-name="${coin.name}" data-symbol="${coin.symbol}">${coin.name} (${coin.symbol})</li>`)
                .join('');
        } else {
            searchResults.style.display = 'none';
            searchResults.innerHTML = '';
        }
    });

    searchResults.addEventListener('click', function(event) {
        const target = event.target;

        if (target.tagName === 'LI') {
            const coinId = target.getAttribute('data-id');
            const selectedCoin = cryptoData.find(coin => coin.id == coinId);

            if (selectedCoin) {
                searchInput.value = '';
                searchResults.style.display = 'none';

                displayCoinDetails(selectedCoin);
            }
        }
    });

    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.style.display = 'none';
        }
    });

    favoriteBtn.addEventListener('click', function() {
        const coinId = favoriteBtn.getAttribute('data-id');
        const coinName = favoriteBtn.getAttribute('data-name');
        const coinSymbol = favoriteBtn.getAttribute('data-symbol');

        const existingIndex = favorites.findIndex(fav => fav.id === coinId);
        if (existingIndex !== -1) {
            favorites.splice(existingIndex, 1);
            favoriteIcon.classList.replace('filled-star', 'empty-star');
        } else {
            favorites.push({
                id: coinId,
                name: coinName,
                symbol: coinSymbol
            });
            favoriteIcon.classList.replace('empty-star', 'filled-star');
        }
        localStorage.setItem('favorites', JSON.stringify(favorites));
        updateFavoritesDropdown();
    });

    favoritesDropdown.addEventListener('change', function() {
        const coinId = this.value;
        const selectedCoin = cryptoData.find(coin => coin.id == coinId);

        if (selectedCoin) {
            displayCoinDetails(selectedCoin);

            if (favorites.some(fav => fav.id == selectedCoin.id)) {
                favoriteIcon.classList.replace('empty-star', 'filled-star');
            } else {
                favoriteIcon.classList.replace('filled-star', 'empty-star');
            }
        }
    });

    function displayCoinDetails(coin) {
        coinDetail.style.display = 'block';
        document.getElementById('coinName').innerText = coin.name;
        document.getElementById('coinSymbol').innerText = `${coin.symbol}/USD`;
        document.getElementById('coinPrice').innerText = `$${coin.price.toLocaleString()}`;
        document.getElementById('coinVolume').innerText = `Volume: $${coin.volume_24h.toLocaleString()}`;

        updatePercentageLabel('coinChange1h', coin.percent_change_1h);
        updatePercentageLabel('coinChange1d', coin.percent_change_24h);
        updatePercentageLabel('coinChange24h', coin.percent_change_24h);
        updatePercentageLabel('coinChange7d', coin.percent_change_7d);
        updatePercentageLabel('coinChange30d', coin.percent_change_30d);
        updatePercentageLabel('coinChange90d', coin.percent_change_90d);

        fetch(`/crypto/chart-data/${coin.id}`)
            .then(response => response.json())
            .then(data => {
                renderChart(data);
            });

        favoriteBtn.setAttribute('data-id', coin.id);
        favoriteBtn.setAttribute('data-name', coin.name);
        favoriteBtn.setAttribute('data-symbol', coin.symbol);

        favoriteBtn.style.display = 'inline-block';
        if (favorites.some(fav => fav.id == coin.id)) {
            favoriteIcon.classList.replace('empty-star', 'filled-star');
        } else {
            favoriteIcon.classList.replace('filled-star', 'empty-star');
        }
    }

    function updatePercentageLabel(id, percentage) {
        const element = document.getElementById(id);
        element.style.display = 'block';
        const span = element.querySelector('span');
        span.innerText = `${percentage.toFixed(2)}%`;
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
                    },
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Price (USD)',
                        },
                    },
                },
            },
        });
    }
</script>
@endsection