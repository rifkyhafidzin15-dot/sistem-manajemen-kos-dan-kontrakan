@extends('layouts.app')

@section('content')
<div x-data="{ expenseFilter: 'all' }" class="space-y-6 pb-12">
    
    <!-- HEADER TITLE & ACTION -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Pencatatan Beban Pengeluaran (OPEX & CAPEX)
            </h2>
            <p class="text-xs text-slate-400 mt-1">Pengelolaan biaya operasional rutin (Listrik/Air/Sampah/Wi-Fi) dan pemeliharaan fisik properti</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-semibold text-slate-300">
                Ledger Pengeluaran
            </span>
        </div>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-6 rounded-3xl bg-[#111827] border border-slate-800 shadow-subtle-card">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-bold uppercase tracking-wider text-[10px] text-rose-400">Total OPEX Bulanan (Rutin)</span>
                <span class="px-2 py-0.5 rounded bg-rose-500/10 text-rose-400 text-[10px] font-bold">OPERASIONAL</span>
            </div>
            <p class="text-3xl font-extrabold text-rose-400 font-mono tracking-tight">Rp 250.000</p>
            <p class="text-xs text-slate-400 mt-2">Tagihan rutin listrik, air pompa, kebersihan lingkungan, dan Wi-Fi</p>
        </div>

        <div class="p-6 rounded-3xl bg-[#111827] border border-slate-800 shadow-subtle-card">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-bold uppercase tracking-wider text-[10px] text-amber-400">Total CAPEX (Modal / Aset)</span>
                <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 text-[10px] font-bold">INVESTASI</span>
            </div>
            <p class="text-3xl font-extrabold text-amber-400 font-mono tracking-tight">Rp 0</p>
            <p class="text-xs text-slate-400 mt-2">Perbaikan besar fasilitas fisik bangunan, renovasi atap, dan penambahan aset</p>
        </div>
    </div>

    <!-- TABLE LIST PENGELUARAN -->
    <div class="bg-[#111827] rounded-3xl border border-slate-800 overflow-hidden shadow-subtle-card">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-900/80 border-b border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="p-4 pl-6">Tanggal</th>
                        <th class="p-4">Kategori Pengeluaran</th>
                        <th class="p-4">Tipe Biaya</th>
                        <th class="p-4">Keterangan</th>
                        <th class="p-4 pr-6 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="p-4 pl-6 text-slate-300 font-medium font-mono">01 Sep 2026</td>
                        <td class="p-4 font-bold text-white">Listrik Pompa & Penerangan Utama</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                OPEX
                            </span>
                        </td>
                        <td class="p-4 text-slate-400">Token listrik area publik, pompa air, dan penerangan lorong</td>
                        <td class="p-4 pr-6 text-right font-extrabold text-rose-400 font-mono text-sm">
                            -Rp 250.000
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection