<?php
require_once 'config/database.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$user_id = $_SESSION['user_id'];

// Proses simpan alamat baru jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_alamat'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $prov = mysqli_real_escape_string($conn, $_POST['provinsi']);
    $kota = mysqli_real_escape_string($conn, $_POST['kota']);
    $kec = mysqli_real_escape_string($conn, $_POST['kecamatan']);
    $detail = mysqli_real_escape_string($conn, $_POST['detail_alamat']);
    $label = mysqli_real_escape_string($conn, $_POST['label']);

    // Set alamat lain jadi tidak utama dulu
    mysqli_query($conn, "UPDATE alamat SET is_utama=0 WHERE user_id=$user_id");
    mysqli_query($conn, "INSERT INTO alamat (user_id, nama_lengkap, no_telp, provinsi, kota, kecamatan, detail_alamat, label, is_utama) VALUES ($user_id, '$nama', '$telp', '$prov', '$kota', '$kec', '$detail', '$label', 1)");
    header("Location: pembayaran.php");
    exit;
}

// Ambil Alamat Utama User
$alamat_q = mysqli_query($conn, "SELECT * FROM alamat WHERE user_id=$user_id AND is_utama=1 LIMIT 1");
$alamat = mysqli_fetch_assoc($alamat_q);

include_once 'includes/header.php';
?>
<div class="max-w-6xl mx-auto w-full pt-8 px-4 flex-grow mb-16">
    <div class="text-xs text-slate-400 mb-2">Beranda / <span class="text-teal-dark">Pembayaran</span></div>
    <h1 class="text-2xl font-bold text-teal-dark mb-6 tracking-wide">PEMBAYARAN</h1>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <div class="w-full lg:w-2/3 space-y-6">
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex justify-between items-start">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 tracking-wide uppercase mb-1">Alamat Pengiriman</h3>
                    <?php if($alamat): ?>
                        <p class="text-xs font-bold text-slate-800"><?= htmlspecialchars($alamat['label']) ?> - <?= htmlspecialchars($alamat['nama_lengkap']) ?></p>
                        <p class="text-[11px] text-slate-500 mt-1"><?= htmlspecialchars($alamat['detail_alamat']) ?>, <?= htmlspecialchars($alamat['kecamatan']) ?>, <?= htmlspecialchars($alamat['kota']) ?></p>
                    <?php else: ?>
                        <p class="text-xs text-red-500 italic">Belum ada alamat pengiriman utama.</p>
                    <?php endif; ?>
                </div>
                <button onclick="toggleModal('modal-pilih-alamat', true)" class="text-[10px] border border-slate-300 rounded-lg px-3 py-1.5 font-medium text-slate-600 hover:bg-slate-50 transition">GANTI ALAMAT</button>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-400 tracking-wide uppercase">Order List</h3>
                <div class="flex justify-between items-center bg-slate-50 p-4 rounded-xl">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-teal-800 rounded-lg flex items-center justify-center text-white font-bold text-xs">COFFEE</div>
                        <div>
                            <p class="font-bold text-sm text-slate-800">Coffe Soda</p>
                            <p class="text-[10px] text-slate-400">2x Coffe Soda</p>
                            <button class="text-[10px] border px-2 py-0.5 rounded mt-1 bg-white">EDIT</button>
                        </div>
                    </div>
                    <span class="font-bold text-sm text-slate-800">Rp. 40.000</span>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/3 space-y-4">
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-800 border-b pb-2">Detail Pembayaran</h3>
                
                <div onclick="toggleModal('modal-payment-method', true)" class="border p-3 rounded-xl flex justify-between items-center text-xs text-slate-600 cursor-pointer hover:bg-slate-50 transition">
                    <span class="flex items-center gap-2">💳 <span id="selected-method">Pilih Metode Pembayaran</span></span>
                    <span class="text-slate-400">PILIH ❯</span>
                </div>

                <div onclick="toggleModal('modal-voucher-list', true)" class="border p-3 rounded-xl flex justify-between items-center text-xs text-slate-600 cursor-pointer hover:bg-slate-50 transition">
                    <span class="flex items-center gap-2">🏷️ <span id="selected-voucher">Ambil Voucher atau Klaim Kupon</span></span>
                    <span class="text-slate-400">PILIH ❯</span>
                </div>

                <div class="space-y-2 text-xs text-slate-600 border-t pt-3">
                    <div class="flex justify-between"><span>SubTotal Produk</span><span>Rp40.000</span></div>
                    <div class="flex justify-between"><span>Pajak</span><span>Rp15.000</span></div>
                    <div class="flex justify-between"><span>Ongkir</span><span>Rp5.000</span></div>
                    <div id="box-diskon" class="flex justify-between text-green-600 hidden"><span>Potongan Voucher</span><span id="val-diskon">-Rp0</span></div>
                </div>
                
                <div class="border-t border-dashed pt-3 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase">TOTAL</p>
                        <p id="total-bayar" class="font-bold text-xl text-teal-dark">Rp60.000</p>
                    </div>
                    <button onclick="toggleModal('modal-sukses-pembayaran', true)" class="btn-primary text-xs font-bold px-5 py-3 tracking-wider">PESAN SEKARANG</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-payment-method" class="fixed inset-0 bg-black/60 z-50 flex justify-end hidden transition-all">
    <div class="bg-white w-full max-w-sm h-full p-6 flex flex-col justify-between shadow-2xl">
        <div class="space-y-6">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-slate-800 tracking-wide text-sm">METODE PEMBAYARAN</h3>
                <button onclick="toggleModal('modal-payment-method', false)" class="text-slate-400 font-bold text-lg">✕</button>
            </div>
            <div class="space-y-3">
                <p class="text-[10px] font-bold text-slate-400 tracking-wider">PEMBAYARAN CEPAT</p>
                <label class="flex justify-between p-3.5 border rounded-xl items-center cursor-pointer hover:bg-slate-50">
                    <span class="text-xs font-bold text-slate-700 flex items-center gap-2">🟢 GOPAY</span>
                    <input type="radio" name="pay_opt" value="GOPAY" checked class="accent-teal-700">
                </label>
                <label class="flex justify-between p-3.5 border rounded-xl items-center cursor-pointer hover:bg-slate-50">
                    <span class="text-xs font-bold text-slate-700 flex items-center gap-2">🟠 SHOPEEPAY</span>
                    <input type="radio" name="pay_opt" value="SHOPEEPAY" class="accent-teal-700">
                </label>
                <label class="flex justify-between p-3.5 border rounded-xl items-center cursor-pointer hover:bg-slate-50">
                    <span class="text-xs font-bold text-slate-700 flex items-center gap-2">🔵 DANA</span>
                    <input type="radio" name="pay_opt" value="DANA" class="accent-teal-700">
                </label>
            </div>
        </div>
        <button onclick="pilihPayment()" class="w-full btn-primary py-3.5 text-xs font-bold tracking-widest">KONFIRMASI</button>
    </div>
</div>

<div id="modal-voucher-list" class="fixed inset-0 bg-black/60 z-50 flex justify-end hidden">
    <div class="bg-white w-full max-w-sm h-full p-6 flex flex-col justify-between">
        <div class="space-y-6">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="font-bold text-slate-800 text-sm">VOUCHER SAYA</h3>
                <button onclick="toggleModal('modal-voucher-list', false)" class="text-slate-400 font-bold">✕</button>
            </div>
            <div class="space-y-3">
                <div class="border border-teal-600/30 bg-teal-50/20 p-4 rounded-xl relative">
                    <p class="font-bold text-xs text-teal-900">VOUCHER DISKON 20%</p>
                    <p class="text-[10px] text-slate-400 mt-1">Berlaku sampai 31 Des 2026</p>
                    <button onclick="pilihVoucher(20, 'DISKON 20%')" class="absolute right-4 top-4 text-[10px] bg-teal-dark text-white px-3 py-1 rounded-md font-bold">PILIH</button>
                </div>
            </div>
        </div>
        <button onclick="toggleModal('modal-voucher-list', false)" class="w-full btn-primary py-3.5 text-xs font-bold tracking-widest">KONFIRMASI</button>
    </div>
</div>

<div id="modal-pilih-alamat" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl max-h-[85vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b pb-2">
            <h3 class="font-bold text-slate-800 text-sm">Alamat Pengiriman</h3>
            <button onclick="toggleModal('modal-pilih-alamat', false)" class="text-slate-400 font-bold">✕</button>
        </div>
        
        <div class="space-y-2">
            <div class="border-2 border-teal-600 p-3 rounded-xl bg-teal-50/10 relative">
                <span class="absolute top-3 right-3 text-teal-700 text-xs font-bold">✓ Utama</span>
                <p class="text-xs font-bold text-slate-800">Rumah - Alvariezi</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Jln. pahlawan nomer 30, Sumberejo, Banyuwangi</p>
            </div>
        </div>
        
        <div class="flex gap-2 pt-2">
            <button onclick="toggleModal('modal-pilih-alamat', false); toggleModal('modal-tambah-alamat', true)" class="w-full border border-slate-300 text-slate-700 py-2.5 text-xs font-bold rounded-xl hover:bg-slate-50">+ TAMBAH ALAMAT BARU</button>
            <button onclick="toggleModal('modal-pilih-alamat', false)" class="w-full btn-primary py-2.5 text-xs font-bold">SIMPAN</button>
        </div>
    </div>
</div>

<div id="modal-tambah-alamat" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 hidden">
    <form method="POST" action="" class="bg-white rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl">
        <h3 class="font-bold text-slate-800 text-sm border-b pb-2 tracking-wide">TAMBAH ALAMAT PENGIRIMAN BARU</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="text-[10px] font-bold text-slate-400 uppercase">Nama Lengkap</label><input type="text" name="nama_lengkap" required class="w-full border p-2 text-xs rounded-lg mt-1 outline-none focus:border-teal-dark"></div>
            <div><label class="text-[10px] font-bold text-slate-400 uppercase">Provinsi</label><input type="text" name="provinsi" required class="w-full border p-2 text-xs rounded-lg mt-1 outline-none focus:border-teal-dark"></div>
            <div><label class="text-[10px] font-bold text-slate-400 uppercase">Kota</label><input type="text" name="kota" required class="w-full border p-2 text-xs rounded-lg mt-1 outline-none focus:border-teal-dark"></div>
            <div><label class="text-[10px] font-bold text-slate-400 uppercase">Kecamatan</label><input type="text" name="kecamatan" required class="w-full border p-2 text-xs rounded-lg mt-1 outline-none focus:border-teal-dark"></div>
            <div class="md:col-span-2"><label class="text-[10px] font-bold text-slate-400 uppercase">Nomor Telepon</label><input type="text" name="no_telp" required class="w-full border p-2 text-xs rounded-lg mt-1 outline-none focus:border-teal-dark"></div>
            <div class="md:col-span-2"><label class="text-[10px] font-bold text-slate-400 uppercase">Alamat Lengkap</label><textarea name="detail_alamat" required rows="2" class="w-full border p-2 text-xs rounded-lg mt-1 outline-none focus:border-teal-dark"></textarea></div>
            <div class="md:col-span-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Pilih Label Untuk Pengiriman Efektif</label>
                <select name="label" class="border p-2 text-xs rounded-lg outline-none">
                    <option value="Rumah">🏠 Rumah</option>
                    <option value="Kantor">🏢 Kantor</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t">
            <button type="button" onclick="toggleModal('modal-tambah-alamat', false)" class="px-4 py-2 text-xs font-bold bg-slate-100 rounded-xl text-slate-600">BATAL</button>
            <button type="submit" name="tambah_alamat" class="px-5 py-2 text-xs font-bold bg-teal-dark text-white rounded-xl">SIMPAN</button>
        </div>
    </form>
</div>

<div id="modal-sukses-pembayaran" class="fixed inset-0 bg-teal-dark/95 z-50 flex items-center justify-center p-4 hidden">
    <div class="text-center text-white space-y-5 max-w-sm w-full animate-bounce-short">
        <div class="w-20 h-20 bg-white text-teal-900 rounded-full flex items-center justify-center text-4xl mx-auto shadow-2xl font-bold">✓</div>
        <h2 class="text-2xl font-bold tracking-wider">Pembayaran Berhasil</h2>
        <p class="text-xs text-slate-300">Horee! Pembayaranmu sudah selesai.</p>
        <div class="border-t border-white/20 pt-4">
            <p class="text-[11px] text-teal-200 tracking-wider uppercase font-medium">JUMLAH PEMBAYARAN</p>
            <p id="success-amount" class="text-2xl font-bold mt-1">Rp. 60.000</p>
        </div>
        <button onclick="window.location.href='status_pemesanan.php'" class="mt-6 bg-white text-teal-900 font-bold text-xs px-8 py-3 rounded-full shadow-lg">LIHAT STATUS PESANAN</button>
    </div>
</div>

<script>
function toggleModal(id, show) {
    document.getElementById(id).classList.toggle('hidden', !show);
}

function pilihPayment() {
    const selected = document.querySelector('input[name="pay_opt"]:checked').value;
    document.getElementById('selected-method').innerText = "Metode: " + selected;
    toggleModal('modal-payment-method', false);
}

function pilihVoucher(potonganPersen, namaVoucher) {
    let subtotal = 40000;
    let pajak = 15000;
    let ongkir = 5000;
    
    let diskon = (potonganPersen / 100) * subtotal;
    let totalBaru = (subtotal + pajak + ongkir) - diskon;
    
    document.getElementById('box-diskon').classList.remove('hidden');
    document.getElementById('val-diskon').innerText = "-Rp" + diskon.toLocaleString('id-ID');
    document.getElementById('total-bayar').innerText = "Rp" + totalBaru.toLocaleString('id-ID');
    document.getElementById('success-amount').innerText = "Rp. " + totalBaru.toLocaleString('id-ID');
    document.getElementById('selected-voucher').innerText = "Voucher: " + namaVoucher;
    
    toggleModal('modal-voucher-list', false);
}
</script>
<?php include_once 'includes/footer.php'; ?>