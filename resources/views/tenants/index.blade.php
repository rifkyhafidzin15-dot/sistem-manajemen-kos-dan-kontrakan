@extends('layouts.app')

@section('content')
@php
    $totalActiveTenants = $leases->count();
    $totalMonthlyRent = $leases->sum('rental_rate');
@endphp

<div x-data="{ searchQuery: '' }" class="space-y-6 pb-12">
    
    <!-- SUCCESS NOTIFICATION -->
    @if (session('success'))
        <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm shadow-glow-emerald">
            <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div class="font-medium">{{ session('success') }}</div>
        </div>
    @endif

    <!-- HEADER TITLE & STATS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Daftar Penyewa Aktif (CRM)
            </h2>
            <p class="text-xs text-slate-400 mt-1">Data seluruh penghuni kost dan kontrakan yang saat ini memiliki kontrak sewa aktif</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-glow-indigo transition-all">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                + Tambah Penyewa Baru
            </a>
        </div>
    </div>

    <!-- STAT SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-[#111827] border border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Penyewa Aktif</span>
            <div class="mt-1 text-2xl font-extrabold text-white font-mono">{{ $totalActiveTenants }} Orang</div>
            <span class="text-[11px] text-emerald-400 font-medium">100% Terverifikasi</span>
        </div>
        <div class="p-4 rounded-2xl bg-[#111827] border border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400">Total Nilai Kontrak Berjalan</span>
            <div class="mt-1 text-2xl font-extrabold text-white font-mono">Rp {{ number_format($totalMonthlyRent, 0, ',', '.') }}</div>
            <span class="text-[11px] text-slate-400">Penerimaan sewa per bulan</span>
        </div>
        <div class="p-4 rounded-2xl bg-[#111827] border border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Status Sewa</span>
            <div class="mt-1 text-2xl font-extrabold text-emerald-400 font-mono">ACTIVE</div>
            <span class="text-[11px] text-slate-400">Kontrak terlindungi database</span>
        </div>
    </div>

    <!-- SEARCH & FILTER BAR -->
    <div class="p-4 rounded-2xl bg-[#111827] border border-slate-800 flex items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari nama penyewa, nomor HP, atau kamar..." 
                   class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-white placeholder:text-slate-400 focus:border-indigo-500 outline-none">
        </div>
    </div>

    <!-- TABLE LIST PENYEWA -->
    <div class="bg-[#111827] rounded-3xl border border-slate-800 overflow-hidden shadow-subtle-card">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-900/80 border-b border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="p-4 pl-6">Nama Penyewa</th>
                        <th class="p-4">Kamar</th>
                        <th class="p-4">Kontak WhatsApp / HP</th>
                        <th class="p-4">Tanggal Masuk</th>
                        <th class="p-4">Tarif Sewa</th>
                        <th class="p-4 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($leases as $lease)
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $lease->customer->phone ?? '');
                    @endphp
                    <tr class="hover:bg-slate-800/30 transition-colors"
                        x-show="'{{ strtolower(($lease->customer->name ?? '') . ' ' . ($lease->room->room_number ?? '') . ' ' . ($lease->customer->phone ?? '')) }}'.includes(searchQuery.toLowerCase())">
                        
                        <!-- Tenant Name & Avatar -->
                        <td class="p-4 pl-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-800 flex items-center justify-center font-bold text-white uppercase text-xs flex-shrink-0">
                                    {{ substr($lease->customer->name ?? 'P', 0, 1) }}
                                </div>
                                <div>
                                    <span class="font-bold text-white block">{{ $lease->customer->name ?? '-' }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $lease->customer->email ?? '' }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Room -->
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-bold font-mono">
                                {{ $lease->room->room_number ?? '-' }}
                            </span>
                        </td>

                        <!-- Phone / Contact -->
                        <td class="p-4 font-mono">
                            <div class="flex items-center gap-2">
                                <span>{{ $lease->customer->phone ?? '-' }}</span>
                                @if($cleanPhone)
                                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" title="Kirim Pesan WhatsApp" class="p-1 rounded bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 transition-colors">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    </a>
                                @endif
                            </div>
                        </td>

                        <!-- Start Date -->
                        <td class="p-4 text-slate-300">
                            {{ \Carbon\Carbon::parse($lease->start_date)->format('d M Y') }}
                        </td>

                        <!-- Rent Rate -->
                        <td class="p-4 font-extrabold text-emerald-400 font-mono text-sm">
                            Rp {{ number_format($lease->rental_rate, 0, ',', '.') }}<span class="text-[10px] text-slate-400 font-normal">/bln</span>
                        </td>

                        <!-- Action -->
                        <td class="p-4 pr-6 text-right">
                            <form action="{{ route('tenants.destroy', $lease->id) }}" method="POST" onsubmit="return confirm('Yakin ingin mengakhiri sewa penyewa ini? Kamar akan kembali tersedia dan status kontrak menjadi ENDED.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-rose-500/10 text-rose-300 hover:text-white border border-rose-500/20 hover:bg-rose-600 rounded-xl transition-all text-xs font-semibold">
                                    Akhiri Sewa
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 px-4 text-center">
                            <div class="max-w-sm mx-auto space-y-3">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center mx-auto">
                                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </div>
                                <h3 class="text-base font-bold text-white">Belum Ada Penyewa Aktif</h3>
                                <p class="text-xs text-slate-400">Seluruh unit kamar saat ini siap untuk dihuni. Catat kontrak baru untuk menambahkan penyewa ke dalam CRM.</p>
                                <div class="pt-2">
                                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-glow-indigo transition-all">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                                        Pilih Unit & Catat Sewa
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection