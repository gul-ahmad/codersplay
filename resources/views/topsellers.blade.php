<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Top 200 Distributors</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Top</th>
                            <th scope="col">Distributor Name</th>
                            <th scope="col">Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($topTwoHundredDistributorSales as $key => $topTwoHundredDistributorSale)
                            <tr>
                                <td>{{ $topTwoHundredDistributorSale->rank }}</td>
                                <td>{{ $topTwoHundredDistributorSale->first_name . ' ' . $topTwoHundredDistributorSale->last_name }}
                                </td>
                                <td>{{ $topTwoHundredDistributorSale->totalSale }}</td>
                            </tr>
                        @empty
                            <h4 style="color: grey;margin-top: 30px">No result</h4>
                        @endforelse
                    </tbody>
                </table>
                {{ $topTwoHundredDistributorSales->links('pagination::bootstrap-4') }}

            </div>
        </div>
    </div>
</body>

</html>
