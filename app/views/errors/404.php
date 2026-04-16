<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>404 — Page Not Found | Aji L3bo Café</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--primary:#4f46e5;--secondary:#8b5cf6;--text:#111827;--muted:#6b7280;--bg:#f3f4f6;--surface:#fff}
[data-theme="dark"]{--text:#f9fafb;--muted:#9ca3af;--bg:#0f172a;--surface:#1f2937}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
.wrap{text-align:center;max-width:520px;animation:up .5s ease}
@keyframes up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.code{
  font-size:clamp(80px,18vw,140px);font-weight:900;line-height:1;
  background:linear-gradient(135deg,var(--primary),var(--secondary));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  background-clip:text;letter-spacing:-6px;margin-bottom:8px;
}
.emoji{font-size:56px;margin-bottom:20px;display:block}
h1{font-size:22px;font-weight:800;margin-bottom:10px}
p{font-size:14px;color:var(--muted);line-height:1.7;margin-bottom:30px}
.btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.btn{
  display:inline-flex;align-items:center;gap:7px;
  padding:11px 24px;border-radius:10px;
  font-family:inherit;font-size:13px;font-weight:600;
  text-decoration:none;border:none;cursor:pointer;
  transition:all .2s ease;
}
.btn-primary{background:var(--primary);color:#fff;box-shadow:0 4px 14px rgba(79,70,229,.3)}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(79,70,229,.4)}
.btn-ghost{background:var(--surface);color:var(--text);border:1.5px solid #e5e7eb}
.btn-ghost:hover{border-color:var(--primary);color:var(--primary)}
.faint{opacity:.08;font-size:200px;font-weight:900;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:-1;color:var(--primary);pointer-events:none;user-select:none}
</style>
</head>
<body>
<div class="faint">404</div>
<div class="wrap">
  <span class="emoji">🕹️</span>
  <div class="code">404</div>
  <h1>Page Not Found</h1>
  <p>Oops! The page you're looking for has left the table.<br>Let's get you back in the game.</p>
  <div class="btns">
    <a href="/Aji-nle3bo-Cafe-Manager/dashboard" class="btn btn-primary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      Back to Dashboard
    </a>
    <a href="/Aji-nle3bo-Cafe-Manager/games" class="btn btn-ghost">
      Browse Games
    </a>
  </div>
</div>
<script>
const t=localStorage.getItem('theme')||'light';
document.documentElement.setAttribute('data-theme',t);
</script>
</body>
</html>
