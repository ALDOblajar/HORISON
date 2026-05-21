<?php
require_once 'config/database.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
include_once 'includes/header.php';
?>
<div class="max-w-4xl mx-auto w-full pt-8 px-4 flex-grow mb-16">
    <div class="text-xs text-slate-400 mb-2">Beranda / <span class="text-teal-dark">Status Pemesanan</span></div>
    <h1 class="text-2xl font-bold text-teal-dark mb-6 tracking-wide">STATUS PEMESANAN</h1>

    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm space-y-6 relative overflow-hidden">
        <span class="absolute top-6 right-6 bg-teal-50 text-teal-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
            🚚 Sedang Dikirim
        </span>

        <div class="flex items-center gap-5 border-b pb-6">
            <div class="w-16 h-16 bg-teal-800 rounded-xl flex items-center justify-center text-white font-bold text-xs shadow-inner">COFFEE</div>
            <div>
                <h3 class="font-bold text-base text-slate-800">Coffe Soda</h3>
                <p class="text-xs text-slate-400 mt-0.5">2x Coffe Soda</p>
                <p class="text-xs font-bold text-teal-dark mt-2">Rp. 40.000</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50 p-4 rounded-xl">
            <div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">TOTAL PEMESANAN</p>
                <p class="text-lg font-bold text-slate-800">Rp. 50.000</p>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button onclick="alert('Resi: HORISON-BWI-09823123')" class="w-full sm:w-auto text-xs font-bold border border-slate-300 text-slate-700 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 transition">HUBUNGI PENJUAL</button>
                <button onclick="window.location.href='keranjang.php'" class="w-full sm:w-auto text-xs font-bold btn-primary px-5 py-2.5 shadow-md">BATALKAN PESANAN</button>
            </div>
        </div>
    </div>
</div>
<?php include_once 'includes/footer.php'; ?>