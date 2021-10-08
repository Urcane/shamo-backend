<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = request()->input('id');
        $limit = request()->input('limit');
        $status = request()->input('status');

        if ($id) {
            $transaction = Transaction::with(['transactionItem.product'])->find($id);

            if ($transaction) {
                return ResponseFormatter::success($transaction, 'Data Transaksi Berhasil diambil 🚀 ');
            } else {
                return ResponseFormatter::error(
                    null,
                    "Data transaksi tidak ada 💥 ",
                    404
                );
            }
        }
        
        $transaction = Transaction::with(['transactionItem.product'])
                                    ->where('user_id', $request->user()->id);

        if ($status) {
            $transaction->where('status', $status);
        }

        return ResponseFormatter::success(
            $transaction->paginate($limit),
            "Data list transaksi berhasil di ambil 🚀 "
        );
    }

    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "address" => "required",
            "items" => "required|array",
            "items.*" => "exists:products,id",
            "total_price" => "required",
            "shipping_price" => "required",
            "status" => "required|in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPPING,DELIVERED",
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                "error" => $validator->errors()
            ],"Upss gagal validasi 💥 ", 422);
        }

        try {
            $transaction = Transaction::create([
                "user_id" => Auth::user()->id,
                "address" => $request->address,
                "total_price" => $request->total_price,
                "shipping_price" => $request->shipping_price,
                "status" => $request->status
            ]);
    
            foreach ($request->items as $product) {
                $items = TransactionItem::create([
                    "user_id" => Auth::user()->id,
                    "product_id" => $product['id'],
                    "transaction_id" => $transaction->id,
                    "quantity" => $product['quantity'],
                ]);
            }
    
            return ResponseFormatter::success(
                $transaction->load(['transactionItem.product']), 
                "Checkout Berhasil 🚀 "
            );
        } catch (\Exception $err) {
            return ResponseFormatter::error([
                "error" => $err
            ],"Upss terjadi kesalahan 💥 ", 500);
        }
    }
}
