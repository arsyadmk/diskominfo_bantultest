<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/soal-1', function () {
    $ordersWithCustomerAndDetails = DB::table('orders')
        ->join('customers', 'orders.customer_id', '=', 'customers.customer_id')
        ->join('order_details', 'orders.order_id', '=', 'order_details.order_id')
        ->select('customers.company_name', 'customers.country', DB::raw('count(order_details.unit_price) as total_purchases'))
        ->groupBy('customers.customer_id')
        ->orderBy('total_purchases', 'desc')
        ->limit('10')
        ->get();

    // dd(json_decode($ordersWithCustomerAndDetails));
    echo "<pre>";
    print_r(json_decode($ordersWithCustomerAndDetails));
});

