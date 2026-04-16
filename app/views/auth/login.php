<?php
$error = $error ?? null;
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Connectez-vous à Ajil L3bo Café pour gérer vos réservations et sessions de jeux.">
    <title>Ajil L3bo Café — Connexion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>

        /* ─── RESET ─── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ─── TOKENS ─── */
        :root {
            --primary:       #4f46e5;
            --primary-dark:  #4338ca;
            --text:          #111827;
            --muted:         #6b7280;
            --light:         #9ca3af;
            --border:        #e5e7eb;
            --white:         #ffffff;
            --radius:        10px;
            --ease:          all .22s ease;
        }

        /* ─── BASE ─── */
        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            overflow: hidden;
        }

        /* ═══════════════════════════════════════
           LAYOUT — full-screen split two panels
        ═══════════════════════════════════════ */
        .page {
            position: fixed;
            inset: 0;           /* top:0 right:0 bottom:0 left:0 */
            display: flex;
            width: 100vw;
            height: 100vh;
        }

        /* ── LEFT PANEL (image) ── */
        .left {
            flex: 1;            /* exactly half */
            position: relative;
            background: url('<?= $baseUrl ?>/app/views/img/login.jpg') center/cover no-repeat;
            background-color: #1e1b4b;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 48px 44px;
            overflow: hidden;
        }

        /* dark gradient overlay */
        .left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                170deg,
                rgba(10,10,30,.15) 0%,
                rgba(10,10,30,.80) 100%
            );
        }

        /* everything above the overlay */
        .left > * { position: relative; z-index: 1; }

        /* badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(255,255,255,.16);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.28);
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .6px;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 100px;
            margin-bottom: 20px;
            width: fit-content;
        }
        .badge::before {
            content: '';
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #6ee7b7;
            flex-shrink: 0;
        }

        /* heading */
        .left h1 {
            font-size: clamp(32px, 4vw, 52px);
            font-weight: 800;
            color: #fff;
            line-height: 1.1;
            margin-bottom: 14px;
            text-shadow: 0 2px 16px rgba(0,0,0,.35);
        }

        /* tagline */
        .left p.tagline {
            font-size: 15px;
            color: rgba(255,255,255,.82);
            line-height: 1.65;
            max-width: 340px;
            margin-bottom: 32px;
        }

        /* avatars row */
        .gamers {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .avatars { display: flex; }
        .avatars span {
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,.65);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            margin-left: -11px;
            background: linear-gradient(135deg,#6366f1,#8b5cf6);
        }
        .avatars span:first-child { margin-left: 0; }
        .avatars span:nth-child(2) { background: linear-gradient(135deg,#ec4899,#f43f5e); }
        .avatars span:nth-child(3) { background: linear-gradient(135deg,#f59e0b,#f97316); }
        .avatars span:nth-child(4) { background: linear-gradient(135deg,#10b981,#059669); }

        .gamers-text {
            font-size: 13px;
            color: rgba(255,255,255,.82);
            font-weight: 500;
        }
        .gamers-text strong { color: #fff; font-weight: 700; }

        /* ── RIGHT PANEL (form) ── */
        .right {
            flex: 1;            /* exactly half */
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 5%;
            overflow-y: auto;
        }

        /* ─── FORM INNER CONTAINER ─── */
        .form-box {
            width: 100%;
            max-width: 420px;
        }

        /* brand */
        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-bottom: 36px;
        }
        .brand-icon {
            width: 40px; height: 40px;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--primary), #818cf8);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 14px rgba(79,70,229,.35);
            flex-shrink: 0;
        }
        .brand-name {
            font-size: 17px;
            font-weight: 700;
        }

        /* headings */
        .form-title {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 7px;
        }
        .form-subtitle {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 32px;
        }

        /* error banner */
        .alert-error {
            display: flex;
            align-items: center;
            gap: 9px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            font-size: 13.5px;
            font-weight: 500;
            padding: 12px 15px;
            border-radius: var(--radius);
            margin-bottom: 24px;
        }

        /* field group */
        .field { margin-bottom: 20px; }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .field-wrap { position: relative; }

        .field-icon {
            position: absolute;
            left: 13px; top: 50%;
            transform: translateY(-50%);
            color: var(--light);
            pointer-events: none;
            display: flex;
        }

        .field input {
            width: 100%;
            height: 48px;
            padding: 0 42px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 14px;
            color: var(--text);
            background: #f9fafb;
            outline: none;
            transition: var(--ease);
        }
        .field input:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(79,70,229,.12);
        }
        .field input::placeholder { color: var(--light); }

        /* eye toggle */
        .eye-btn {
            position: absolute;
            right: 13px; top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--light);
            padding: 0;
            display: flex;
            transition: color .2s;
        }
        .eye-btn:hover { color: var(--primary); }

        /* options row */
        .options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 28px;
        }
        .check-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }
        .check-label input { accent-color: var(--primary); width: 15px; height: 15px; cursor: pointer; }

        .link-forgot {
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
            transition: color .2s;
            white-space: nowrap;
        }
        .link-forgot:hover { color: var(--primary-dark); }

        /* submit */
        .btn-submit {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), #6366f1);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: var(--ease);
            box-shadow: 0 4px 18px rgba(79,70,229,.35);
            margin-bottom: 22px;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, var(--primary-dark), #4f46e5);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(79,70,229,.45);
        }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit svg { transition: transform .2s; }
        .btn-submit:hover svg { transform: translateX(3px); }

        /* register link */
        .reg-link {
            text-align: center;
            font-size: 13.5px;
            color: var(--muted);
        }
        .reg-link a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
            transition: color .2s;
        }
        .reg-link a:hover { color: var(--primary-dark); }

        /* footer inside form-box */
        .form-footer {
            margin-top: 36px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }
        .footer-links { display: flex; gap: 18px; }
        .footer-links a,
        .footer-copy {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--light);
            text-decoration: none;
            transition: color .2s;
        }
        .footer-links a:hover { color: var(--primary); }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 768px) {
            html, body { overflow: auto; }
            .page {
                flex-direction: column;
                position: relative;
                height: auto;
                min-height: 100vh;
            }
            .left  { flex: none; min-height: 45vh; padding: 32px 28px; }
            .right { flex: none; padding: 40px 7%; }
        }
    </style>
</head>
<body>

<div class="page">

    <!-- ══════ LEFT PANEL ══════ -->
    <div class="left">
        <span class="badge">Établi en 2026</span>

        <h1>Bienvenue<br>de retour !</h1>

        <p class="tagline">
            Votre espace de jeu vous attend.<br>
            Tirez une chaise, lancez les dés et<br>
            laissez l'aventure continuer.
        </p>

        <div class="gamers">
            <div class="avatars">
                <span>A</span>
                <span>K</span>
                <span>M</span>
                <span>S</span>
            </div>
            <p class="gamers-text">
                Rejoints par <strong>1 200+</strong> joueurs cette semaine
            </p>
        </div>
    </div>

    <!-- ══════ RIGHT PANEL ══════ -->
    <div class="right">
        <div class="form-box">

            <!-- Brand -->
            <div class="brand">
                <div class="brand-icon">🎲</div>
                <span class="brand-name">Ajil L3bo Café</span>
            </div>

            <h1 class="form-title">Connexion à votre compte</h1>
            <p class="form-subtitle">Gérez vos réservations et sessions de jeux</p>

            <?php if (!empty($error)): ?>
            <div class="alert-error" role="alert">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/>
                </svg>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= $baseUrl ?>/login" id="loginForm" novalidate>

                <!-- Email -->
                <div class="field">
                    <label for="email">Adresse e-mail</label>
                    <div class="field-wrap">
                        <span class="field-icon">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </span>
                        <input
                            type="email" id="email" name="email"
                            placeholder="nom@exemple.com"
                            required autocomplete="email"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="field">
                    <label for="password">Mot de passe</label>
                    <div class="field-wrap">
                        <span class="field-icon">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input
                            type="password" id="password" name="password"
                            placeholder="••••••••"
                            required autocomplete="current-password"
                        >
                        <button type="button" class="eye-btn" id="eyeBtn" aria-label="Afficher mot de passe">
                            <svg id="eyeShow" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg id="eyeHide" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Options -->
                <div class="options">
                    <label class="check-label" for="remember">
                        <input type="checkbox" id="remember" name="remember">
                        Se souvenir 30 jours
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-submit" id="submitBtn">
                    Se connecter
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>

                <!-- Register -->
                <p class="reg-link">
                    Pas encore de compte ?
                    <a href="<?= $baseUrl ?>/register">Créer un compte</a>
                </p>

            </form>

            <!-- Footer -->
            <footer class="form-footer">
                <div class="footer-links">
                    <a href="#">Confidentialité</a>
                    <a href="#">CGU</a>
                </div>
                <span class="footer-copy">© 2026 Ajil L3bo Café</span>
            </footer>

        </div><!-- /.form-box -->
    </div><!-- /.right -->

</div><!-- /.page -->

<script>
    // Toggle password visibility
    const eyeBtn  = document.getElementById('eyeBtn');
    const pwdIn   = document.getElementById('password');
    const eyeShow = document.getElementById('eyeShow');
    const eyeHide = document.getElementById('eyeHide');

    eyeBtn.addEventListener('click', () => {
        const vis = pwdIn.type === 'text';
        pwdIn.type = vis ? 'password' : 'text';
        eyeShow.style.display = vis ? 'block' : 'none';
        eyeHide.style.display = vis ? 'none'  : 'block';
    });

    // Loading state on submit
    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = `
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                 style="animation:spin .7s linear infinite">
                <polyline points="23 4 23 10 17 10"/>
                <path d="M20.49 15a9 9 0 1 1-.06-6.98"/>
            </svg>
            Connexion…`;
    });
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>

</body>
</html>
