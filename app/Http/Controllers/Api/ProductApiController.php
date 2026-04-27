<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductApiController extends Controller
{
    /**
     * TECHNIQUE MATOOR : Consultation rapide pour l'IA
     */
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'status' => 'success',
            'count' => $products->count(),
            'data' => $products
        ], 200);
    }

    /**
     * TECHNIQUE HARIK : Injection sécurisée avec validation
     */
    public function store(Request $request)
    {
        // Validation stricte (Standard Harik)
        $validator = Validator::make($request->all(), [
            'designation' => 'required|string|max:255',
            'prix_unitaire' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'code_barre' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Injection propre
        $product = Product::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Données injectées via API',
            'data' => $product
        ], 201);
    }
}
