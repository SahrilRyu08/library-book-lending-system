<?php

namespace App\Http\Requests;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class StoreLoanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // We will validate session cart items.
        // Expected cart format (from session):
        // session('cart') = [
        //   'items' => [
        //     ['buku_id' => 1, 'jumlah' => 2],
        //     ...
        //   ]
        // ]

        $cartItems = $this->cartItems();

        return [
            'cart' => 'nullable',
            'cartItems' => 'array|min:1',
            'cartItems.*.buku_id' => 'required|integer|exists:buku,id',
            'cartItems.*.jumlah' => 'required|integer|min:1',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cartItems' => $this->cartItems(),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $maxBuku = (int) config('library.max_buku_per_pinjam', 3);

            $cartItems = $this->validated('cartItems', $this->cartItems());

            // Validate cart not empty
            if (empty($cartItems) || !is_array($cartItems)) {
                $validator->errors()->add('cart', 'Keranjang peminjaman masih kosong.');
                return;
            }

            // Check total books limit (active dipinjam)
            $activeCount = Peminjaman::where('user_id', $user->id)
                ->where('status', 'dipinjam')
                ->with('detail')
                ->get()
                ->reduce(function ($carry, $loan) {
                    return $carry + $loan->detail->sum('jumlah');
                }, 0);


            $totalRequested = collect($cartItems)->sum('jumlah');

            if ($totalRequested < 1) {
                $validator->errors()->add('cartItems', 'Jumlah buku minimal harus 1.');
                return;
            }

            if ($totalRequested + $activeCount > $maxBuku) {
                $validator->errors()->add('cartItems', "Total buku yang dipinjam melebihi batas maksimal ({$maxBuku} buku).");
                return;
            }

            // Validate stock availability per item
            foreach ($cartItems as $item) {
                $bukuId = (int) ($item['buku_id'] ?? 0);
                $jumlah = (int) ($item['jumlah'] ?? 0);

                $buku = Buku::query()->find($bukuId);
                if (!$buku) {
                    $validator->errors()->add('cartItems', 'Buku tidak ditemukan.');
                    continue;
                }

                // Buku::tersedia already accounts for dipinjam status.
                $tersedia = (int) $buku->tersedia;

                if ($jumlah > $tersedia) {
                    $validator->errors()->add(
                        'cartItems',
                        "Stok buku '{$buku->judul}' tidak mencukupi. Tersedia {$tersedia}, diminta {$jumlah}."
                    );
                }
            }
        });
    }

    /**
     * Helper to extract cart items from session.
     */
    private function cartItems(): array
    {
        $cart = $this->session('cart');

        if (!$cart) {
            return [];
        }

        // support multiple formats:
        // - cart = ['items' => [...]]
        // - cart = [...items]
        if (is_array($cart) && isset($cart['items']) && is_array($cart['items'])) {
            return array_values($cart['items']);
        }

        // If session cart already is items array
        if (is_array($cart)) {
            return array_values($cart);
        }

        return [];
    }

    /**
     * Normalize validated output.
     */
    public function validatedForStore(): array
    {
        $data = $this->validated();
        $cartItems = $data['cartItems'] ?? $this->cartItems();

        return [
            'cartItems' => $cartItems,
        ];
    }
}

