@extends('layouts.app')

@section('title', 'Peta Taburan Penternak (EPU & EPTR) - JPVNK')
@section('page_title', 'Peta Taburan Penternak GIS')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .custom-marker-icon {
        background: transparent;
        border: none;
    }
    .leaflet-popup-content-wrapper {
        padding: 0;
        border-radius: 1rem;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .leaflet-popup-content {
        margin: 0;
        line-height: 1.5;
    }
    .leaflet-container {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    #gisMap {
        width: 100% !important;
        height: 650px !important;
        min-height: 550px !important;
        position: relative;
    }
    .fixed #gisMap {
        height: 100% !important;
        min-height: 100% !important;
    }
    .map-height {
        height: 650px !important;
        min-height: 550px !important;
    }
    @media (max-width: 1024px) {
        #gisMap, .map-height {
            height: 540px !important;
            min-height: 500px !important;
        }
    }
    .leaflet-tile {
        visibility: inherit !important;
    }
    .leaflet-control-zoom {
        border-radius: 0.75rem !important;
        overflow: hidden;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1) !important;
        border: 1px solid #cbd5e1 !important;
        margin-right: 1rem !important;
        margin-bottom: 1.25rem !important;
    }
    .leaflet-control-zoom a {
        background-color: rgba(255, 255, 255, 0.95) !important;
        color: #1e293b !important;
        font-weight: bold !important;
        transition: all 0.15s ease-in-out;
    }
    .leaflet-control-zoom a:hover {
        background-color: #f1f5f9 !important;
        color: #059669 !important;
    }
</style>
@endpush

@section('content')
<div x-data="gisMapApp()" x-init="initMap()" @keydown.escape.window="if(isFullscreen) toggleFullscreen()" class="space-y-4">

    <!-- Top Executive Header & Statistics Bar -->
    <div class="bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 rounded-2xl p-4 sm:p-6 text-white shadow-xl border border-slate-800">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1.5">
                        <i class="fa-solid fa-satellite-dish animate-pulse"></i> GIS Geospatial Eksekutif
                    </span>
                    <span class="text-xs text-slate-400">Negeri Kelantan Darul Naim</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white flex items-center gap-2.5">
                    <i class="fa-solid fa-map-location-dot text-emerald-400"></i>
                    Peta Taburan Penternak EPU &amp; EPTR
                </h1>
                <p class="text-xs sm:text-sm text-slate-300">
                    Pemantauan taburan lokasi ladang unggas (EPU) dan ternakan ruminan (EPTR) secara langsung bagi tujuan pengurusan &amp; kawalan biosekuriti.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2 self-start lg:self-center">
                <button @click="resetFilters()" type="button" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-rotate-left text-amber-400"></i> Reset Penapis
                </button>
                <button @click="toggleLayer()" type="button" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 shadow-md flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-layer-group text-sky-400"></i> <span x-text="currentLayerName">Mod Satelit</span>
                </button>
                <button @click="fitAllMarkers()" type="button" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-crosshairs text-emerald-400"></i> Fokus Kelantan
                </button>
                <button @click="toggleFullscreen()" type="button" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md flex items-center gap-1.5 transition hover:scale-102">
                    <i :class="isFullscreen ? 'fa-solid fa-compress text-amber-300' : 'fa-solid fa-expand'"></i>
                    <span x-text="isFullscreen ? 'Keluar Skrin Penuh' : 'Skrin Penuh'"></span>
                </button>
            </div>
        </div>

        <!-- 4 KPI Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-5 mt-5 border-t border-slate-800/80">
            <!-- Total Penternak Dipaparkan -->
            <div class="bg-slate-800/60 backdrop-blur-md rounded-xl p-3 border border-slate-700/50">
                <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                    <span>Penternak Aktif</span>
                    <i class="fa-solid fa-users-viewfinder text-emerald-400"></i>
                </div>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-xl sm:text-2xl font-extrabold text-white" x-text="filteredMarkers.length"></span>
                    <span class="text-[11px] text-slate-400">lokasi</span>
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">
                    Daripada <span class="font-bold text-slate-300" x-text="rawMarkers.length"></span> keseluruhan
                </div>
            </div>

            <!-- Total Unggas (EPU) -->
            <div class="bg-slate-800/60 backdrop-blur-md rounded-xl p-3 border border-slate-700/50">
                <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                    <span>Unggas (EPU)</span>
                    <i class="fa-solid fa-feather-pointed text-amber-400"></i>
                </div>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-xl sm:text-2xl font-extrabold text-amber-400" x-text="totalFilteredUnggas.toLocaleString()"></span>
                    <span class="text-[11px] text-slate-400">ekor/kapasiti</span>
                </div>
                <div class="text-[10px] text-amber-300/80 mt-0.5" x-text="countFilteredEpu + ' ladang berdaftar'"></div>
            </div>

            <!-- Total Ruminan (EPTR) -->
            <div class="bg-slate-800/60 backdrop-blur-md rounded-xl p-3 border border-slate-700/50">
                <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                    <span>Ruminan (EPTR)</span>
                    <i class="fa-solid fa-cow text-sky-400"></i>
                </div>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-xl sm:text-2xl font-extrabold text-sky-400" x-text="totalFilteredRuminan.toLocaleString()"></span>
                    <span class="text-[11px] text-slate-400">ekor berdaftar</span>
                </div>
                <div class="text-[10px] text-sky-300/80 mt-0.5" x-text="countFilteredEptr + ' penternak'"></div>
            </div>

            <!-- Jajahan Teramai -->
            <div class="bg-slate-800/60 backdrop-blur-md rounded-xl p-3 border border-slate-700/50">
                <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                    <span>Jajahan Teramai</span>
                    <i class="fa-solid fa-chart-pie text-purple-400"></i>
                </div>
                <div class="mt-1">
                    <span class="text-lg sm:text-xl font-extrabold text-purple-300 truncate block" x-text="topJajahanName"></span>
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">
                    Taburan tertinggi dalam penapis
                </div>
            </div>
        </div>
    </div>

    <!-- Main Workspace Layout: Filter Sidebar + Interactive Leaflet Map + Drawer Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        
        <!-- Filter Controls Panel (4 Cols on Desktop) -->
        <div class="lg:col-span-4 space-y-4">
            
            <!-- Filter Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-filter"></i>
                        </div>
                        <h2 class="text-sm font-bold text-slate-800">Penapis Taburan Penternak</h2>
                    </div>
                    <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-bold" x-text="filteredMarkers.length + ' Ditemui'"></span>
                </div>

                <!-- Carian Pantas -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        <i class="fa-solid fa-magnifying-glass mr-1 text-slate-400"></i> Carian Pantas
                    </label>
                    <div class="relative">
                        <input type="text" x-model="searchQuery" @input="applyFilters()" placeholder="Nama penternak, ladang, no IC, jajahan..." class="w-full pl-9 pr-8 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                        <i class="fa-solid fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        <button x-show="searchQuery" @click="searchQuery = ''; applyFilters()" type="button" class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-circle-xmark text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Penapis Modul (Semua / EPU / EPTR) -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Modul Sistem
                    </label>
                    <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 rounded-xl">
                        <button type="button" @click="selectedModul = 'ALL'; applyFilters()" :class="selectedModul === 'ALL' ? 'bg-white text-slate-800 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-800 font-medium'" class="py-1.5 text-xs rounded-lg transition text-center">
                            Semua
                        </button>
                        <button type="button" @click="selectedModul = 'EPU'; applyFilters()" :class="selectedModul === 'EPU' ? 'bg-amber-500 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-800 font-medium'" class="py-1.5 text-xs rounded-lg transition text-center flex items-center justify-center gap-1">
                            <i class="fa-solid fa-feather-pointed text-[10px]"></i> EPU
                        </button>
                        <button type="button" @click="selectedModul = 'EPTR'; applyFilters()" :class="selectedModul === 'EPTR' ? 'bg-emerald-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-800 font-medium'" class="py-1.5 text-xs rounded-lg transition text-center flex items-center justify-center gap-1">
                            <i class="fa-solid fa-cow text-[10px]"></i> EPTR
                        </button>
                    </div>
                </div>

                <!-- Penapis Jajahan -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        <i class="fa-solid fa-location-dot mr-1 text-slate-400"></i> Jajahan Penternakan
                    </label>
                    <select x-model="selectedJajahan" @change="applyFilters()" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition cursor-pointer">
                        <option value="ALL">-- Semua Jajahan (Seluruh Kelantan) --</option>
                        @foreach($jajahanList as $jajahan)
                            <option value="{{ $jajahan }}">{{ $jajahan }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Penapis Jenis Ternakan (Checkbox Pills / Tags) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider">
                            <i class="fa-solid fa-paw mr-1 text-slate-400"></i> Jenis Ternakan
                        </label>
                        <button x-show="selectedLivestockTypes.length > 0" @click="selectedLivestockTypes = []; applyFilters()" type="button" class="text-[11px] text-rose-500 hover:underline">
                            Kosongkan Pilihan
                        </button>
                    </div>

                    <!-- Ruminan Tags -->
                    <div class="space-y-2">
                        <div>
                            <span class="text-[10px] font-bold text-sky-700 uppercase tracking-wide flex items-center gap-1 mb-1">
                                <i class="fa-solid fa-cow"></i> Ruminan (EPTR):
                            </span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($availableLivestockTypes['Ruminan'] as $ruminan)
                                <button type="button" 
                                    @click="toggleLivestockType('{{ $ruminan }}')" 
                                    :class="selectedLivestockTypes.includes('{{ $ruminan }}') ? 'bg-sky-600 text-white font-bold border-sky-600' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200'"
                                    class="px-2.5 py-1 rounded-lg text-xs border transition flex items-center gap-1">
                                    <span>{{ $ruminan }}</span>
                                    <i x-show="selectedLivestockTypes.includes('{{ $ruminan }}')" class="fa-solid fa-check text-[10px]"></i>
                                </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Unggas Tags -->
                        <div class="pt-1.5 border-t border-slate-100">
                            <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wide flex items-center gap-1 mb-1">
                                <i class="fa-solid fa-feather-pointed"></i> Unggas (EPU):
                            </span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($availableLivestockTypes['Unggas'] as $unggas)
                                <button type="button" 
                                    @click="toggleLivestockType('{{ $unggas }}')" 
                                    :class="selectedLivestockTypes.includes('{{ $unggas }}') ? 'bg-amber-500 text-white font-bold border-amber-500' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200'"
                                    class="px-2.5 py-1 rounded-lg text-xs border transition flex items-center gap-1">
                                    <span>{{ $unggas }}</span>
                                    <i x-show="selectedLivestockTypes.includes('{{ $unggas }}')" class="fa-solid fa-check text-[10px]"></i>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Petunjuk Simbol (Map Legend) -->
                <div class="pt-3 border-t border-slate-100 text-xs space-y-2">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Petunjuk Simbol Pin Peta
                    </span>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="flex items-center gap-2 p-1.5 rounded-lg bg-amber-50 border border-amber-200/60">
                            <span class="w-3.5 h-3.5 rounded-full bg-amber-500 flex items-center justify-center text-white text-[9px] font-bold">🐔</span>
                            <span class="text-amber-900 font-semibold text-[11px]">EPU (Unggas)</span>
                        </div>
                        <div class="flex items-center gap-2 p-1.5 rounded-lg bg-emerald-50 border border-emerald-200/60">
                            <span class="w-3.5 h-3.5 rounded-full bg-emerald-600 flex items-center justify-center text-white text-[9px] font-bold">🐄</span>
                            <span class="text-emerald-900 font-semibold text-[11px]">EPTR (Ruminan)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Drawer Preview / Quick Selector -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-list-ul text-emerald-600"></i> Senarai Padanan Penternak
                    </h3>
                    <span class="text-[11px] text-slate-400" x-text="filteredMarkers.length + ' item'"></span>
                </div>
                
                <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 pr-1 text-xs space-y-1">
                    <template x-for="item in filteredMarkers.slice(0, 50)" :key="item.id">
                        <div @click="focusOnMarker(item)" class="p-2 rounded-xl hover:bg-slate-50 cursor-pointer transition flex items-start justify-between gap-2 group">
                            <div class="space-y-0.5 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span :class="item.modul === 'EPU' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-emerald-100 text-emerald-700 border-emerald-200'" class="px-1.5 py-0.2 rounded text-[10px] font-bold border">
                                        <span x-text="item.modul"></span>
                                    </span>
                                    <p class="font-bold text-slate-800 truncate group-hover:text-emerald-600 transition-colors" x-text="item.nama_penternak"></p>
                                </div>
                                <p class="text-[11px] text-slate-500 truncate" x-text="item.nama_premis"></p>
                                <div class="flex items-center gap-2 text-[10px] text-slate-400">
                                    <span class="flex items-center gap-1"><i class="fa-solid fa-location-dot"></i> <span x-text="item.jajahan"></span></span>
                                    <span>•</span>
                                    <span class="font-semibold text-slate-600" x-text="item.jumlah_ternakan + (item.modul === 'EPU' ? ' ekor/kapasiti' : ' ekor')"></span>
                                </div>
                            </div>
                            <i class="fa-solid fa-crosshairs text-slate-300 group-hover:text-emerald-500 mt-1 text-xs"></i>
                        </div>
                    </template>
                    <div x-show="filteredMarkers.length === 0" class="py-6 text-center text-slate-400 text-xs">
                        <i class="fa-solid fa-map-pin text-2xl mb-1 text-slate-300 block"></i>
                        Tiada penternak sepadan dengan penapis.
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaflet GIS Map Container (8 Cols on Desktop, Fullscreen Mode Support) -->
        <div :class="isFullscreen ? 'fixed inset-0 z-[9999] bg-slate-950 p-3 flex flex-col h-screen w-screen overflow-hidden' : 'lg:col-span-8 bg-white rounded-2xl p-2 shadow-sm border border-slate-200 overflow-hidden relative'">
            
            <!-- Map Container -->
            <div id="gisMap" :class="isFullscreen ? 'w-full h-full flex-1 rounded-xl z-10' : 'w-full rounded-xl map-height z-10'"></div>

            <!-- Floating Top Left Controls on Map (Fullscreen, Layer Switcher, Focus) -->
            <div class="absolute top-4 left-4 z-[400] flex flex-wrap items-center gap-2">
                <button @click="toggleFullscreen()" type="button" class="px-3.5 py-2 bg-white/95 hover:bg-white text-slate-800 rounded-xl shadow-lg border border-slate-200 text-xs font-bold flex items-center gap-1.5 transition hover:scale-105 backdrop-blur-md">
                    <i :class="isFullscreen ? 'fa-solid fa-compress text-rose-500' : 'fa-solid fa-expand text-emerald-600'"></i>
                    <span x-text="isFullscreen ? 'Tutup Skrin Penuh (Esc)' : 'Skrin Penuh'"></span>
                </button>
                <button @click="toggleLayer()" type="button" class="px-3.5 py-2 bg-white/95 hover:bg-white text-slate-800 rounded-xl shadow-lg border border-slate-200 text-xs font-bold flex items-center gap-1.5 transition hover:scale-105 backdrop-blur-md">
                    <i class="fa-solid fa-layer-group text-sky-600"></i>
                    <span x-text="currentLayerName"></span>
                </button>
                <button @click="fitAllMarkers()" type="button" class="px-3.5 py-2 bg-white/95 hover:bg-white text-slate-800 rounded-xl shadow-lg border border-slate-200 text-xs font-bold flex items-center gap-1.5 transition hover:scale-105 backdrop-blur-md">
                    <i class="fa-solid fa-crosshairs text-amber-500"></i>
                    <span>Fokus</span>
                </button>
            </div>

            <!-- Floating Top Right Info & Quick Filter Bar in Fullscreen Mode -->
            <div x-show="isFullscreen" x-cloak class="absolute top-4 right-4 z-[400] flex items-center gap-2 bg-slate-900/95 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-slate-700 shadow-xl text-white text-xs">
                <span class="font-extrabold text-emerald-400" x-text="filteredMarkers.length + ' Penternak'"></span>
                <span class="text-slate-500">|</span>
                <button @click="selectedModul = 'ALL'; applyFilters()" :class="selectedModul === 'ALL' ? 'text-white font-bold bg-slate-800 px-2 py-0.5 rounded' : 'text-slate-400 hover:text-white px-1'">Semua</button>
                <button @click="selectedModul = 'EPU'; applyFilters()" :class="selectedModul === 'EPU' ? 'text-amber-400 font-bold bg-slate-800 px-2 py-0.5 rounded' : 'text-slate-400 hover:text-white px-1'">🐔 EPU</button>
                <button @click="selectedModul = 'EPTR'; applyFilters()" :class="selectedModul === 'EPTR' ? 'text-emerald-400 font-bold bg-slate-800 px-2 py-0.5 rounded' : 'text-slate-400 hover:text-white px-1'">🐄 EPTR</button>
                <span class="text-slate-500">|</span>
                <button @click="toggleFullscreen()" type="button" class="text-rose-400 hover:text-rose-300 font-bold ml-1 flex items-center gap-1">
                    <i class="fa-solid fa-xmark"></i> Keluar
                </button>
            </div>

            <!-- Floating Overlay Badge / Instructions (Normal Mode) -->
            <div x-show="!isFullscreen" class="absolute top-4 right-4 z-[400] bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-xl shadow-md border border-slate-200 text-xs font-semibold text-slate-700 flex items-center gap-2 pointer-events-none">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span>Klik mana-mana pin untuk butiran</span>
            </div>

            <!-- Selected Farm / Breeder Quick Bottom Drawer (Shown when Marker is active) -->
            <div x-show="selectedMarkerDetails" x-cloak 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute bottom-5 left-5 right-5 z-[400] bg-white/98 backdrop-blur-lg p-4 rounded-2xl shadow-2xl border border-slate-200 max-w-2xl mx-auto">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1 flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span :class="selectedMarkerDetails?.modul === 'EPU' ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-emerald-100 text-emerald-800 border-emerald-300'" class="px-2 py-0.5 rounded-full text-xs font-extrabold border">
                                <span x-text="selectedMarkerDetails?.modul_label"></span>
                            </span>
                            <span class="text-xs text-slate-500 font-medium flex items-center gap-1">
                                <i class="fa-solid fa-location-dot text-rose-500"></i>
                                <span x-text="selectedMarkerDetails?.jajahan + (selectedMarkerDetails?.daerah ? ' (' + selectedMarkerDetails?.daerah + ')' : '')"></span>
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 truncate" x-text="selectedMarkerDetails?.nama_penternak"></h3>
                        <p class="text-xs text-slate-600 truncate font-medium" x-text="selectedMarkerDetails?.nama_premis"></p>
                    </div>
                    <button @click="selectedMarkerDetails = null" type="button" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-3 pt-3 border-t border-slate-100 text-xs">
                    <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
                        <span class="block text-[10px] text-slate-400 font-bold uppercase">Jumlah Ternakan</span>
                        <span class="text-sm font-extrabold text-emerald-600" x-text="selectedMarkerDetails?.jumlah_ternakan + ' Ekor'"></span>
                    </div>
                    <div class="bg-slate-50 p-2 rounded-xl border border-slate-100">
                        <span class="block text-[10px] text-slate-400 font-bold uppercase">Jenis Ternakan</span>
                        <span class="text-xs font-bold text-slate-800 truncate block" x-text="selectedMarkerDetails?.jenis_ternakan_list?.join(', ') || '-'"></span>
                    </div>
                    <div class="bg-slate-50 p-2 rounded-xl border border-slate-100 col-span-2 sm:col-span-1">
                        <span class="block text-[10px] text-slate-400 font-bold uppercase">No. Telefon</span>
                        <span class="text-xs font-bold text-slate-800" x-text="selectedMarkerDetails?.no_telefon || '-'"></span>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="mt-3 flex items-center justify-end gap-2">
                    <a :href="selectedMarkerDetails?.url" target="_blank" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm flex items-center gap-1.5 transition">
                        <span>Buka Rekod Penuh</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Leaflet JS CDN -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
function gisMapApp() {
    return {
        map: null,
        markersGroup: null,
        rawMarkers: @json($allMarkers),
        filteredMarkers: [],
        
        // Filter States
        searchQuery: '',
        selectedModul: 'ALL', // 'ALL', 'EPU', 'EPTR'
        selectedJajahan: 'ALL',
        selectedLivestockTypes: [],
        
        // Selected Item for Drawer
        selectedMarkerDetails: null,

        // Fullscreen Mode State
        isFullscreen: false,

        // Map Layers
        currentLayerIndex: 0,
        layers: [],
        layerNames: ['Satelit (Esri)', 'Peta Terang (Carto)', 'Peta Standard (OSM)'],
        currentLayerName: 'Mod Satelit',

        toggleFullscreen() {
            this.isFullscreen = !this.isFullscreen;
            this.$nextTick(() => {
                setTimeout(() => {
                    if (this.map) {
                        this.map.invalidateSize();
                    }
                }, 200);
            });
        },

        initMap() {
            this.filteredMarkers = [...this.rawMarkers];

            // Kelantan Center: Lat ~ 5.65, Lng ~ 102.15, Zoom: 9
            this.map = L.map('gisMap', {
                center: [5.6500, 102.1500],
                zoom: 9,
                zoomControl: false, // Dinyahaktifkan dari topleft agar tidak terlindung oleh butang skrin penuh
            });

            // Letak butang zoom (+ / -) di sudut kanan bawah dengan reka bentuk kemas
            L.control.zoom({
                position: 'bottomright'
            }).addTo(this.map);

            // Definisi Base Tile Layers
            const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors | JPVNK GIS'
            });

            const cartoLightLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                attribution: '&copy; CARTO &copy; OpenStreetMap | JPVNK GIS'
            });

            const esriSatLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 18,
                attribution: 'Tiles &copy; Esri &mdash; JPVNK GIS'
            });

            this.layers = [osmLayer, esriSatLayer, cartoLightLayer];
            this.layers[0].addTo(this.map);

            this.markersGroup = L.featureGroup().addTo(this.map);

            this.renderMarkers();

            // Paksa Leaflet mengira semula saiz bekas (invalidateSize) secara bersiri agar peta dipaparkan 100% penuh pada kali pertama dimuatkan
            this.$nextTick(() => {
                const forceRedraw = () => {
                    if (this.map) {
                        this.map.invalidateSize();
                    }
                };

                forceRedraw();
                setTimeout(forceRedraw, 50);
                setTimeout(forceRedraw, 200);
                setTimeout(forceRedraw, 500);
                setTimeout(forceRedraw, 1000);
            });

            // Pasang ResizeObserver agar peta mengikut saiz bekas sebenar secara automatik apabila susun atur berubah
            const mapContainer = document.getElementById('gisMap');
            if (mapContainer && window.ResizeObserver) {
                const ro = new ResizeObserver(() => {
                    if (this.map) {
                        this.map.invalidateSize();
                    }
                });
                ro.observe(mapContainer);
            }

            window.addEventListener('resize', () => {
                if (this.map) {
                    this.map.invalidateSize();
                }
            });
        },

        toggleLayer() {
            this.map.removeLayer(this.layers[this.currentLayerIndex]);
            this.currentLayerIndex = (this.currentLayerIndex + 1) % this.layers.length;
            this.layers[this.currentLayerIndex].addTo(this.map);
            this.currentLayerName = this.layerNames[this.currentLayerIndex];
        },

        fitAllMarkers() {
            if (this.markersGroup && this.markersGroup.getLayers().length > 0) {
                this.map.fitBounds(this.markersGroup.getBounds().pad(0.1));
            } else {
                this.map.setView([5.6500, 102.1500], 9);
            }
        },

        toggleLivestockType(type) {
            const index = this.selectedLivestockTypes.indexOf(type);
            if (index > -1) {
                this.selectedLivestockTypes.splice(index, 1);
            } else {
                this.selectedLivestockTypes.push(type);
            }
            this.applyFilters();
        },

        resetFilters() {
            this.searchQuery = '';
            this.selectedModul = 'ALL';
            this.selectedJajahan = 'ALL';
            this.selectedLivestockTypes = [];
            this.selectedMarkerDetails = null;
            this.applyFilters();
            this.fitAllMarkers();
        },

        applyFilters() {
            const query = this.searchQuery.toLowerCase().trim();
            const modul = this.selectedModul;
            const jajahan = this.selectedJajahan;
            const types = this.selectedLivestockTypes.map(t => t.toLowerCase());

            this.filteredMarkers = this.rawMarkers.filter(item => {
                // 1. Modul Filter
                if (modul !== 'ALL' && item.modul !== modul) {
                    return false;
                }

                // 2. Jajahan Filter
                if (jajahan !== 'ALL' && item.jajahan.toLowerCase() !== jajahan.toLowerCase()) {
                    return false;
                }

                // 3. Livestock Types Filter
                if (types.length > 0) {
                    const itemTypes = (item.jenis_ternakan_list || []).map(t => t.toLowerCase());
                    const matchesType = types.some(t => itemTypes.some(it => it.includes(t) || t.includes(it)));
                    if (!matchesType) {
                        return false;
                    }
                }

                // 4. Text Search Query
                if (query) {
                    const name = (item.nama_penternak || '').toLowerCase();
                    const premis = (item.nama_premis || '').toLowerCase();
                    const jName = (item.jajahan || '').toLowerCase();
                    const dName = (item.daerah || '').toLowerCase();
                    const ic = (item.no_kp || '').toLowerCase();
                    const ssm = (item.no_syarikat_ssm || '').toLowerCase();
                    const tList = (item.jenis_ternakan_list || []).join(' ').toLowerCase();

                    const matchesQuery = name.includes(query) || 
                                         premis.includes(query) || 
                                         jName.includes(query) || 
                                         dName.includes(query) || 
                                         ic.includes(query) || 
                                         ssm.includes(query) ||
                                         tList.includes(query);
                    if (!matchesQuery) {
                        return false;
                    }
                }

                return true;
            });

            this.renderMarkers();
        },

        renderMarkers() {
            this.markersGroup.clearLayers();

            this.filteredMarkers.forEach(item => {
                const isEpu = (item.modul === 'EPU');
                const markerColor = isEpu ? '#f59e0b' : '#059669'; // Amber vs Emerald
                const markerBg = isEpu ? '#fffbeb' : '#ecfdf5';
                const emoji = isEpu ? '🐔' : (item.has_pawah ? '🤝' : '🐄');

                // Custom HTML Marker Icon
                const customIcon = L.divIcon({
                    className: 'custom-marker-icon',
                    html: `
                        <div style="transform: translate(-50%, -100%); cursor: pointer;">
                            <div style="background: ${markerColor}; color: white; width: 34px; height: 34px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3); border: 2px solid white;">
                                <span style="transform: rotate(45deg); font-size: 15px;">${emoji}</span>
                            </div>
                        </div>
                    `,
                    iconSize: [34, 34],
                    iconAnchor: [17, 34],
                    popupAnchor: [0, -32]
                });

                const marker = L.marker([item.lat, item.lng], { icon: customIcon });

                // Construct Rich Popup HTML
                const pecahanHtml = Object.entries(item.pecahan_ternakan || {}).map(([name, count]) => {
                    return `<span class="inline-block bg-slate-100 text-slate-700 font-semibold px-2 py-0.5 rounded text-[11px] mr-1 mb-1 border border-slate-200">${name}: <b>${count}</b></span>`;
                }).join('');

                const popupHtml = `
                    <div style="width: 280px; font-family: inherit;">
                        <div style="background: ${isEpu ? '#f59e0b' : '#059669'}; color: white; padding: 10px 14px; border-radius: 12px 12px 0 0;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">${item.modul_label}</span>
                                <span style="font-size: 11px; opacity: 0.9;">📍 ${item.jajahan}</span>
                            </div>
                            <div style="font-size: 14px; font-weight: 800; margin-top: 4px; line-height: 1.3;">${item.nama_penternak}</div>
                        </div>
                        <div style="padding: 12px 14px; background: white; font-size: 12px; color: #334155;">
                            <div style="margin-bottom: 8px;">
                                <div style="font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase;">Premis / Lokasi:</div>
                                <div style="font-weight: 700; color: #0f172a;">${item.nama_premis}</div>
                                <div style="font-size: 11px; color: #64748b;">${item.alamat}</div>
                            </div>
                            
                            <div style="margin-bottom: 10px; padding: 8px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <div style="font-size: 10px; color: #64748b; font-weight: 700; text-transform: uppercase;">Jumlah &amp; Jenis Ternakan:</div>
                                <div style="font-size: 14px; font-weight: 800; color: ${isEpu ? '#d97706' : '#059669'}; margin-bottom: 4px;">
                                    ${item.jumlah_ternakan.toLocaleString()} ${isEpu ? 'Ekor (Kapasiti)' : 'Ekor'}
                                </div>
                                <div>${pecahanHtml}</div>
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 6px; border-top: 1px solid #f1f5f9;">
                                <span style="font-size: 11px; color: #64748b;">📞 ${item.no_telefon || '-'}</span>
                                <a href="${item.url}" target="_blank" style="background: ${isEpu ? '#f59e0b' : '#059669'}; color: white; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; text-decoration: none; display: inline-block;">
                                    Lihat Butiran &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupHtml);

                marker.on('click', () => {
                    this.selectedMarkerDetails = item;
                });

                this.markersGroup.addLayer(marker);
            });
        },

        focusOnMarker(item) {
            this.selectedMarkerDetails = item;
            this.map.setView([item.lat, item.lng], 14, { animate: true });
            
            // Cari layer yang sepadan dan buka popup
            this.markersGroup.eachLayer(layer => {
                const latLng = layer.getLatLng();
                if (Math.abs(latLng.lat - item.lat) < 0.0001 && Math.abs(latLng.lng - item.lng) < 0.0001) {
                    layer.openPopup();
                }
            });
        },

        // Computed stats
        get countFilteredEpu() {
            return this.filteredMarkers.filter(m => m.modul === 'EPU').length;
        },
        get countFilteredEptr() {
            return this.filteredMarkers.filter(m => m.modul === 'EPTR').length;
        },
        get totalFilteredUnggas() {
            return this.filteredMarkers.filter(m => m.modul === 'EPU').reduce((sum, m) => sum + (m.jumlah_ternakan || 0), 0);
        },
        get totalFilteredRuminan() {
            return this.filteredMarkers.filter(m => m.modul === 'EPTR').reduce((sum, m) => sum + (m.jumlah_ternakan || 0), 0);
        },
        get topJajahanName() {
            if (this.filteredMarkers.length === 0) return '-';
            const counts = {};
            this.filteredMarkers.forEach(m => {
                counts[m.jajahan] = (counts[m.jajahan] || 0) + 1;
            });
            let maxJajahan = '-';
            let maxCount = 0;
            for (const [j, c] of Object.entries(counts)) {
                if (c > maxCount) {
                    maxCount = c;
                    maxJajahan = j;
                }
            }
            return maxJajahan + ' (' + maxCount + ')';
        }
    };
}
</script>
@endpush
