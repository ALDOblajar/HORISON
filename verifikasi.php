<?php
require_once 'config/database.php';
$email = $_SESSION['temp_email'] ?? 'Alvariezi12@gmail.com';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");
    if (mysqli_num_rows($q) > 0) {
        $u = mysqli_fetch_assoc($q);
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['nama'] = $u['nama'];
    }
    header("Location: keranjang.php");
    exit;
}
include_once 'includes/header.php';
?>
<div class="bg-teal-dark text-white py-4 px-6 flex items-center shadow-md">
    <a href="register.php" class="text-xl">←</a>
    <h1 class="text-lg font-medium mx-auto">Verifikasi</h1>
</div>
<div class="flex-grow flex items-center justify-center p-6 my-12">
    <form method="POST" class="bg-white p-10 rounded-2xl shadow-sm border border-slate-200 text-center w-full max-w-xl space-y-6">
        <h2 class="text-3xl font-bold text-slate-900">Verifikasi Akun Anda</h2>
        <p class="text-gray-500 text-sm">Masukkan kode verifikasi yang dikirim ke email Anda sample <br><span class="font-semibold text-slate-800"><?= htmlspecialchars($email) ?></span></p>
        <div class="flex justify-center gap-4 py-4">
            <input type="text" value="1" class="w-14 h-16 border rounded-xl text-center text-2xl font-bold border-teal-dark">
            <input type="text" value="2" class="w-14 h-16 border rounded-xl text-center text-2xl font-bold border-teal-dark">
            <input type="text" value="2" class="w-14 h-16 border rounded-xl text-center text-2xl font-bold border-teal-dark">
            <input type="text" value="1" class="w-14 h-16 border rounded-xl text-center text-2xl font-bold border-teal-dark">
        </div>
        <button type="submit" class="w-full max-w-xs mx-auto btn-primary py-3.5 font-semibold text-sm block">SELANJUTNYA</button>
        <p class="text-xs text-slate-400">Tidak menerima kode? <span class="text-teal-700 font-medium">00.20</span> <a href="#" class="font-bold text-slate-600">Kirim Ulang</a></p>
    </form>
</div>
<?php include_once 'includes/footer.php'; ?>