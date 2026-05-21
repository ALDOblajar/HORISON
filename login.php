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

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

// ── Validasi dasar ──
if (empty($email) || empty($password)) {
    $_SESSION['login_error']     = 'Email dan kata sandi wajib diisi.';
    $_SESSION['old_email_login'] = $email;
    $_SESSION['active_tab']      = 'login';
    header('Location: auth.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_error']     = 'Format email tidak valid.';
    $_SESSION['old_email_login'] = $email;
    $_SESSION['active_tab']      = 'login';
    header('Location: auth.php');
    exit;
}

// ── Cek ke database ──
// Sesuaikan nama tabel & kolom dengan database.sql kamu
$stmt = $conn->prepare("SELECT id, nama, email, password FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['login_error']     = 'Email atau kata sandi salah.';
    $_SESSION['old_email_login'] = $email;
    $_SESSION['active_tab']      = 'login';
    header('Location: auth.php');
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// ── Verifikasi password (password_hash) ──
if (!password_verify($password, $user['password'])) {
    $_SESSION['login_error']     = 'Email atau kata sandi salah.';
    $_SESSION['old_email_login'] = $email;
    $_SESSION['active_tab']      = 'login';
    header('Location: auth.php');
    exit;
}

// ── Login berhasil — simpan session ──
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_nama']  = $user['nama'];
$_SESSION['user_email'] = $user['email'];

// Redirect ke halaman utama
header('Location: index.php');
exit;
