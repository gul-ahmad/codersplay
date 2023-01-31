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
            'distributor_id' => ['integer'],
            'first_name' => ['string'],
            'last_name' => ['string'],
        ])->validate();

        //Validating From and To dates if both exist at the same time
        if (($request->input('from_date')) && ($request->input('to_date'))) {

            validator(request()->all(), [

                'from_date' => ['date', 'required_with:to_date'],
                'to_date' => ['date', 'required_with:from_date', 'after:from_date'],
            ])->validate();
        }
        $created_from = $request->input('from_date');
        $created_to = $request->input('to_date');
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $distributor_id = $request->input('distributor_id');

        DB::enableQueryLog();
        // $orders = Order::whereHas(
        //     'user.referredDistributors',
        //     fn ($query) => $query
        //         ->where('first_name', 'like', '%' . $first_name . '%')
        // )->get()->count();
        // dd($orders);

        // $distributorByName = Order::with('user.referredDistributors')->get()->count();
        // dd($distributorByName);


        // $orderNew = User::with('referredDistributors')->where('id', 30451)->first();

        // return $orderNew;


        // $orderNewHere = Order::whereHas('user.referrer.categories', function ($query) {

        //     $query->where('id', 1);
        // })
        //     ->whereHas('user.referrer', function ($query) use ($first_name) {
        //         $query->where('first_name', 'like', '%' . $first_name . '%');
        //     })
        //     // ->where('id', 224613)
        //     ->get();

        // return $orderNewHere;


        // $userReferer =User::with('referrer')
        //         ->where('id',10082)
        //         ->get();


        // $OrderReferer = Order::with('user.referrer.referrals')
        //     ->where('id', 217511)
        //     ->get();


        // return $OrderReferer;



        $orders = Order::query()
            ->when(
                $created_from,
                fn ($query) => $query->whereDate('order_date', '>=', $created_from)
                //Mysql Query
                //SELECT * FROM orders
                //WHERE DATE(order_date) >= '2020-05-19'
            )
            ->when(
                $created_to,
                fn ($query) => $query->whereDate('order_date', '<=', $created_to)

                //Mysql Query
                //SELECT * FROM orders
                //WHERE DATE(order_date) <= '2020-05-19'
            )

            ->when(
                $created_from && $created_to,
                fn ($query) => $query->whereBetween('order_date', [$created_from, $created_to])
                //SELECT * FROM `orders` 
                //WHERE  (order_date BETWEEN '2020-02-22' AND '2020-02-23')
            )
            ->when(
                $first_name || $last_name || $distributor_id,
                function ($query) use ($first_name, $last_name, $distributor_id) {
                    $query->whereHas('user.referrer.categories', function ($query) {

                        $query->where('id', 1);
                    })
                        ->whereHas('user.referrer', function ($query) use ($first_name, $last_name, $distributor_id) {
                            $query->where('first_name', 'like', '%' . $first_name . '%')
                                ->orWhere('last_name', 'like', '%' . $last_name . '%')
                                ->orWhere('id', $distributor_id);
                        });
                }

            )
            ->with('user', 'products')


            ->get() //->count();
            // dd($orders, DB::getQueryLog());    //->toSql();
            ->paginate(10);
        // dd($orders);
        $orders->transform(function ($item) {



            //get totalOrder
            if (isset($item->products) && count($item->products) > 0) {
                $totalOrder = floatval(0);
                foreach ($item->products as $product) {
                    $totalOrder += floatval($product->price) * (int)$product->pivot->quantity;
                }
                $item->totalOrder = $totalOrder;
            }


            //get referredDistributors number
            //  dd($item->user->referrer->referrals->whereHas('user.referrer.categories'), function ($query) {

            //     $query->where('id', 1);
            // });
            if (isset($item->user->referrer->referrals) && count($item->user->referrer->referrals) > 0) {


                $item->user->referredDistributorsCount = count($item->user->referrer->referrals);
            } else {
                $item->user->referredDistributorsCount = 0;
            }



            //get percentage and commission
            $referrerCount = $item->user->referredDistributorsCount;
            if ($referrerCount >= 0 && $referrerCount < 5) {
                $item->percentage = Order::PERCENTAGE_IF_ZERO_REFERRERS;
            } elseif ($referrerCount >= 5 && $referrerCount < 11) {
                $item->percentage = Order::PERCENTAGE_IF_FROM_FIVE_TO_TEN_REFERRERS;
            } elseif ($referrerCount >= 11 && $referrerCount < 21) {
                $item->percentage = Order::PERCENTAGE_IF_FROM_ELEVEN_TO_TWENTY_REFERRERS;
            } elseif ($referrerCount >= 21 && $referrerCount < 31) {
                $item->percentage = Order::PERCENTAGE_IF_FROM_TWENTY_ONE_TO_THIRTY_REFERRERS;
            } elseif ($referrerCount > 31) {
                $item->percentage = Order::PERCENTAGE_IF_FROM_THIRTY_ONE_AND_ABOVE_REFERRERS;
            } else {
                $item->percentage = 0;
            }

            $item->commission = floatval($item->totalOrder) * (int)$item->percentage / 100;

            return $item;
        });
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
