<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        validator(request()->all(), [

            'from_date' => ['date'],
            'to_date' => ['date'],
        ])->validate();
        $created_from = $request->input('from_date');
        $created_to = $request->input('to_date');

        DB::enableQueryLog();
        $orders = Order::query()
            ->when(
                $created_from,
                fn ($query) => $query->whereDate('order_date', '>=', $created_from)
            )
            ->when(
                $created_to,
                fn ($query) => $query->whereDate('order_date', '<=', $created_to)
            )

            ->when(
                $created_from && $created_to,
                fn ($query) => $query->whereBetween('order_date', [$created_from, $created_to])
            )
            ->with('user', 'products')

            // ->get()->count();
            // dd($orders, DB::getQueryLog());    //->toSql();
            ->paginate(10);
        // dd($orders);
        return view('creport', compact('orders'));
    }
    // public function calcSponserSales(User $user)
    // {
    //     $count = count($user->sponserSales);

    //     if ($count === 1) {
    //         //something
    //     }
    // }

    /**
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function topsellers()
    {
        $users = User::whereHas('categories', fn ($q) => $q
            ->where('name', "Distributor"))
            ->with('referredDistributors.orders.products')
            ->get();

        // dd($users);


        $users->map(function ($item) {
            if (isset($item->referredDistributors) && count($item->referredDistributors) > 0) {

                foreach ($item->referredDistributors as $referredDistributor) {

                    if (isset($referredDistributor->orders) && count($referredDistributor->orders) > 0)

                        foreach ($referredDistributor->orders as $order) {

                            if (isset($order->products) && count($order->products) > 0) {
                                $totalOrder = 0;
                                foreach ($order->products as $product) {
                                    //dd($product->pivot->quantity);
                                    $totalOrder += $product->price *  $product->pivot->quantity;
                                    // dd($totalOrder);
                                }
                                $item->totalSale += $totalOrder;

                                // dd($item->totalSale);
                            }
                        }
                }
            }
            //dd($item);
            return $item;
        });
        $sorted = $users->sortByDesc('totalSale');

        $ranked = $sorted->values()->all();


        //calc rank
        $rank = 1;
        for ($i = 0; $i <= count($ranked) - 1; $i++) {
            if ($i == count($ranked) - 1) {
                $ranked[$i]['rank'] = $rank;
                continue;
            }
            if ($ranked[$i]['totalSale'] == $ranked[$i + 1]['totalSale']) {
                $ranked[$i]['rank'] = $rank;
                $ranked[$i + 1]['rank'] = $rank;
                continue;
            }
            $ranked[$i]['rank'] = $rank;
            $rank++;
        }
        $topTwoHundredDistributorSales = collect($ranked)->take(200)->paginate(10);
        /// dd($topTwoHundredDistributorSales);

        return view('topsellers', compact('topTwoHundredDistributorSales'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id, Order $order)
    {
        $orderDetails = $order->getOrderDetails($id);

        // dd($orderDetails);

        return view('details', ['orderDetails' => $orderDetails]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
