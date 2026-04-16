  </div><!-- /.page -->

  <footer class="footer">
    © <?= date('Y') ?> Aji L3bo Café Management System. All rights reserved.
  </footer>
</div><!-- /.main -->
</div><!-- /.app -->

<script>
// ── Dark / Light mode ──────────────────────────────────
const html = document.documentElement;
const saved = localStorage.getItem('theme') || 'light';
html.setAttribute('data-theme', saved);
updateThemeIcon(saved);

document.getElementById('themeBtn').addEventListener('click', () => {
  const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
  html.setAttribute('data-theme', next);
  localStorage.setItem('theme', next);
  updateThemeIcon(next);
});

function updateThemeIcon(t) {
  const el = document.getElementById('themeIcon');
  el.innerHTML = t === 'dark'
    ? '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>'
    : '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
}

// ── Auto-dismiss flash ─────────────────────────────────
setTimeout(() => {
  const f = document.getElementById('flashWrap');
  if (f) { f.style.transition='opacity .4s'; f.style.opacity='0'; setTimeout(()=>f.remove(),400); }
}, 4000);

// ── Mobile sidebar close on outside click ─────────────
document.addEventListener('click', e => {
  const sb = document.getElementById('sidebar');
  const btn = document.getElementById('menuBtn');
  if (sb && sb.classList.contains('open') && !sb.contains(e.target) && btn && !btn.contains(e.target)) {
    sb.classList.remove('open');
  }
});

// ── Live session timers ────────────────────────────────
function tick() {
  document.querySelectorAll('[data-start]').forEach(el => {
    const start = new Date(el.dataset.start.replace(' ','T'));
    const diff = Math.max(0, Math.floor((Date.now() - start) / 1000));
    const h = String(Math.floor(diff/3600)).padStart(2,'0');
    const m = String(Math.floor((diff%3600)/60)).padStart(2,'0');
    const s = String(diff%60).padStart(2,'0');
    el.textContent = `${h}:${m}:${s}`;
  });
}
if (document.querySelector('[data-start]')) { tick(); setInterval(tick, 1000); }
</script>
</body>
</html>
