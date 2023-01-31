<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Commission Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        {{-- <form method="POST" action=""> --}}
            <div>

                <div class="float-left w-100">
                    <label for="distributor">Distributor</label>
                    <input autocomplete="name" id="distributor" name="distributor" type="text"
                        class="form-control rounded w-50" placeholder="Distributor First Name" aria-label="Search"
                        aria-describedby="search-addon" />

                    @error('distributor')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                </div><br>
                <div class="float-left w-50">
                    <div class="float-left w-50">
                        <label for="from">From</label>
                        <input id="from_date" name="from_date" type="date" class="form-control rounded"
                            aria-label="Search" aria-describedby="search-addon" />
                        @error('from_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="float-left w-50">
                        <label for="to">To</label>
                        <input type="date" id="to_date" class="form-control rounded" aria-label="Search"
                            aria-describedby="search-addon" />
                        @error('to_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="clearfix"></div><br>
                <button type="submit" class="btn btn-outline-primary">Filter</button>
                {{-- <button class="btn btn-outline-danger">Clear Filter</button> --}}
        {{-- </form> --}}
    </div>
    <table class="table table-bordered mb-5">
        <thead>
            <tr class="table-success">

                <th scope="col">Invoice</th>
                <th scope="col">Purchaser</th>
                <th scope="col">Referer(Distributor)</th>
                <th scope="col">No of Referers(Distributor)</th>
                <th scope="col">Percentage</th>
                <th scope="col">Order Total</th>
                <th scope="col">Order Date</th>
                <th scope="col">Commission</th>
                <th scope="col">Action</th>


            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>

                    <td>{{ $order->invoice_number }}</td>
                    <td>{{ $order->user->username }}</td>
                    <td>{{ $order->user->referrer?->username . '--' . $order->user->referrer?->id }}</td>
                    {{-- <td>{{ $order->user->referrals->count() ?? '0' }}</td> --}}
                    <td>{{ $order->user->referredDistributorsCount }}</td>
                    {{-- <td>
                        @foreach ($order->user->groupby('referred_by') as $key => $value)
                            <td> {{ count($value) }}</td>
                        @endforeach
                        </td> --}}

                    {{-- @if ($order->user->referrals->count() > 0 && $order->user->referrals->count() < 5)
                            <td>5%</td>
                        @elseif($order->user->referrals->count() > 5 && $order->user->referrals->count() < 10)
                            <td>10%</td>
                        @elseif($order->user->referrals->count() > 11 && $order->user->referrals->count() < 20)
                            <td>15%</td>
                        @elseif($order->user->referrals->count() > 21 && $order->user->referrals->count() < 29)
                            <td>20%</td>
                        @elseif($order->user->referrals->count() > 30)
                            <td>30%</td>
                        @else
                            <td>0</td>
                        @endif --}}

                    <td>{{ $order->percentage }}%</td>

                    {{-- <td>{{ $order->user->referrals->count() ?? '0' }}</td> --}}
                    {{-- <?php// $total = 0; //$commission=0; ?> ?> ?> ?> ?> ?> ?> ?> ?>
                        @foreach ($order->products as $product)
                            <?php //$total += $product->pivot->quantity * $product['price'];
                            //  $commission =floatval($total)*
                            ?>
                        @endforeach --}}
                    <td>{{ $order->totalOrder }}</td>
                    {{-- <td>{{ $order->products }}</td> --}}

                    <td>{{ $order->order_date }}</td>
                    <td>{{ $order->commission }}</td>

                    <td><a class="" href=" {{ route('view.details', ['id' => $order->id]) }}">View</a></td>

                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- Pagination --}}
    <div class="d-flex justify-content-center">
        {!! $orders->links() !!}
    </div>
    </div>
</body>

</html>
