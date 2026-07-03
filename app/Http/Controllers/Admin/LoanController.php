<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    private function makeLoan(int $id, string $nama, string $email, string $judul, string $penulis, string $tglPinjam, string $tglKembali, string $status = 'dipinjam'): \stdClass
    {
        $loan = new \stdClass();
        $loan->id              = $id;
        $loan->tanggal_pinjam  = $tglPinjam;
        $loan->tanggal_kembali = $tglKembali;
        $loan->status          = $status;
        $loan->denda           = 0;

        $user = new \stdClass();
        $user->nama         = $nama;
        $user->email        = $email;
        $user->active_loans = 1;
        $loan->user         = $user;

        $buku          = new \stdClass();
        $buku->judul   = $judul;
        $buku->penulis = $penulis;
        $det           = new \stdClass();
        $det->buku     = $buku;
        $det->jumlah   = 1;
        $col           = collect([$det]);

        $loan->detail = new class($col) {
            public function __construct(private $col) {}
            public function first() { return $this->col->first(); }
            public function getIterator() { return $this->col->getIterator(); }
        };

        return $loan;
    }

    /**
     * Data dummy peminjaman menunggu konfirmasi admin
     */
    private function loanMenunggu(): \Illuminate\Support\Collection
    {
        return collect([
            $this->makeLoan(1, 'John Doe',  'john@mail.com',  'Laskar Pelangi', 'Andrea Hirata',  '2026-06-25', '2026-07-09', 'menunggu'),
            $this->makeLoan(2, 'Citra M.',  'citra@mail.com', 'Sapiens',        'Yuval Harari',   '2026-06-25', '2026-07-09', 'menunggu'),
        ]);
    }

    /**
     * Data dummy peminjaman yang sudah aktif dipinjam
     */
    private function loanAktif(): \Illuminate\Support\Collection
    {
        return collect([
            $this->makeLoan(3, 'Jane Smith', 'jane@mail.com', 'Atomic Habits', 'James Clear',    '2026-06-10', '2026-06-24', 'dipinjam'),
            $this->makeLoan(4, 'Budi S.',    'budi@mail.com', 'Bumi Manusia',  'Pramoedya A.T.', '2026-06-05', '2026-06-22', 'dipinjam'),
            $this->makeLoan(5, 'Ani R.',     'ani@mail.com',  'Deep Work',     'Cal Newport',    '2026-05-28', '2026-06-11', 'dipinjam'),
        ]);
    }

    /**
     * Tampilkan daftar peminjaman dengan 2 tab:
     * - Tab 'menunggu': permintaan yang butuh konfirmasi admin
     * - Tab 'aktif': buku yang sedang dipinjam
     */
    public function index(Request $request)
    {
        $tab      = $request->get('tab', 'menunggu');
        $menunggu = $this->loanMenunggu();
        $aktif    = $this->loanAktif();

        if ($tab === 'aktif') {
            $loans = $aktif;
        } else {
            $loans = $menunggu;
        }

        $page = $request->get('page', 1);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $loans->values(), $loans->count(), 10, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.loans.index', [
            'loans'          => $paginator,
            'menunggu'       => $menunggu,
            'aktif'          => $aktif,
            'jumlahMenunggu' => $menunggu->count(),
            'tab'            => $tab,
        ]);
    }

    /**
     * Detail satu transaksi peminjaman
     */
    public function show($id)
    {
        $semua  = $this->loanMenunggu()->merge($this->loanAktif());
        $loan   = $semua->firstWhere('id', (int) $id) ?? $semua->first();
        $isDone = false;

        return view('admin.loans.show', compact('loan', 'isDone'));
    }

    /**
     * Konfirmasi peminjaman — ubah status dari 'menunggu' ke 'dipinjam'
     * Nanti Dev 4 akan isi logic nyata di sini (update DB + kurangi stok)
     */
    public function confirm($id)
    {
        return redirect()->route('admin.loans.index')
            ->with('success', 'Peminjaman #' . $id . ' berhasil dikonfirmasi. Status berubah menjadi Dipinjam.');
    }

    /**
     * Tolak permintaan peminjaman — hapus atau ubah status ke 'ditolak'
     * Nanti Dev 4 akan isi logic nyata di sini
     */
    public function reject($id)
    {
        return redirect()->route('admin.loans.index')
            ->with('success', 'Permintaan peminjaman #' . $id . ' ditolak.');
    }
}
