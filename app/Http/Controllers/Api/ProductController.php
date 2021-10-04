<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->id;
        $limit = $request->input('limit', 6);
        $name = $request->name;
        $description = $request->description;
        $tags = $request->tags;
        $categories = $request->categories;

        $price_from = $request->price_from;
        $price_to = $request->price_to;

        $product = Product::with(['category', 'productGallery']);

        if ($id) {
            $product = $product->find($id);
            if ($product) {
                return ResponseFormatter::success(
                    $product, 
                    'Data Produk Berhasil Di Dapatkan 🚀 '
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data Produk Gagal Di Dapatkan 💥 ',
                    404
                );
            }
        }

        if ($name) {
            $product->where('name', 'like', '%', $name, '%');
        }
        if ($description) {
            $product->where('description', 'like', '%', $description, '%');
        }
        if ($tags) {
            $product->where('tags', 'like', '%', $tags, '%');
        }
        if ($price_from) {
            $product->where('price', '>=', $price_from);
        }
        if ($price_to) {
            $product->where('price', '<=', $price_to);
        }
        if ($categories) {
            $product->where('product_category_id',$categories);
        }

        return ResponseFormatter::success(
            $product->paginate($limit),
            'Data Produk Berhasil Di Dapatkan 🚀 '
        );
    }
}
