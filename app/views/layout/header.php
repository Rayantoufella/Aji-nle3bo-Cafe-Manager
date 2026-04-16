<?php
// helpers available in all pages
function base(){ return '/Aji-nle3bo-Cafe-Manager'; }
$_user   = $_SESSION['username']  ?? 'User';
$_role   = $_SESSION['user_role'] ?? 'user';
$_init   = strtoupper(substr($_user, 0, 1));
$_pageId = $pageId ?? '';
$_title  = $pageTitle ?? 'Dashboard';

// nav active helper
function isActive($id){ global $_pageId; return $_pageId === $id ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($_title) ?> — Aji L3bo Café</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base() ?>/app/views/assets/css/style.css">
</head>
<body>
<div class="app">

<!-- ══════════════ SIDEBAR ══════════════ -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-icon">🎲</div>
    <div>
      <div class="sidebar-logo-text">Aji <span>L3bo</span> Café</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <a href="<?= base() ?>/dashboard" class="nav-item <?= isActive('dashboard') ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>
    <a href="<?= base() ?>/games" class="nav-item <?= isActive('games') ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
      Games
    </a>
    <a href="<?= base() ?>/reservations" class="nav-item <?= isActive('reservations') ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Reservations
    </a>
    <a href="<?= base() ?>/sessions" class="nav-item <?= isActive('sessions') ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Sessions
    </a>

    <div class="nav-label">Management</div>
    <a href="<?= base() ?>/categories" class="nav-item <?= isActive('categories') ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
      Categories
    </a>
  </nav>

  <div class="sidebar-bottom">
    <a href="<?= base() ?>/logout" class="nav-item danger">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Logout
    </a>
  </div>
</aside>

<!-- ══════════════ MAIN ══════════════ -->
<div class="main">

  <!-- TOP BAR -->
  <header class="topbar">
    <div class="topbar-left">
      <button class="hamburger" id="menuBtn" onclick="document.getElementById('sidebar').classList.toggle('open')">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <div class="topbar-search">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search games, reservations, or players..." id="globalSearch">
      </div>
    </div>
    <div class="topbar-right">
      <button class="topbar-icon-btn" id="themeBtn" title="Toggle dark mode">
        <svg id="themeIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
      </button>
      <div class="topbar-user">
        <div class="topbar-avatar"><?= $_init ?></div>
        <div>
          <div class="topbar-user-name"><?= htmlspecialchars($_user) ?></div>
          <div class="topbar-user-role"><?= htmlspecialchars($_role) ?></div>
        </div>
      </div>
    </div>
  </header>

  <!-- FLASH MESSAGES -->
  <div style="padding:0 28px;margin-top:0" id="flashWrap">
    <?php $fs = flash('success'); if($fs): ?>
      <div class="flash flash-success" style="margin-top:18px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <?= htmlspecialchars($fs) ?>
      </div>
    <?php endif; ?>
    <?php $fe = flash('error'); if($fe): ?>
      <div class="flash flash-error" style="margin-top:18px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <?= htmlspecialchars($fe) ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- PAGE -->
  <div class="page anim-fade">
