@extends('layouts.app')

@section('content')
@php
    $totalRooms = $rooms->count();
    $occupiedRooms = $rooms->where('status', 'OCCUPIED')->count();
    $availableRooms = $rooms->where('status', 'AVAILABLE')->count();
    $avgPrice = $totalRooms > 0 ? $rooms->avg('base_price') : 0;
@endphp

<div x-data="{
    searchQuery: '',
    statusFilter: 'all',
    viewMode: 'table'
}" class="space-y-6 pb-12">

    <!-- HEADER TITLE & METRICS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Manajemen Unit Properti
            </h2>
            <p class="text-xs text-slate-400 mt-1">Daftar lengkap unit Kontrakan & Kost beserta spesifikasi kamar dan harga dasar</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-glow-indigo transition-all">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                + Sewa Unit Kamar
            </a>
        </div>
    </div>

    <!-- STAT SUMMARY MINI CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-[#111827] border border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Kapasitas</span>
            <div class="mt-1 text-2xl font-extrabold text-white font-mono">{{ $totalRooms }} Unit</div>
            <span class="text-[11px] text-slate-400">Terdaftar di sistem</span>
        </div>
        <div class="p-4 rounded-2xl bg-[#111827] border border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Unit Tersedia</span>
            <div class="mt-1 text-2xl font-extrabold text-emerald-400 font-mono">{{ $availableRooms }} Unit</div>
            <span class="text-[11px] text-slate-400">Siap untuk disewakan</span>
        </div>
        <div class="p-4 rounded-2xl bg-[#111827] border border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-400">Unit Terisi</span>
            <div class="mt-1 text-2xl font-extrabold text-rose-400 font-mono">{{ $occupiedRooms }} Unit</div>
            <span class="text-[11px] text-slate-400">Penyewa aktif</span>
        </div>
        <div class="p-4 rounded-2xl bg-[#111827] border border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400">Rata-rata Tarif</span>
            <div class="mt-1 text-xl font-extrabold text-white font-mono">Rp {{ number_format((int) $avgPrice, 0, ',', '.') }}</div>
            <span class="text-[11px] text-slate-400">per bulan</span>
        </div>
    </div>

    <!-- CONTROLS & FILTER BAR -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-[#111827] border border-slate-800">
        <!-- Search -->
        <div class="relative flex-1 max-w-md">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari nomor kamar atau tipe (K-01, Kontrakan...)" 
                   class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-white placeholder:text-slate-400 focus:border-indigo-500 outline-none">
        </div>

        <!-- Filter Tabs -->
        <div class="inline-flex p-1 rounded-xl bg-slate-900 border border-slate-800 text-xs font-semibold">
            <button @click="statusFilter = 'all'" 
                    :class="statusFilter === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                    class="px-3 py-1.5 rounded-lg transition-all">
                Semua ({{ $totalRooms }})
            </button>
            <button @click="statusFilter = 'AVAILABLE'" 
                    :class="statusFilter === 'AVAILABLE' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                    class="px-3 py-1.5 rounded-lg transition-all">
                Tersedia ({{ $availableRooms }})
            </button>
            <button @click="statusFilter = 'OCCUPIED'" 
                    :class="statusFilter === 'OCCUPIED' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                    class="px-3 py-1.5 rounded-lg transition-all">
                Terisi ({{ $occupiedRooms }})
            </button>
        </div>
    </div>

    <!-- TABLE LIST UNIT -->
    <div class="bg-[#111827] rounded-3xl border border-slate-800 overflow-hidden shadow-subtle-card">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-900/80 border-b border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="p-4 pl-6">Nomor Unit</th>
                        <th class="p-4">Tipe Properti</th>
                        <th class="p-4">Ukuran</th>
                        <th class="p-4">Harga Dasar</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($rooms as $room)
                    <tr class="hover:bg-slate-800/30 transition-colors"
                        x-show="(statusFilter === 'all' || statusFilter === '{{ $room->status }}') && ('{{ strtolower($room->room_number . ' ' . $room->room_type) }}'.includes(searchQuery.toLowerCase()))">
                        <td class="p-4 pl-6 font-extrabold text-white font-mono text-sm">
                            <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-indigo-300">
                                {{ $room->room_number }}
                            </span>
                        </td>
                        <td class="p-4 font-semibold text-slate-200">{{ $room->room_type }}</td>
                        <td class="p-4 text-slate-400">{{ $room->size ?? 'Standar' }}</td>
                        <td class="p-4 font-extrabold text-white font-mono text-sm">
                            Rp {{ number_format($room->base_price, 0, ',', '.') }}<span class="text-[10px] text-slate-400 font-normal">/bln</span>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full border {{ $room->status === 'OCCUPIED' ? 'bg-rose-500/10 text-rose-300 border-rose-500/20' : 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20' }}">
                                {{ $room->status }}
                            </span>
                        </td>
                        <td class="p-4 pr-6 text-right space-x-2">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-semibold transition-colors">
                                Kelola di Dashboard →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-400">
                            Belum ada unit yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection