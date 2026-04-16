<?php
$error = $error ?? null;
if (!defined('BASE_URL')) require_once __DIR__ . '/../../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create an account at Ajil L3bo Café to manage your gaming sessions.">
    <title>Ajil L3bo Café — Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>

        /* ─── RESET ─── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ─── TOKENS ─── */
        :root {
            --primary:       #6366f1;
            --primary-dark:  #4f46e5;
            --text:          #111827;
            --muted:         #6b7280;
            --light:         #9ca3af;
            --border:        #e5e7eb;
            --white:         #ffffff;
            --radius:        10px;
            --ease:          all .22s ease;
            --error:         #f43f5e;
            --error-bg:      #fff1f2;
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
            inset: 0;
            display: flex;
            width: 100vw;
            height: 100vh;
        }

        /* ── LEFT PANEL (image) ── */
        .left {
            flex: 1;
            position: relative;
            background: url('/Aji-nle3bo-Cafe-Manager/app/views/img/registre.jpg') center/cover no-repeat;
            background-color: #312e81;
            display: flex;
            flex-direction: column;
            padding: 32px 40px;
            overflow: hidden;
        }

        /* gradient overlay */
        .left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                170deg,
                rgba(30,27,75,0.4) 0%,
                rgba(49,46,129,0.85) 100%
            );
        }

        .left > * { position: relative; z-index: 1; }

        /* top logo */
        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: auto;
        }

        .brand-logo .icon {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        /* heading */
        .left h1 {
            font-size: clamp(32px, 4vw, 44px);
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }
        .left h1 .highlight {
            color: #34d399; /* Mint green */
        }

        /* tagline */
        .left p.tagline {
            font-size: 15.5px;
            color: rgba(255,255,255,.9);
            line-height: 1.6;
            max-width: 360px;
            margin-bottom: 40px;
            font-weight: 400;
        }

        /* avatars row */
        .gamers {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .avatars { display: flex; align-items: center; }
        
        .avatars span {
            width: 38px; height: 38px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,.65);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            margin-left: -12px;
            background: linear-gradient(135deg, #1e293b, #475569);
            overflow: hidden;
        }
        
        .avatars span:first-child { margin-left: 0; background: linear-gradient(135deg, #9ca3af, #4b5563); }
        .avatars span:nth-child(2) { background: linear-gradient(135deg, #fbbf24, #d97706); }
        .avatars span:nth-child(3) { background: linear-gradient(135deg, #38bdf8, #0284c7); }
        .avatars span:nth-child(4) { background: linear-gradient(135deg, #a78bfa, #7c3aed); }

        .avatars .more {
            background: #fb7185; 
            font-size: 11px;
        }

        .gamers-text {
            font-size: 13.5px;
            color: rgba(255,255,255,.9);
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        /* ── RIGHT PANEL (form) ── */
        .right {
            flex: 1;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 5%;
            overflow-y: auto;
        }

        /* ─── FORM INNER CONTAINER ─── */
        .form-box {
            width: 100%;
            max-width: 480px;
            padding: 20px 0;
        }

        /* brand */
        .brand-right {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-bottom: 24px;
        }
        .brand-right .icon {
            width: 40px; height: 40px;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--primary), #818cf8);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 14px rgba(99,102,241,.35);
            flex-shrink: 0;
            color: white;
        }
        .brand-right .name {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
        }

        /* top badge right */
        .badge-right {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #eef2ff;
            color: var(--primary);
            font-size: 11px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 100px;
            margin-bottom: 24px;
            letter-spacing: 0.3px;
        }

        /* headings */
        .form-title {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        .form-subtitle {
            font-size: 14.5px;
            color: var(--muted);
            margin-bottom: 32px;
        }

        /* form layout */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        
        .field { margin-bottom: 20px; }
        .form-row .field { margin-bottom: 0; }

        .field label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
        }

        .field-wrap { position: relative; }

        .field-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--light);
            pointer-events: none;
            display: flex;
        }

        .field input {
            width: 100%;
            height: 46px;
            padding: 0 14px 0 40px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text);
            background: #fafafa;
            outline: none;
            transition: var(--ease);
        }
        .field input:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        .field input::placeholder { color: var(--light); }

        /* Error state styling */
        .field.has-error input {
            border-color: var(--error);
            background: var(--error-bg);
            color: var(--error);
        }
        .field.has-error input:focus {
            box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.1);
        }
        .field.has-error .error-msg {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--error);
            font-size: 11px;
            font-weight: 600;
            margin-top: 8px;
        }

        /* eye toggle */
        .eye-btn {
            position: absolute;
            right: 14px; top: 50%;
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
            margin-bottom: 28px;
        }
        .check-label {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
            cursor: pointer;
            line-height: 1.5;
        }
        .check-label input { 
            margin-top: 2px;
            accent-color: var(--primary); 
            width: 16px; height: 16px; 
            cursor: pointer; 
            flex-shrink: 0;
            border: 1px solid var(--border);
            border-radius: 4px;
        }
        .check-label a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        .check-label a:hover {
            text-decoration: underline;
        }

        /* submit */
        .btn-submit {
            width: 100%;
            height: 48px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: var(--ease);
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
        }
        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.3);
        }
        .btn-submit:active { transform: translateY(0); }

        /* register link */
        .reg-link {
            text-align: center;
            font-size: 13.5px;
            color: #6b7280;
        }
        .reg-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            transition: color .2s;
        }
        .reg-link a:hover { color: var(--primary-dark); text-decoration: underline; }

        /* footer features */
        .features-footer {
            margin-top: 40px;
            padding-top: 28px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 28px;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #4b5563;
        }
        .feature-item svg {
            color: #fb7185; /* pinkish red */
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 768px) {
            html, body { overflow: auto; }
            .page {
                flex-direction: column;
                position: relative;
                height: auto;
                min-height: 100vh;
            }
            .left  { flex: none; min-height: 40vh; padding: 32px 24px; }
            .right { flex: none; padding: 40px 6%; }
            .form-row { grid-template-columns: 1fr; gap: 20px; }
        }
    </style>
</head>
<body>

<div class="page">

    <!-- ══════ LEFT PANEL ══════ -->
    <div class="left">
        <div class="brand-logo">
            
        </div>

        <h1>More than just a<br>café, it's your<br><span class="highlight">gaming space.</span></h1>

        <p class="tagline">
            Connect with local players, book<br>
            tables, and track your victories all in<br>
            one place.
        </p>

        <div class="gamers">
            <div class="avatars">
                <!-- Example avatars, normally images -->
                <span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span>
                <span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span>
                <span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span>
                <span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span>
                <span class="more">+20</span>
            </div>
            <p class="gamers-text">
                Join 2,000+ active gamers in our community
            </p>
        </div>
    </div>

    <!-- ══════ RIGHT PANEL ══════ -->
    <div class="right">
        <div class="form-box">

            <!-- Brand Logo from Login Page -->
            <div class="brand-right">
                <div class="icon">🎲</div>
                <span class="name">Ajil L3bo Café</span>
            </div>

            <div class="badge-right">
                New Season Live! 🎯
            </div>

            <h1 class="form-title">Create your account</h1>
            <p class="form-subtitle">Join and start managing your gaming sessions.</p>

            <form method="POST" action="<?= BASE_URL ?>/register" id="registerForm" novalidate>

                <div class="form-row">
                    <!-- Full Name -->
                    <div class="field">
                        <label for="name">Full Name</label>
                        <div class="field-wrap">
                            <span class="field-icon">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <input
                                type="text" id="name" name="name"
                                placeholder="John Doe"
                                required
                            >
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div class="field">
                        <label for="phone">Phone Number</label>
                        <div class="field-wrap">
                            <span class="field-icon">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </span>
                            <input
                                type="text" id="phone" name="phone"
                                placeholder="+212 600..."
                                required
                            >
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="field <?= isset($error) ? 'has-error' : '' ?>">
                    <label for="email">Email Address</label>
                    <div class="field-wrap">
                        <span class="field-icon">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                <path d="M2 4l10 8 10-8"></path>
                            </svg>
                        </span>
                        <input
                            type="email" id="email" name="email"
                            placeholder="john@example.com"
                            required
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        >
                    </div>
                    <?php if (isset($error)): ?>
                    <div class="error-msg">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        Please enter a valid gaming email <!-- Static error message to match design -->
                    </div>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <!-- Password -->
                    <div class="field">
                        <label for="password">Password</label>
                        <div class="field-wrap">
                            <span class="field-icon">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </span>
                            <input
                                type="password" id="password" name="password"
                                placeholder="••••••••"
                                required
                            >
                            <button type="button" class="eye-btn" onclick="togglePassword('password', this)" aria-label="Show password">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="field">
                        <label for="password_confirm">Confirm Password</label>
                        <div class="field-wrap">
                            <span class="field-icon">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </span>
                            <input
                                type="password" id="password_confirm" name="password_confirm"
                                placeholder="••••••••"
                                required
                            >
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="options">
                    <label class="check-label" for="terms">
                        <input type="checkbox" id="terms" name="terms" required>
                        <span>I accept the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-submit" id="submitBtn">
                    Create Account
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>

                <!-- Login link -->
                <p class="reg-link">
                    Already have an account?
                    <a href="<?= BASE_URL ?>/login">Sign in here</a>
                </p>

            </form>

            <!-- Footer Features -->
            <div class="features-footer">
                <div class="feature-item">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                    </svg>
                    INSTANT BOOKING
                </div>
                <div class="feature-item">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="7"></circle>
                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                    </svg>
                    GLOBAL RANKINGS
                </div>
                <div class="feature-item">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                        <line x1="6" y1="1" x2="6" y2="4"></line>
                        <line x1="10" y1="1" x2="10" y2="4"></line>
                        <line x1="14" y1="1" x2="14" y2="4"></line>
                    </svg>
                    CAFE PERKS
                </div>
            </div>

        </div><!-- /.form-box -->
    </div><!-- /.right -->

</div><!-- /.page -->

<script>
    // Toggle password visibility
    function togglePassword(inputId, btn) {
        const pwdIn = document.getElementById(inputId);
        const vis = pwdIn.type === 'text';
        pwdIn.type = vis ? 'password' : 'text';
        
        if (vis) {
            btn.innerHTML = `<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
        } else {
            btn.innerHTML = `<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
        }
    }
</script>

</body>
</html>
