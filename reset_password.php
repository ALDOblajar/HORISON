<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }

$token = trim($_GET['token'] ?? '');
$error   = $_SESSION['reset_error']   ?? '';
$success = $_SESSION['reset_success'] ?? '';
unset($_SESSION['reset_error'], $_SESSION['reset_success']);

// Validasi token
$valid_token = false;
$token_user_id = null;

if (!empty($token)) {
    $now  = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > ? LIMIT 1");
    $stmt->bind_param("ss", $token, $now);
    $stmt->execute();
    $stmt->bind_result($token_user_id);
    if ($stmt->fetch()) {
        $valid_token = true;
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_token       = trim($_POST['token']            ?? '');
    $new_password     = trim($_POST['new_password']     ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validasi ulang token dari POST
    $now  = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > ? LIMIT 1");
    $stmt->bind_param("ss", $post_token, $now);
    $stmt->execute();
    $stmt->bind_result($uid);
    $found = $stmt->fetch();
    $stmt->close();

    if (!$found) {
        $_SESSION['reset_error'] = 'Token tidak valid atau sudah kedaluwarsa.';
        header('Location: lupa_password.php'); exit;
    }

    if (strlen($new_password) < 8) {
        $_SESSION['reset_error'] = 'Kata sandi minimal 8 karakter.';
        header("Location: reset_password.php?token=$post_token"); exit;
    }
    if ($new_password !== $confirm_password) {
        $_SESSION['reset_error'] = 'Konfirmasi sandi tidak cocok.';
        header("Location: reset_password.php?token=$post_token"); exit;
    }

    // Update password
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed, $uid);
    $stmt->execute();
    $stmt->close();

    // Hapus token
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $post_token);
    $stmt->execute();
    $stmt->close();

    $_SESSION['login_error']    = ''; // bersihkan
    $_SESSION['active_tab']     = 'login';
    // Kirim pesan sukses ke auth.php
    $_SESSION['register_success'] = 'Kata sandi berhasil diubah! Silakan masuk. 🎉';
    header('Location: auth.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Reset Password – Minuman Boss</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root { --gold: #E8A020; --dark: #111; --muted: #888; --radius: 14px; --trans: 0.35s cubic-bezier(.4,0,.2,1); }
  body { min-height: 100vh; display: flex; align-items: stretch; font-family: 'DM Sans', sans-serif; background: var(--dark); }

  .left {
    flex: 1; position: relative; overflow: hidden;
    display: flex; flex-direction: column; justify-content: flex-end; padding: 3rem; min-height: 100vh;
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
    width: 460px; background: #fff; display: flex; align-items: center; justify-content: center;
    padding: 3rem 2.8rem; position: relative; overflow: hidden;
  }
  .right::before {
    content: ''; position: absolute; top: -80px; right: -80px; width: 280px; height: 280px;
    border-radius: 50%; background: radial-gradient(circle, rgba(232,160,32,.13) 0%, transparent 70%); pointer-events: none;
  }
  .form-box { width: 100%; max-width: 340px; }

  .back-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    font-size: .82rem; font-weight: 600; color: var(--muted);
    text-decoration: none; margin-bottom: 1.6rem; transition: var(--trans);
  }
  .back-btn:hover { color: var(--dark); }

  h2 { font-family: 'Bebas Neue', cursive; font-size: 2.2rem; letter-spacing: .06em; color: var(--dark); margin-bottom: .3rem; }
  .subtitle { font-size: .85rem; color: var(--muted); margin-bottom: 1.8rem; line-height: 1.5; }

  .field { margin-bottom: 1.1rem; }
  label { display: block; font-size: .8rem; font-weight: 600; color: #444; margin-bottom: .4rem; letter-spacing: .03em; text-transform: uppercase; }
  .input-wrap { position: relative; }
  input[type=password] {
    width: 100%; padding: .75rem 1rem; border: 1.5px solid #e0e0e0; border-radius: var(--radius);
    font-family: 'DM Sans', sans-serif; font-size: .95rem; color: var(--dark);
    background: #fafafa; outline: none; transition: var(--trans);
  }
  input:focus { border-color: var(--gold); background: #fff; box-shadow: 0 0 0 3px rgba(232,160,32,.15); }

  .toggle-pw {
    position: absolute; right: .9rem; top: 50%; transform: translateY(-50%);
    cursor: pointer; color: var(--muted); font-size: 1rem; user-select: none; background: none; border: none;
  }

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

  .expired-box {
    text-align: center; padding: 2rem 0;
  }
  .expired-box .icon { font-size: 3rem; margin-bottom: 1rem; }
  .expired-box p { font-size: .9rem; color: var(--muted); margin-bottom: 1.4rem; line-height: 1.6; }
  .expired-box a {
    display: inline-block; padding: .75rem 2rem; background: var(--dark); color: #fff;
    border-radius: var(--radius); font-family: 'Bebas Neue', cursive; font-size: 1rem;
    letter-spacing: .1em; text-decoration: none;
  }

  /* strength bar */
  .strength-bar { height: 4px; border-radius: 4px; margin-top: .4rem; background: #eee; overflow: hidden; }
  .strength-fill { height: 100%; width: 0; border-radius: 4px; transition: width .3s, background .3s; }

  @media (max-width: 740px) { .left { display: none; } .right { width: 100%; } }
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

    <?php if (!$valid_token): ?>
      <!-- Token tidak valid / expired -->
      <div class="expired-box">
        <div class="icon">⏰</div>
        <h2>Link Kedaluwarsa</h2>
        <p>Link reset password tidak valid atau sudah kedaluwarsa.<br>Silakan minta link baru.</p>
        <a href="lupa_password.php">MINTA LINK BARU</a>
      </div>

    <?php else: ?>
      <h2>Reset Sandi</h2>
      <p class="subtitle">Masukkan kata sandi baru untuk akunmu.</p>

      <?php if ($error): ?>
        <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="reset_password.php">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>"/>

        <div class="field">
          <label for="new-pw">Kata Sandi Baru</label>
          <div class="input-wrap">
            <input type="password" id="new-pw" name="new_password"
                   placeholder="Min. 8 karakter" required
                   oninput="checkStrength(this.value)"/>
            <button type="button" class="toggle-pw" onclick="togglePw('new-pw',this)">👁</button>
          </div>
          <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
        </div>

        <div class="field">
          <label for="confirm-pw">Konfirmasi Sandi Baru</label>
          <div class="input-wrap">
            <input type="password" id="confirm-pw" name="confirm_password"
                   placeholder="Ulangi kata sandi" required/>
            <button type="button" class="toggle-pw" onclick="togglePw('confirm-pw',this)">👁</button>
          </div>
        </div>

        <button type="submit" class="btn-primary">SIMPAN SANDI BARU</button>
      </form>
    <?php endif; ?>

  </div>
</div>

<script>
  function togglePw(id, el) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    el.textContent = inp.type === 'password' ? '👁' : '🙈';
  }

  function checkStrength(val) {
    const fill = document.getElementById('strength-fill');
    let score = 0;
    if (val.length >= 8)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['', '#e53e3e', '#e8a020', '#68d391', '#38a169'];
    const widths  = ['0%', '25%', '50%', '75%', '100%'];
    fill.style.width      = widths[score];
    fill.style.background = colors[score];
  }
</script>
</body>
</html>
