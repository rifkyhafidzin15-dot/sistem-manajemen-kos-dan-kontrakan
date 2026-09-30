<?php

namespace App\Http\Controllers;

use App\Models\Lease;

// Tambahkan ini

class TenantController extends Controller
{
    public function index()
    {
        $leases = Lease::with(['customer', 'room'])
            ->where('status', 'ACTIVE')
            ->latest()
            ->get();

        return view('tenants.index', compact('leases'));
    }

    // Mengakhiri masa sewa penyewa & mengosongkan kamar tanpa menghapus riwayat pembayaran
    public function destroy($id)
    {
        $lease = Lease::findOrFail($id);

        // 1. Kembalikan status kamar menjadi AVAILABLE
        if ($lease->room) {
            $lease->room->update(['status' => 'AVAILABLE']);
        }

        // 2. Tandai kontrak sewa selesai (ENDED) agar histori pendapatan tetap tersimpan
        $lease->update(['status' => 'ENDED']);

        return redirect()->back()->with('success', 'Masa sewa berhasil diakhiri dan kamar kembali tersedia.');
    }
}
