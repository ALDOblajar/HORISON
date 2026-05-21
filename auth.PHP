<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Minuman Boss – Login / Register</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --gold:   #E8A020;
    --dark:   #111111;
    --panel:  #1a1a1a;
    --card:   #ffffff;
    --muted:  #888;
    --accent: #5B3EE0;
    --radius: 14px;
    --trans:  0.35s cubic-bezier(.4,0,.2,1);
  }

  body {
    min-height: 100vh;
    display: flex;
    align-items: stretch;
    font-family: 'DM Sans', sans-serif;
    background: var(--dark);
    overflow: hidden;
  }

  /* ── LEFT PANEL ── */
  .left {
    flex: 1;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 3rem;
    min-height: 100vh;
  }
  .left-bg {
    position: absolute; inset: 0;
    background-image: url('assets/img/login.png');
    background-size: cover;
    background-position: center;
    z-index: 0;
  }
  /* dark gradient overlay so text stays readable */
  .left-bg::after {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(
      to top,
      rgba(0,0,0,.75) 0%,
      rgba(0,0,0,.25) 50%,
      rgba(0,0,0,.1) 100%
    );
  }

  .left-copy {
    position: relative; z-index: 2;
  }
  .brand {
    font-family: 'Bebas Neue', cursive;
    font-size: 2.8rem;
    color: var(--gold);
    letter-spacing: .08em;
    line-height: 1;
    margin-bottom: .4rem;
  }
  .tagline {
    font-size: .9rem;
    color: rgba(255,255,255,.5);
    letter-spacing: .06em;
    text-transform: uppercase;
  }

  /* ── RIGHT PANEL ── */
  .right {
    width: 460px;
    background: var(--card);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 2.8rem;
    position: relative;
    overflow: hidden;
  }
  .right::before {
    content: '';
    position: absolute;
    top: -80px; right: -80px;
    width: 280px; height: 280px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(232,160,32,.15) 0%, transparent 70%);
    pointer-events: none;
  }

  .form-box {
    width: 100%;
    max-width: 340px;
  }

  /* tabs */
  .tabs {
    display: flex;
    background: #f0f0f0;
    border-radius: 10px;
    padding: 4px;
    margin-bottom: 2rem;
  }
  .tab-btn {
    flex: 1;
    padding: .6rem 0;
    border: none;
    background: transparent;
    border-radius: 7px;
    font-family: 'DM Sans', sans-serif;
    font-size: .9rem;
    font-weight: 600;
    color: var(--muted);
    cursor: pointer;
    transition: var(--trans);
  }
  .tab-btn.active {
    background: #fff;
    color: var(--dark);
    box-shadow: 0 2px 8px rgba(0,0,0,.12);
  }

  /* forms */
  .form-panel { display: none; animation: fadeUp .3s ease; }
  .form-panel.active { display: block; }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  h2 {
    font-family: 'Bebas Neue', cursive;
    font-size: 2.2rem;
    letter-spacing: .06em;
    color: var(--dark);
    margin-bottom: .3rem;
  }
  .subtitle {
    font-size: .85rem;
    color: var(--muted);
    margin-bottom: 1.8rem;
    line-height: 1.5;
  }

  .field { margin-bottom: 1.1rem; }
  label {
    display: block;
    font-size: .8rem;
    font-weight: 600;
    color: #444;
    margin-bottom: .4rem;
    letter-spacing: .03em;
    text-transform: uppercase;
  }
  .input-wrap {
    position: relative;
  }
  input[type=text], input[type=email], input[type=password] {
    width: 100%;
    padding: .75rem 1rem;
    border: 1.5px solid #e0e0e0;
    border-radius: var(--radius);
    font-family: 'DM Sans', sans-serif;
    font-size: .95rem;
    color: var(--dark);
    background: #fafafa;
    outline: none;
    transition: var(--trans);
  }
  input:focus {
    border-color: var(--gold);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(232,160,32,.15);
  }
  .toggle-pw {
    position: absolute;
    right: .9rem; top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: var(--muted);
    font-size: 1rem;
    user-select: none;
  }

  .forgot {
    display: block;
    text-align: right;
    font-size: .8rem;
    color: var(--gold);
    text-decoration: none;
    margin-top: -.4rem;
    margin-bottom: 1.4rem;
    font-weight: 600;
  }
  .forgot:hover { text-decoration: underline; }

  .btn-primary {
    width: 100%;
    padding: .9rem;
    background: var(--dark);
    color: #fff;
    border: none;
    border-radius: var(--radius);
    font-family: 'Bebas Neue', cursive;
    font-size: 1.15rem;
    letter-spacing: .12em;
    cursor: pointer;
    transition: var(--trans);
    position: relative;
    overflow: hidden;
    margin-top: .2rem;
  }
  .btn-primary::after {
    content:'';
    position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent, rgba(232,160,32,.3), transparent);
    transform: translateX(-100%);
    transition: transform .6s ease;
  }
  .btn-primary:hover { background: #222; }
  .btn-primary:hover::after { transform: translateX(100%); }
  .btn-primary:active { transform: scale(.98); }

  .divider {
    display: flex;
    align-items: center;
    gap: .8rem;
    margin: 1.2rem 0;
    color: #ccc;
    font-size: .75rem;
  }
  .divider::before, .divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e8e8e8;
  }

  .btn-social {
    width: 100%;
    padding: .75rem;
    border: 1.5px solid #e0e0e0;
    border-radius: var(--radius);
    background: #fff;
    font-family: 'DM Sans', sans-serif;
    font-size: .9rem;
    font-weight: 500;
    color: var(--dark);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .6rem;
    transition: var(--trans);
  }
  .btn-social:hover { background: #f5f5f5; border-color: #ccc; }

  .switch-line {
    text-align: center;
    font-size: .85rem;
    color: var(--muted);
    margin-top: 1.4rem;
  }
  .switch-line a {
    color: var(--dark);
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
  }
  .switch-line a:hover { color: var(--gold); }

  /* toast */
  .toast {
    position: fixed;
    bottom: 2rem; right: 2rem;
    background: var(--dark);
    color: #fff;
    padding: .8rem 1.4rem;
    border-radius: 10px;
    font-size: .88rem;
    font-weight: 500;
    box-shadow: 0 8px 24px rgba(0,0,0,.3);
    display: flex;
    align-items: center;
    gap: .6rem;
    transform: translateY(80px);
    opacity: 0;
    transition: var(--trans);
    z-index: 999;
    border-left: 4px solid var(--gold);
  }
  .toast.show { transform: translateY(0); opacity: 1; }

  /* responsive */
  @media (max-width: 740px) {
    .left { display: none; }
    .right { width: 100%; border-radius: 0; }
  }
</style>
</head>
<body>

<!-- LEFT -->
<div class="left">
  <div class="left-bg"></div>
  <div class="left-copy">
    <div class="brand">HORISON</div>
    <div class="tagline">Rasa Terbaik, Pilihan Terpercaya</div>
  </div>
</div>

<!-- RIGHT -->
<div class="right">
  <div class="form-box">

    <div class="tabs">
      <button class="tab-btn active" onclick="switchTab('login')">Masuk</button>
      <button class="tab-btn" onclick="switchTab('register')">Daftar</button>
    </div>

    <!-- LOGIN -->
    <div class="form-panel active" id="panel-login">
      <h2>Masuk</h2>
      <p class="subtitle">Hello, teman! Task manager, semuanya bisa kamu percaya. Ayo hubungi kami.</p>

      <div class="field">
        <label>Email</label>
        <input type="email" id="login-email" placeholder="kamu@email.com" />
      </div>
      <div class="field">
        <label>Kata Sandi</label>
        <div class="input-wrap">
          <input type="password" id="login-pw" placeholder="••••••••" />
          <span class="toggle-pw" onclick="togglePw('login-pw',this)">👁</span>
        </div>
      </div>
      <a href="#" class="forgot">Lupa Password?</a>
      <button class="btn-primary" onclick="handleLogin()">MULAI</button>

      <div class="divider">atau masuk dengan</div>
      <button class="btn-social">
        <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.5 0 6.6 1.2 9.1 3.2l6.8-6.8C35.8 2.4 30.2 0 24 0 14.8 0 6.9 5.4 3 13.3l7.9 6.1C12.7 13.2 17.9 9.5 24 9.5z"/><path fill="#4285F4" d="M46.5 24.5c0-1.6-.1-3.1-.4-4.5H24v8.5h12.7c-.6 3-2.3 5.5-4.8 7.2l7.5 5.8c4.4-4.1 7.1-10.1 7.1-17z"/><path fill="#FBBC05" d="M10.9 28.6A14.6 14.6 0 0 1 9.5 24c0-1.6.3-3.1.8-4.6L2.4 13.3A24 24 0 0 0 0 24c0 3.8.9 7.4 2.5 10.6l8.4-6z"/><path fill="#34A853" d="M24 48c6.2 0 11.4-2 15.2-5.5l-7.5-5.8c-2 1.4-4.7 2.2-7.7 2.2-6.1 0-11.3-3.7-13.1-9l-7.9 6.1C6.9 42.6 14.8 48 24 48z"/></svg>
        Google
      </button>

      <div class="switch-line">Tidak punya akun? <a onclick="switchTab('register')">Daftar</a></div>
    </div>

    <!-- REGISTER -->
    <div class="form-panel" id="panel-register">
      <h2>Daftar</h2>
      <p class="subtitle">Buat akun baru dan mulai petualanganmu bersama HORISON.</p>

      <div class="field">
        <label>Nama Lengkap</label>
        <input type="text" id="reg-name" placeholder="Nama kamu" />
      </div>
      <div class="field">
        <label>Email</label>
        <input type="email" id="reg-email" placeholder="kamu@email.com" />
      </div>
      <div class="field">
        <label>Kata Sandi</label>
        <div class="input-wrap">
          <input type="password" id="reg-pw" placeholder="Min. 8 karakter" />
          <span class="toggle-pw" onclick="togglePw('reg-pw',this)">👁</span>
        </div>
      </div>
      <div class="field">
        <label>Konfirmasi Sandi</label>
        <div class="input-wrap">
          <input type="password" id="reg-pw2" placeholder="Ulangi kata sandi" />
          <span class="toggle-pw" onclick="togglePw('reg-pw2',this)">👁</span>
        </div>
      </div>
      <button class="btn-primary" onclick="handleRegister()">DAFTAR SEKARANG</button>

      <div class="switch-line">Sudah punya akun? <a onclick="switchTab('login')">Masuk</a></div>
    </div>

  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
  <span id="toast-icon">✅</span>
  <span id="toast-msg">Berhasil!</span>
</div>

<script>
  function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach((b,i) =>
      b.classList.toggle('active', (tab==='login' ? i===0 : i===1)));
    document.querySelectorAll('.form-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-'+tab).classList.add('active');
  }

  function togglePw(id, el) {
    const inp = document.getElementById(id);
    if (inp.type === 'password') { inp.type = 'text'; el.textContent = '🙈'; }
    else { inp.type = 'password'; el.textContent = '👁'; }
  }

  function showToast(msg, ok=true) {
    const t = document.getElementById('toast');
    document.getElementById('toast-icon').textContent = ok ? '✅' : '❌';
    document.getElementById('toast-msg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }

  function handleLogin() {
    const email = document.getElementById('login-email').value.trim();
    const pw    = document.getElementById('login-pw').value;
    if (!email || !pw) { showToast('Email dan kata sandi wajib diisi.', false); return; }
    if (!/\S+@\S+\.\S+/.test(email)) { showToast('Format email tidak valid.', false); return; }
    // Simulate: check localStorage
    const users = JSON.parse(localStorage.getItem('mb_users') || '[]');
    const user  = users.find(u => u.email === email && u.pw === pw);
    if (user) { showToast(`Selamat datang, ${user.name}! 🎉`); }
    else      { showToast('Email atau kata sandi salah.', false); }
  }

  function handleRegister() {
    const name  = document.getElementById('reg-name').value.trim();
    const email = document.getElementById('reg-email').value.trim();
    const pw    = document.getElementById('reg-pw').value;
    const pw2   = document.getElementById('reg-pw2').value;
    if (!name || !email || !pw || !pw2) { showToast('Semua kolom wajib diisi.', false); return; }
    if (!/\S+@\S+\.\S+/.test(email))   { showToast('Format email tidak valid.', false); return; }
    if (pw.length < 8)                  { showToast('Kata sandi minimal 8 karakter.', false); return; }
    if (pw !== pw2)                     { showToast('Konfirmasi sandi tidak cocok.', false); return; }
    const users = JSON.parse(localStorage.getItem('mb_users') || '[]');
    if (users.find(u => u.email === email)) { showToast('Email sudah terdaftar.', false); return; }
    users.push({ name, email, pw });
    localStorage.setItem('mb_users', JSON.stringify(users));
    showToast(`Akun berhasil dibuat, ${name}! Silakan masuk. 🚀`);
    setTimeout(() => switchTab('login'), 1500);
  }
</script>
</body>
</html>