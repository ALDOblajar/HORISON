<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }

$error   = $_SESSION['lupa_error']   ?? '';
$success = $_SESSION['lupa_success'] ?? '';
$old_email = $_SESSION['lupa_old_email'] ?? '';
unset($_SESSION['lupa_error'], $_SESSION['lupa_success'], $_SESSION['lupa_old_email']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['lupa_error']     = 'Masukkan email yang valid.';
        $_SESSION['lupa_old_email'] = $email;
        header('Location: lupa_password.php'); exit;
    }

    // Cek apakah email ada di database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        // Pesan sengaja sama biar tidak bisa ditebak akun mana yang ada
        $_SESSION['lupa_success'] = 'Jika email terdaftar, link reset telah dibuat. Cek halaman reset password.';
        header('Location: lupa_password.php'); exit;
    }
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    // Buat token unik
    $token   = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Hapus token lama milik user ini (kalau ada)
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Simpan token baru
    $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $token, $expires);
    $stmt->execute();
    $stmt->close();

    // Karena lokal (XAMPP), tampilkan link langsung di halaman
    $_SESSION['reset_link']   = 'reset_password.php?token=' . $token;
    $_SESSION['lupa_success'] = 'Link reset berhasil dibuat! Klik link di bawah untuk melanjutkan.';
    header('Location: lupa_password.php'); exit;
}

$reset_link = $_SESSION['reset_link'] ?? '';
unset($_SESSION['reset_link']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Lupa Password – Minuman Boss</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --gold: #E8A020; --dark: #111; --muted: #888;
    --radius: 14px; --trans: 0.35s cubic-bezier(.4,0,.2,1);
  }
  body {
    min-height: 100vh; display: flex; align-items: stretch;
    font-family: 'DM Sans', sans-serif; background: var(--dark);
  }
  .left {
    flex: 1; position: relative; overflow: hidden;
    display: flex; flex-direction: column;
    justify-content: flex-end; padding: 3rem; min-height: 100vh;
  }
  .left-bg {
    position: absolute; inset: 0;
    background-image: url('assets/img/login.png');
    background-size: cover; background-position: center; z-index: 0;
  }
  .left-bg::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.78) 0%, rgba(0,0,0,.25) 50%, rgba(0,0,0,.1) 100%);
  }
  .left-copy { position: relative; z-index: 2; }
  .brand { font-family: 'Bebas Neue', cursive; font-size: 2.8rem; color: var(--gold); letter-spacing: .08em; line-height: 1; margin-bottom: .4rem; }
  .tagline { font-size: .9rem; color: rgba(255,255,255,.5); letter-spacing: .06em; text-transform: uppercase; }

  .right {
    width: 460px; background: #fff; display: flex;
    align-items: center; justify-content: center;
    padding: 3rem 2.8rem; position: relative; overflow: hidden;
  }
  .right::before {
    content: ''; position: absolute; top: -80px; right: -80px;
    width: 280px; height: 280px; border-radius: 50%;
    background: radial-gradient(circle, rgba(232,160,32,.13) 0%, transparent 70%);
    pointer-events: none;
  }
  .form-box { width: 100%; max-width: 340px; }

  .back-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    font-size: .82rem; font-weight: 600; color: var(--muted);
    text-decoration: none; margin-bottom: 1.6rem;
    transition: var(--trans);
  }
  .back-btn:hover { color: var(--dark); }

  h2 { font-family: 'Bebas Neue', cursive; font-size: 2.2rem; letter-spacing: .06em; color: var(--dark); margin-bottom: .3rem; }
  .subtitle { font-size: .85rem; color: var(--muted); margin-bottom: 1.8rem; line-height: 1.5; }

  .field { margin-bottom: 1.1rem; }
  label { display: block; font-size: .8rem; font-weight: 600; color: #444; margin-bottom: .4rem; letter-spacing: .03em; text-transform: uppercase; }
  input[type=email] {
    width: 100%; padding: .75rem 1rem;
    border: 1.5px solid #e0e0e0; border-radius: var(--radius);
    font-family: 'DM Sans', sans-serif; font-size: .95rem;
    color: var(--dark); background: #fafafa; outline: none; transition: var(--trans);
  }
  input:focus { border-color: var(--gold); background: #fff; box-shadow: 0 0 0 3px rgba(232,160,32,.15); }

  .btn-primary {
    width: 100%; padding: .9rem; background: var(--dark); color: #fff;
    border: none; border-radius: var(--radius);
    font-family: 'Bebas Neue', cursive; font-size: 1.15rem; letter-spacing: .12em;
    cursor: pointer; transition: var(--trans); position: relative; overflow: hidden;
  }
  .btn-primary::after {
    content:''; position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent, rgba(232,160,32,.3), transparent);
    transform: translateX(-100%); transition: transform .6s ease;
  }
  .btn-primary:hover { background: #222; }
  .btn-primary:hover::after { transform: translateX(100%); }

  .alert { padding: .7rem 1rem; border-radius: 10px; font-size: .85rem; font-weight: 500; margin-bottom: 1.2rem; }
  .alert-error   { background: #fff5f5; color: #c53030; border: 1px solid #fed7d7; }
  .alert-success { background: #f0fff4; color: #276749; border: 1px solid #c6f6d5; }

  .reset-link-box {
    margin-top: 1rem; padding: .9rem 1rem;
    background: #fffbeb; border: 1.5px dashed var(--gold);
    border-radius: var(--radius); word-break: break-all;
  }
  .reset-link-box p { font-size: .78rem; color: #888; margin-bottom: .4rem; }
  .reset-link-box a { font-size: .85rem; color: var(--dark); font-weight: 600; text-decoration: underline; }

  @media (max-width: 740px) {
    .left { display: none; }
    .right { width: 100%; }
  }
</style>
</head>
<body>

<div class="left">
  <div class="left-bg"></div>
  <div class="left-copy">
    <div class="brand">Minuman Boss</div>
    <div class="tagline">Rasa Terbaik, Pilihan Terpercaya</div>
  </div>
</div>

<div class="right">
  <div class="form-box">

    <a href="auth.php" class="back-btn">← Kembali ke Login</a>

    <h2>Lupa Sandi</h2>
    <p class="subtitle">Masukkan email akunmu. Kami akan buatkan link untuk reset kata sandimu.</p>

    <?php if ($error): ?>
      <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($reset_link): ?>
      <div class="reset-link-box">
        <p>🔗 Link reset password kamu (berlaku 1 jam):</p>
        <a href="<?= htmlspecialchars($reset_link) ?>"><?= htmlspecialchars($reset_link) ?></a>
      </div>
    <?php endif; ?>

    <?php if (!$reset_link): ?>
    <form method="POST" action="lupa_password.php">
      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email"
               placeholder="kamu@email.com"
               value="<?= htmlspecialchars($old_email) ?>"
               required />
      </div>
      <button type="submit" class="btn-primary">KIRIM LINK RESET</button>
    </form>
    <?php endif; ?>

  </div>
</div>
</body>
</html>
