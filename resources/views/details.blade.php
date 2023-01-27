<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>More Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-success">

                    <th scope="col">SKU</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Total</th>


                </tr>
            </thead>
            <tbody>
               @foreach ($orderDetails->products as $orderDetail) 
                <tr>
                    {{-- @foreach ($orderDetail->products as $product)  --}}
                    <td>{{ $orderDetail['sku'] }}</td>
                    <td>{{ $orderDetail['name'] }}</td>
                    <td>{{ $orderDetail['price'] }}</td>
                    <td>{{ $orderDetail->pivot->quantity }}</td>
                    <td>{{ ($orderDetail->pivot->quantity)*($orderDetail['price']) }}</td>
                    {{-- <td>{{ $orderDetails->products->name ?? '' }}</td>
                    <td>{{ $orderDetails->products->price ?? '' }}</td>
                    <td>{{ $orderDetails->products->sum('pivot.quantity') ?? '' }}</td>
                    <td>{{ $orderDetails->products->sum('pivot.price') ?? '' }}</td> --}}
                    {{-- @endforeach --}}


                </tr>
                 @endforeach 
            </tbody>
        </table>
        {{-- Pagination --}}
        <div class="d-flex justify-content-center">
            {{-- {!! $orders->links() !!} --}}
        </div>
    </div>
</body>

</html>
