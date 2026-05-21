<?php
require_once 'config/database.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$user_id = $_SESSION['user_id'];
$q = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$u = mysqli_fetch_assoc($q);

$updated = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $telp = mysqli_real_escape_string($conn, $_POST['telp']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    
    mysqli_query($conn, "UPDATE users SET nama='$nama', no_telp='$telp', gender='$gender' WHERE id=$user_id");
    $_SESSION['nama'] = $nama;
    $updated = true;
}
include_once 'includes/header.php';
?>
<div class="max-w-5xl mx-auto w-full pt-8 px-4 flex-grow mb-16">
    <h1 class="text-2xl font-bold text-teal-dark mb-6">PROFIL SAYA</h1>
    <form method="POST" action="" class="flex flex-col md:flex-row gap-8">
        <div class="w-full md:w-1/3 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-center space-y-4 h-max">
            <div class="w-20 h-20 bg-teal-800 text-white text-3xl font-bold rounded-full flex items-center justify-center mx-auto">👤</div>
            <p class="text-xs font-bold text-slate-700">Upload Gambar (JPG/PNG)</p>
            <button type="button" class="text-[10px] border px-3 py-1 rounded">Pilih Gambar</button>
            <hr>
            <button type="button" class="w-full border p-2 text-xs font-bold rounded text-slate-600">🔒 GANTI PIN</button>
        </div>

        <div class="w-full md:w-2/3 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-5">
            <div><label class="text-[11px] text-slate-400 block font-bold">Nama Lengkap</label><input type="text" name="nama" value="<?= htmlspecialchars($u['nama']) ?>" class="w-full border-b py-2 text-sm outline-none focus:border-teal-dark"></div>
            <div><label class="text-[11px] text-slate-400 block font-bold">Nomor Telepon</label><input type="text" name="telp" value="<?= htmlspecialchars($u['no_telp'] ?? '081XXXXXXXXX') ?>" class="w-full border-b py-2 text-sm outline-none focus:border-teal-dark"></div>
            <div><label class="text-[11px] text-slate-400 block font-bold">Email</label><input type="text" readonly value="<?= htmlspecialchars($u['email']) ?>" class="w-full border-b py-2 text-sm bg-slate-50 text-slate-400 outline-none"></div>
            <div>
                <label class="text-[11px] text-slate-400 block font-bold mb-2">Gender</label>
                <div class="flex gap-4 text-xs">
                    <label class="flex items-center gap-1"><input type="radio" name="gender" value="Laki-laki" <?= (($u['gender'] ?? 'Laki-laki') == 'Laki-laki') ? 'checked' : '' ?> class="accent-teal-700"> Laki - Laki</label>
                    <label class="flex items-center gap-1"><input type="radio" name="gender" value="Perempuan" <?= (($u['gender'] ?? '') == 'Perempuan') ? 'checked' : '' ?> class="accent-teal-700"> Perempuan</label>
                </div>
            </div>
            <div><label class="text-[11px] text-slate-400 block font-bold">Tanggal Lahir</label><input type="text" placeholder="DD / MM / YYYY" class="w-full border-b py-2 text-sm outline-none"></div>
            
            <button type="submit" name="save_profile" class="w-full btn-primary py-3 text-xs font-bold uppercase tracking-wider">Simpan Perubahan</button>
        </div>
    </form>
</div>

<div id="modal-sukses-profil" class="fixed inset-0 bg-teal-950/90 z-50 flex items-center justify-center p-4 <?= $updated ? '' : 'hidden' ?>">
    <div class="text-center text-white space-y-4 max-w-sm w-full">
        <div class="w-20 h-20 bg-white text-teal-800 rounded-full flex items-center justify-center text-4xl mx-auto shadow-xl font-bold">✓</div>
        <h2 class="text-2xl font-bold uppercase tracking-wider">Profil Berhasil Diubah</h2>
        <p class="text-xs text-slate-300">Horee! Profilmu Sudah Berhasil Diganti.</p>
        <button onclick="window.location.href='profil.php'" class="mt-6 bg-white text-teal-900 font-bold text-xs px-6 py-2.5 rounded-full">OKEY</button>
    </div>
</div>
<?php include_once 'includes/footer.php'; ?>