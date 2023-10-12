<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // Retrieve all products
    // public function index()
    // {

    //     $products = Product::all();
    //     return response()->json(['products' => $products]);
    // }
    // public function index()
    // {
    //     $products = Product::with('productMaterials')->get();

    //     return response()->json(['products' => $products]);
    // }


    public function index()
    {
        // Retrieve data from the database
        $warehouses = DB::table('warehouses')->get();
        $materials = DB::table('materials')->get();
        $products = DB::table('products')->get();
        $productMaterials = DB::table('product_materials')->get();

        // Initialize an array to store the result
        $result = [];

        // Loop through products to calculate quantities
        foreach ($products as $product) {
            $productData = [
                'product_name' => $product->product_name,
                'product_code' => $product->product_code,
                'product_materials' => [],
            ];

            foreach ($productMaterials as $pm) {
                if ($pm->product_id === $product->id) {
                    // Calculate quantities using warehouse data
                    $material = $materials->firstWhere('id', $pm->material_id);
                    $warehouse = $warehouses->firstWhere('material_id', $pm->material_id);

                    $quantity = min($pm->quantity, $warehouse->remainder);

                    // Construct product materials array
                    $productMaterial = [
                        'warehouse_id' => $warehouse->id,
                        'material_name' => $material->material_name,
                        'qty' => $quantity,
                        'price' => $warehouse->price,
                    ];

                    $productData['product_materials'][] = $productMaterial;

                    // Update the remainder in the warehouse
                    DB::table('warehouses')
                        ->where('id', $warehouse->id)
                        ->update(['remainder' => $warehouse->remainder - $quantity]);
                }
            }

            $result[] = $productData;
        }

        return response()->json(['result' => $result]);
    }


    // Create a new product
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_name' => 'required|string',
            'product_code' => 'required|integer',
        ]);

        $product = Product::create($data);

        return response()->json(['message' => 'Data inserted successfully', 'product' => $product], 201);
    }

    // Update an existing product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $data = $request->validate([
            'product_name' => 'string',
            'product_code' => 'integer',
        ]);

        $product->update($data);

        return response()->json(['product' => $product]);
    }
}
