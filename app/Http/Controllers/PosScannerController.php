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
     * Preview produk (tanpa memasukkan ke antrean POS).
     */
    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = trim($request->input('code'));

        $product = Product::with('units')->where('code', $code)->first();

        if (! $product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk dengan kode tersebut tidak ditemukan di POS.',
            ], 422);
        }

        $units = $product->units->map(function ($unit) {
            return [
                'id' => $unit->id,
                'name' => $unit->name,
                'price' => $unit->price,
                'is_default' => (bool) $unit->is_default,
            ];
        })->values();

        $defaultUnit = $units->firstWhere('is_default', true) ?: $units->first();
        $price = $defaultUnit['price'] ?? $product->price;

        return response()->json([
            'status' => 'success',
            'product' => [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'price' => $price,
                'units' => $units,
            ],
        ]);
    }

    /**
     * Endpoint HP: simpan kode hasil scan ke tabel sementara.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'product_unit_id' => ['nullable', 'integer', 'exists:product_units,id'],
        ]);

        $userId = Auth::id();
        $code = trim($request->input('code'));

        $product = Product::with('units')->where('code', $code)->first();

        if (! $product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk dengan kode tersebut tidak ditemukan di POS.',
            ], 422);
        }

        $quantity = max((int) $request->input('quantity', 1), 1);
        $unitId = $request->input('product_unit_id');

        $unit = null;
        if ($unitId) {
            $unit = $product->units->firstWhere('id', (int) $unitId);
        }

        if (! $unit) {
            $unit = $product->units->firstWhere('is_default', true) ?: $product->units->first();
        }

        $price = $unit ? $unit->price : $product->price;

        DB::table('pos_temp_scans')->insert([
            'user_id' => $userId,
            'product_code' => $code,
            'product_unit_id' => $unit ? $unit->id : null,
            'quantity' => $quantity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil dikirim ke POS',
            'product' => [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'price' => $price,
                'unit_id' => $unit ? $unit->id : null,
                'unit_name' => $unit ? $unit->name : null,
                'quantity' => $quantity,
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
            'product_unit_id' => $scan->product_unit_id,
            'quantity' => $scan->quantity ?? 1,
        ]);
    }
}
