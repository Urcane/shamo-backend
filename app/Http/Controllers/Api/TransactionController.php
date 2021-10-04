<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
