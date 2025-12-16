<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CatalogueController extends Controller
{
    /**
     * Display the catalogue page.
     */
    public function index()
    {
        return view('catalogue');
    }

    /**
     * Handle checkout and payment proof upload.
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_type' => 'required|in:CORE,SCALE,INFINITE',
            'package_amount' => 'required|numeric|min:0',
            'payment_proof' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // max 5MB
        ], [
            'package_type.required' => 'Tipe paket harus dipilih.',
            'package_type.in' => 'Tipe paket tidak valid.',
            'package_amount.required' => 'Jumlah pembayaran harus diisi.',
            'package_amount.numeric' => 'Jumlah pembayaran harus berupa angka.',
            'payment_proof.required' => 'Bukti pembayaran harus diupload.',
            'payment_proof.image' => 'File yang diupload harus berupa gambar.',
            'payment_proof.mimes' => 'Format file harus JPG, PNG, GIF, atau WEBP.',
            'payment_proof.max' => 'Ukuran file maksimal 5MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Store payment proof
            $file = $request->file('payment_proof');
            $fileName = time() . '_' . $request->package_type . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('payment_proofs', $fileName, 'public');

            // Here you can save to database, send email notification, etc.
            // For now, we'll just return success

            return response()->json([
                'message' => 'Bukti pembayaran berhasil dikirim. Kami akan memverifikasi pembayaran Anda segera.',
                'path' => $path
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengupload file. Silakan coba lagi.'
            ], 500);
        }
    }
}

