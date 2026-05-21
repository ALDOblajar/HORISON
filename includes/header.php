<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horison Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .bg-teal-dark { background-color: #004d61; }
        .text-teal-dark { color: #004d61; }
        .border-teal-dark { border-color: #004d61; }
        .btn-primary { background-color: #004d61; color: white; border-radius: 9999px; transition: all 0.3s ease; }
        .btn-primary:hover { background-color: #003745; transform: translateY(-1px); }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-slate-50">

<?php if (isset($_SESSION['user_id'])): ?>
<nav class="bg-white border-b border-slate-100 px-6 py-4 shadow-sm">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
        <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='keranjang.php'">
            <div class="w-8 h-8 bg-teal-dark rounded-full flex items-center justify-center text-white font-bold text-sm">H</div>
            <span class="font-bold text-lg text-teal-dark tracking-wider">HORISON</span>
        </div>
        <div class="flex items-center gap-6 text-sm font-medium text-slate-600">
            <a href="keranjang.php" class="hover:text-teal-dark transition">Keranjang</a>
            <a href="pembayaran.php" class="hover:text-teal-dark transition">Pembayaran</a>
            <a href="status_pemesanan.php" class="hover:text-teal-dark transition">Status Pesanan</a>
            <a href="profil.php" class="hover:text-teal-dark font-bold text-teal-dark flex items-center gap-1">
                👤 <?= htmlspecialchars($_SESSION['nama'] ?? 'Profil') ?>
            </a>
            <a href="logout.php" class="text-red-500 hover:text-red-700 text-xs">Keluar</a>
        </div>
    </div>
</nav>
<?php endif; ?>