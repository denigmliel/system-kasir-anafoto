<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosScannerController extends Controller
{
    /**
     * Halaman ringan untuk dipakai di HP (scanner).
     */
    public function index()
    {
        return view('kasir.scanner-mobile');
    }

    /**
     * Endpoint HP: simpan kode hasil scan ke tabel sementara.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $userId = Auth::id();
        $code = trim($request->input('code'));

        $product = Product::where('code', $code)->first();

        if (! $product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk dengan kode tersebut tidak ditemukan di POS.',
            ], 422);
        }

        DB::table('pos_temp_scans')->insert([
            'user_id' => $userId,
            'product_code' => $code,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $price = $product->price;
        $defaultUnit = $product->units()->where('is_default', true)->first();
        if ($defaultUnit) {
            $price = $defaultUnit->price;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil dikirim ke POS',
            'product' => [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'price' => $price,
            ],
        ]);
    }

    /**
     * Endpoint PC: ambil scan antrian terlama, lalu hapus.
     */
    public function check(): JsonResponse
    {
        $userId = Auth::id();

        $scan = DB::table('pos_temp_scans')
            ->where('user_id', $userId)
            ->orderBy('created_at')
            ->first();

        if (! $scan) {
            return response()->json(['found' => false]);
        }

        DB::table('pos_temp_scans')->where('id', $scan->id)->delete();

        $product = Product::where('code', $scan->product_code)->first();

        return response()->json([
            'found' => true,
            'code' => $scan->product_code,
            'product' => $product,
        ]);
    }
}
