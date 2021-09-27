<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        $show_product = $request->input('show_product');
        
        $categories = ProductCategory::query();
        if ($id) {
            $categories = $categories->with(['product'])->find($id);

            if ($categories) {
                return ResponseFormatter::success(
                    $categories,
                    'Data Kategori Berhasil Di Dapatkan 🚀 '
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'yaah, Kategori nya ga ketemu 🚣 ',
                    404
                );
            }
        }

        if ($name) {
            $categories->where('name','like', '%', $name, '%');
        }

        if ($show_product) {
            $categories->with('product');
        }

        return ResponseFormatter::success(
            $categories->paginate($limit),
            'Hore, Data Kategori Berhasil Di dapatkan 🚀 '
        );
    }
}
