<?php

namespace App\Http\Requests;

use App\Models\Buku;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Catatan: payload diambil dari session('cart'), bukan dari input form
     * langsung, sesuai kontrak Tahap F (Integrasi Keranjang dengan Dev 3):
     *
     * [
     *     ['buku_id' => 1, 'jumlah' => 1],
     *     ['buku_id' => 5, 'jumlah' => 2],
     * ]
     *
     * Supaya bisa divalidasi lewat FormRequest, kita merge session cart
     * ke input sebelum validasi jalan (lihat prepareForValidation).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cart' => session('cart', []),
        ]);
    }

    public function rules(): array
    {
        return [
            'cart'            => ['required', 'array', 'min:1'],
            'cart.*.buku_id'  => ['required', 'integer', 'exists:buku,id'],
            'cart.*.jumlah'   => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'cart.required'        => 'Keranjang masih kosong.',
            'cart.min'             => 'Keranjang masih kosong.',
            'cart.*.buku_id.exists' => 'Salah satu buku pada keranjang tidak ditemukan.',
            'cart.*.jumlah.min'    => 'Jumlah buku minimal 1.',
        ];
    }

    /**
     * Validasi tambahan yang butuh logika (bukan cuma rule bawaan):
     * - total buku tidak melebihi max_buku_per_pinjam
     * - stok tiap buku masih tersedia (pakai accessor `tersedia` di model Buku)
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $cart = $this->input('cart', []);

            if (empty($cart)) {
                return; // sudah ditangkap rule 'required'
            }

            $maxBuku   = (int) config('library.max_buku_per_pinjam', 3);
            $totalBuku = collect($cart)->sum('jumlah');

            if ($totalBuku > $maxBuku) {
                $validator->errors()->add(
                    'cart',
                    "Total buku tidak boleh melebihi {$maxBuku} buku per peminjaman."
                );
            }

            foreach ($cart as $i => $item) {
                if (empty($item['buku_id'])) {
                    continue;
                }

                $buku = Buku::find($item['buku_id']);

                if ($buku && $buku->tersedia < ($item['jumlah'] ?? 0)) {
                    $validator->errors()->add(
                        "cart.$i.buku_id",
                        "Stok buku \"{$buku->judul}\" tidak mencukupi."
                    );
                }
            }
        });
    }
}
