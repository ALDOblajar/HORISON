<?php
require_once 'config/database.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
include_once 'includes/header.php';
?>
<div class="max-w-6xl mx-auto w-full pt-8 px-4 flex-grow mb-16">
    <h1 class="text-2xl font-bold text-teal-dark mb-6">Keranjang</h1>
    <div class="flex flex-col lg:flex-row gap-8">
        
        <div class="w-full lg:w-2/3 bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-6">
            <div class="flex justify-between font-semibold text-sm text-slate-500 border-b pb-2">
                <span>Detail Produk</span>
                <span>Total Harga</span>
            </div>
            
            <div class="flex items-center justify-between border-b pb-4">
                <div class="flex items-center gap-4">
                    <input type="checkbox" id="check-coffe" onchange="hitungTotal()" class="accent-teal-700 w-4 h-4 item-checkbox" data-harga="50000">
                    <div class="w-14 h-14 bg-slate-100 rounded-lg flex items-center justify-center font-bold text-xs text-slate-400">COFFEE</div>
                    <div>
                        <h4 class="font-bold text-slate-800">Coffe Soda</h4>
                        <p class="text-xs text-red-500 font-semibold">Rp. 25.000</p>
                        <div class="flex items-center gap-2 mt-1 text-xs">
                            <button class="px-1.5 bg-slate-100 rounded">-</button>
                            <span>2</span>
                            <button class="px-1.5 bg-slate-100 rounded">+</button>
                        </div>
                    </div>
                </div>
                <span class="font-bold text-sm text-slate-800">Rp. 50.000</span>
            </div>

            <div class="flex items-center justify-between border-b pb-4">
                <div class="flex items-center gap-4">
                    <input type="checkbox" onchange="hitungTotal()" class="accent-teal-700 w-4 h-4 item-checkbox" data-harga="25000">
                    <div class="w-14 h-14 bg-slate-100 rounded-lg flex items-center justify-center font-bold text-xs text-slate-400">LEMON</div>
                    <div>
                        <h4 class="font-bold text-slate-800">Lemon Wizz</h4>
                        <p class="text-xs text-red-500 font-semibold">Rp. 25.000</p>
                    </div>
                </div>
                <span class="font-bold text-sm text-slate-800">Rp. 25.000</span>
            </div>

            <div class="flex items-center justify-between border-b pb-4 p-1 rounded-xl transition hover:bg-slate-50">
                <div class="flex items-center gap-4">
                    <input type="checkbox" onchange="hitungTotal()" class="accent-teal-700 w-4 h-4 item-checkbox" data-harga="25000">
                    <div onclick="toggleModal('modal-temulawak', true)" class="w-14 h-14 bg-amber-100 rounded-lg flex items-center justify-center font-bold text-xs text-amber-600 cursor-pointer">JAMU</div>
                    <div onclick="toggleModal('modal-temulawak', true)" class="cursor-pointer">
                        <h4 class="font-bold text-slate-800 flex items-center gap-2">Temulawak <span class="text-[9px] bg-amber-500 text-white px-1.5 py-0.5 rounded-full">Detail UI</span></h4>
                        <p class="text-xs text-red-500 font-semibold">Rp. 25.000</p>
                    </div>
                </div>
                <span class="font-bold text-sm text-slate-800">Rp. 25.000</span>
            </div>

            <button class="text-teal-700 text-xs font-bold flex items-center gap-1 mt-4">← Lanjutkan Belanja</button>
        </div>

        <div class="w-full lg:w-1/3 bg-white border border-slate-100 rounded-2xl p-6 shadow-sm h-max space-y-4">
            <h3 class="text-base font-bold text-teal-dark border-b pb-2">Ringkasan Pesanan</h3>
            <div class="flex justify-between text-xs text-slate-600"><span>Subtotal</span><span id="txt-subtotal">Rp. 0</span></div>
            <div class="flex justify-between text-xs text-slate-600"><span>Diskon</span><span>0%</span></div>
            <div class="flex justify-between text-xs text-slate-600"><span>Shipping</span><span>Free</span></div>
            <div class="border-t pt-3 flex justify-between font-bold text-sm text-slate-800"><span>Total</span><span id="txt-total">Rp. 0</span></div>
            
            <button id="btn-checkout" onclick="window.location.href='pembayaran.php'" disabled class="w-full bg-slate-300 text-white text-center py-3 text-xs font-bold tracking-wider rounded-full cursor-not-allowed transition">
                Checkout
            </button>
        </div>
    </div>
</div>

<div id="modal-temulawak" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full overflow-hidden relative flex flex-col md:flex-row">
        <button onclick="toggleModal('modal-temulawak', false)" class="absolute top-3 right-3 text-white bg-black/40 hover:bg-black/70 rounded-full w-7 h-7 flex items-center justify-center font-bold">✕</button>
        <div class="w-full md:w-1/2 bg-amber-50 flex items-center justify-center p-6">
            <div class="w-full h-56 bg-amber-500 rounded-xl flex items-center justify-center text-white font-bold">Botol Temulawak Image</div>
        </div>
        <div class="w-full md:w-1/2 p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">Temulawak</h3>
                <p class="text-xs font-bold text-red-500 mt-1">Rp. 25.000</p>
                <p class="text-[11px] text-slate-500 mt-3 leading-relaxed">Minuman tradisional Indonesia yang dibuat dari rimpang temulawak (Curcuma xanthorrhiza) murni pilihan terbaik.</p>
            </div>
            <button onclick="toggleModal('modal-temulawak', false)" class="w-full btn-primary py-2 text-xs font-semibold mt-4">🛒 Pilih</button>
        </div>
    </div>
</div>

<script>
function toggleModal(id, show) {
    document.getElementById(id).classList.toggle('hidden', !show);
}

function hitungTotal() {
    let subtotal = 0;
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => {
        if(cb.checked) subtotal += parseInt(cb.getAttribute('data-harga'));
    });
    
    document.getElementById('txt-subtotal').innerText = 'Rp. ' + subtotal.toLocaleString('id-ID');
    document.getElementById('txt-total').innerText = 'Rp. ' + subtotal.toLocaleString('id-ID');
    
    const btnCheck = document.getElementById('btn-checkout');
    if(subtotal > 0) {
        btnCheck.disabled = false;
        btnCheck.className = "w-full btn-primary text-center py-3 text-xs font-bold tracking-wider rounded-full cursor-pointer";
    } else {
        btnCheck.disabled = true;
        btnCheck.className = "w-full bg-slate-300 text-white text-center py-3 text-xs font-bold tracking-wider rounded-full cursor-not-allowed";
    }
}
</script>
<?php include_once 'includes/footer.php'; ?>