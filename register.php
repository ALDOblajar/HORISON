<?php
session_start();
require_once 'config/database.php';

// Kalau sudah login, redirect
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: auth.php');
    exit;
}

$nama             = trim($_POST['nama']             ?? '');
$email            = trim($_POST['email']            ?? '');
$password         = trim($_POST['password']         ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

// Helper redirect error
function regError($msg, $nama, $email) {
    $_SESSION['register_error']       = $msg;
    $_SESSION['old_name']             = $nama;
    $_SESSION['old_email_register']   = $email;
    $_SESSION['active_tab']           = 'register';
    header('Location: auth.php');
    exit;
}

// ── Validasi ──
if (empty($nama) || empty($email) || empty($password) || empty($confirm_password)) {
    regError('Semua kolom wajib diisi.', $nama, $email);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    regError('Format email tidak valid.', $nama, $email);
}

if (strlen($password) < 8) {
    regError('Kata sandi minimal 8 karakter.', $nama, $email);
}

if ($password !== $confirm_password) {
    regError('Konfirmasi sandi tidak cocok.', $nama, $email);
}

// ── Cek email sudah terdaftar ──
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    regError('Email sudah terdaftar. Silakan gunakan email lain.', $nama, $email);
}
$stmt->close();

// ── Hash password & simpan ──
$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nama, $email, $hashed);

if ($stmt->execute()) {
    $stmt->close();
    $_SESSION['register_success'] = "Akun berhasil dibuat! Silakan masuk, $nama. 🎉";
    $_SESSION['active_tab']       = 'login';
    header('Location: auth.php');
    exit;
} else {
    $stmt->close();
    regError('Gagal membuat akun. Coba lagi.', $nama, $email);
}
