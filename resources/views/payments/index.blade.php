@extends('layouts.app')

@section('content')
@php
    $totalPaymentsCollected = $payments->where('status', 'SUCCESS')->sum('amount');
    $successfulTransactions = $payments->where('status', 'SUCCESS')->count();
@endphp

<div x-data="{ searchQuery: '' }" class="space-y-6 pb-12">
    
    <!-- HEADER TITLE & METRICS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                Pembayaran & Tagihan Sewa
            </h2>
            <p class="text-xs text-slate-400 mt-1">Riwayat penerimaan kas masuk dari pembayaran sewa kamar dan kontrakan</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-semibold text-slate-300">
                Laporan Kas Masuk
            </span>
        </div>
    </div>

    <!-- STAT SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-[#111827] border border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Total Kas Masuk Terkumpul</span>
            <div class="mt-1 text-2xl font-extrabold text-emerald-400 font-mono">
                Rp {{ number_format($totalPaymentsCollected, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-slate-400">Status transaksi terverifikasi</span>
        </div>
        <div class="p-4 rounded-2xl bg-[#111827] border border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Transaksi Sukses</span>
            <div class="mt-1 text-2xl font-extrabold text-white font-mono">{{ $successfulTransactions }} Transaksi</div>
            <span class="text-[11px] text-slate-400">Tercatat di pembukuan</span>
        </div>
        <div class="p-4 rounded-2xl bg-[#111827] border border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400">Kanal Pembayaran</span>
            <div class="mt-1 text-xl font-extrabold text-white">Transfer & Tunai</div>
            <span class="text-[11px] text-slate-400">Multi-channel payment</span>
        </div>
    </div>

    <!-- SEARCH & FILTER BAR -->
    <div class="p-4 rounded-2xl bg-[#111827] border border-slate-800 flex items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari transaksi, penyewa, kamar..." 
                   class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-white placeholder:text-slate-400 focus:border-indigo-500 outline-none">
        </div>
    </div>

    <!-- TABLE LIST TRANSAKSI PEMBAYARAN -->
    <div class="bg-[#111827] rounded-3xl border border-slate-800 overflow-hidden shadow-subtle-card">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-900/80 border-b border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="p-4 pl-6">Tanggal Bayar</th>
                        <th class="p-4">Kamar & Penyewa</th>
                        <th class="p-4">Nominal</th>
                        <th class="p-4">Metode Pembayaran</th>
                        <th class="p-4 pr-6 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-slate-800/30 transition-colors"
                        x-show="'{{ strtolower(($payment->customer->name ?? '') . ' ' . ($payment->lease->room->room_number ?? '') . ' ' . ($payment->method ?? '')) }}'.includes(searchQuery.toLowerCase())">
                        
                        <!-- Date -->
                        <td class="p-4 pl-6 text-slate-300 font-medium">
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                        </td>

                        <!-- Room & Tenant -->
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-lg bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-bold font-mono">
                                    {{ $payment->lease->room->room_number ?? '-' }}
                                </span>
                                <span class="font-bold text-white">{{ $payment->customer->name ?? '-' }}</span>
                            </div>
                        </td>

                        <!-- Amount -->
                        <td class="p-4 font-extrabold text-emerald-400 font-mono text-sm">
                            +Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </td>

                        <!-- Method -->
                        <td class="p-4 text-slate-300">
                            <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-[11px] font-mono">
                                {{ $payment->method ?? 'Transfer Bank' }}
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td class="p-4 pr-6 text-right">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                {{ $payment->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-slate-400">
                            Belum ada riwayat pembayaran sewa yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection