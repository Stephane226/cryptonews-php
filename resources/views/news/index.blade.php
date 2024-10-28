<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}"> <!-- Link to the new stylesheet -->

</head>
<body>
    <div class="container mt-5">
        <h3>News Articles</h3>

        <form method="GET" action="{{ route('news.filter.index') }}" id="filter-form">
            <select name="coin" class="form-control form-elem">
                      <option value="Bitcoin" {{ request('coin') == 'Bitcoin' ? 'selected' : '' }}>Bitcoin</option>
        <option value="Ethereum" {{ request('coin') == 'Ethereum' ? 'selected' : '' }}>Ethereum</option>
        <option value="Ripple" {{ request('coin') == 'Ripple' ? 'selected' : '' }}>Ripple (XRP)</option>
        <option value="Litecoin" {{ request('coin') == 'Litecoin' ? 'selected' : '' }}>Litecoin (LTC)</option>
        <option value="Cardano" {{ request('coin') == 'Cardano' ? 'selected' : '' }}>Cardano (ADA)</option>
        <option value="Polkadot" {{ request('coin') == 'Polkadot' ? 'selected' : '' }}>Polkadot (DOT)</option>
        <option value="Chainlink" {{ request('coin') == 'Chainlink' ? 'selected' : '' }}>Chainlink (LINK)</option>
        <option value="Binance Coin" {{ request('coin') == 'Binance Coin' ? 'selected' : '' }}>Binance Coin (BNB)</option>
        <option value="Dogecoin" {{ request('coin') == 'Dogecoin' ? 'selected' : '' }}>Dogecoin (DOGE)</option>
        <option value="Solana" {{ request('coin') == 'Solana' ? 'selected' : '' }}>Solana (SOL)</option>
        <option value="Polygon" {{ request('coin') == 'Polygon' ? 'selected' : '' }}>Polygon (MATIC)</option>
        <option value="Tron" {{ request('coin') == 'Tron' ? 'selected' : '' }}>Tron (TRX)</option>
        <option value="Stellar" {{ request('coin') == 'Stellar' ? 'selected' : '' }}>Stellar (XLM)</option>
        
            </select>
            <input type="date" name="start_date" class="form-control form-elem" value="{{ request('start_date') }}">
            <input type="date" name="end_date" class="form-control form-elem" value="{{ request('end_date') }}">
            <button type="submit" class="btn btn-primary mt-2 filter-btn">Filter</button>
        </form>

        <table id="news-table" class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Title</th>
                    <th>Published</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($news as $index => $newsItem)
                <tr class="news-row" 
                    data-title="{{ $newsItem['title'] }}" 
                    data-coin="{{ $newsItem['coin'] ?? 'N/A' }}" 
                    data-published="{{ \Carbon\Carbon::parse($newsItem['published_at'])->format('Y-m-d H:i:s') }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $newsItem['title'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($newsItem['published_at'])->format('Y-m-d H:i:s') }}</td>
                   
                </tr>
                @empty
                <tr>
                    <td colspan="4">No news available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="newsModal" tabindex="-1" role="dialog" aria-labelledby="newsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newsModalLabel">News Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Title:</strong> <span id="modal-title"></span></p>
                    <p><strong>Coin:</strong> <span id="modal-coin"></span></p>
                    <p><strong>Published At:</strong> <span id="modal-published"></span></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            // Use event delegation to handle click events on dynamically added rows
            $('#news-table').on('click', '.news-row', function() {
                // Get data from the clicked row
                const title = $(this).data('title');
                const coin = $(this).data('coin');
                const published = $(this).data('published');

                // Set the data in the modal
                $('#modal-title').text(title);
                $('#modal-coin').text(coin);
                $('#modal-published').text(published);

                // Show the modal
                $('#newsModal').modal('show');
            });

            // Fetch news every minute
            function fetchNews() {
                $.get('/news', function(data) {
                    $('#news-table tbody').empty(); // Clear existing rows

                    // Sort and display the data
                    data.sort((a, b) => new Date(b.published_at) - new Date(a.published_at));
                    $.each(data, function(index, newsItem) {
                        $('#news-table tbody').append(
                            '<tr class="news-row" data-title="' + newsItem.title + '" data-coin="' + (newsItem.coin || 'N/A') + '" data-published="' + new Date(newsItem.published_at).toLocaleString() + '">' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + newsItem.title + '</td>' +
                            '<td>' + new Date(newsItem.published_at).toLocaleString() + '</td>' +
                            '</tr>'
                        );
                    });
                }).fail(function() {
                    console.error("Error fetching news.");
                });
            }

            // Initial fetch
            fetchNews();
            setInterval(fetchNews, 60000);
        });
    </script>
</body>
</html>
