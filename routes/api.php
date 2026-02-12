<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//http://diskominfo-bantultest.test/api/products?customer_id=a
Route::get('/products/{category_id?}', function (Request $request) {
    

    try {
        $validatedData = $request->validate([
            'category_id' => '',
            'min_price' => '',
            'max_price' => '',
            'page' => '',
            'limit' => '',
            'sort' => '',
        ]);
        
        $maxResults = $request->input('limit', 999999999);
        $minPrice = $request->input('min_price', 0);
        $maxPrice = $request->input('max_price', 999999999);
        $categoryId = $request->input('category_id');
        $supplierId = $request->input('supplier_id');
        // $supplierId = $request->input('supplier_id');

        $data_raw = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.category_id')
            ->join('suppliers', 'products.supplier_id', '=', 'suppliers.supplier_id')
            ->select('products.product_id', 'products.product_name', 'products.supplier_id', 'products.category_id', 'products.unit_price', 'products.units_in_stock', 'categories.category_name', DB::raw('suppliers.company_name as supplier_name'))
            ->when($minPrice && $maxPrice, function ($query) use ($minPrice, $maxPrice) {
                return $query->whereBetween('products.unit_price', [$minPrice, $maxPrice]);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                return $query->where('products.category_id', [$categoryId]);
            })
            ->when($supplierId, function ($query) use ($supplierId) {
                return $query->where('products.supplier_id', [$supplierId]);
            })
            ->paginate($maxResults);

        $data = $data_raw->items();
        
        return response()->json([
            'message' => 'Success',
            'status' => '200',
            // 'count' => count($data),
            // 'min_price' => $minPrice,
            // 'max_price' => $maxPrice,
            'data' => $data,
            'meta' => [
                'pagination' => [
                    'page' => $data_raw->currentPage(),
                    'limit' => $data_raw->perPage(),
                    'total' => $data_raw->total(),
                    'total_pages' => $data_raw->lastPage()
                ],
                'keyword' => 'tes1',
                'sort' => 'tes2'
            ]
        ], 201);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed',
            'status' => '500',
            'data' => '',
            'errors' => [$e->getMessage()]
        ], 500);
    }


    // dd(json_decode($ordersWithCustomerAndDetails));
    // echo "<pre>";
    // print_r(json_decode($ordersWithCustomerAndDetails));

    return $ordersWithCustomerAndDetails;
});