<?php

namespace App\Http\Controllers;

use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('products');
    }

    /**
     * Get a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexJson()
    {
        return response()->json([
        	'products' => Product::getAllProducts()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
        	'name' => 'required|string|max:200',
        	'price' => 'required|numeric',
        	'quantity' => 'required|numeric',
        ]);

        $id = str_random(50);

        $products = Product::createNewProduct([
        	'id' => $id,
        	'name' => $request->name,
        	'price' => $request->price,
        	'quantity' => $request->quantity,
        	'created_at' => Carbon::now()->toDateTimeString(),
        	'updated_at' => Carbon::now()->toDateTimeString(),
        ]);

        return response()->json([
        	'result' => true,
        	'id' => $id
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
        	'name' => 'required|string|max:200',
        	'price' => 'required|numeric',
        	'quantity' => 'required|numeric',
        ]);

    	$product = Product::getAllProducts()->where('id', $id);

    	if ($product) {
    		Product::updateWhereId($id, [
	        	'name' => $request->name,
	        	'price' => $request->price,
	        	'quantity' => $request->quantity,
	        	'updated_at' => Carbon::now()->toDateTimeString(),
    		]);

	        return response()->json(['result' => true]);
    	} else {
	        return response()->json(['error' => 'This product is not exist!'], 422);
    	}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    	$product = Product::getAllProducts()->where('id', $id);
    	if ($product) {
	        Product::removeWhereId($id);
	        return response()->json(['result' => true]);
    	} else {
	        return response()->json(['error' => 'This product is not exist!'], 422);
    	}
    }
}
