<?php
// ═══════════════════════════════════════════════════
//  AJI L3BO CAFÉ — Public Entry Point / Router
// ═══════════════════════════════════════════════════
session_start();
define('BASE', '/Aji-nle3bo-Cafe-Manager');
define('VIEWS', dirname(__DIR__) . '/app/views');

// ── DB Connection ──────────────────────────────────
require_once dirname(__DIR__) . '/config/config.php';
try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:40px;"><h2>DB Error</h2><p>'.$e->getMessage().'</p></div>');
}

// ── URL Parsing ────────────────────────────────────
$url   = trim($_GET['url'] ?? '', '/');
$url   = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
$parts = $url === '' ? [] : explode('/', $url);
$route = $parts[0] ?? '';
$p1    = $parts[1] ?? null;
$p2    = $parts[2] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

// ── Auth helpers ───────────────────────────────────
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: '.BASE.'/login'); exit;
    }
}
function flash($key, $msg = null) {
    if ($msg !== null) { $_SESSION['_flash'][$key] = $msg; return; }
    $v = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $v;
}
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ── ROUTING ────────────────────────────────────────
switch ($route) {

    // ─ ROOT → redirect ─
    case '':
        if (isset($_SESSION['user_id'])) { header('Location:'.BASE.'/dashboard'); }
        else { header('Location:'.BASE.'/login'); }
        exit;

    // ─ AUTH ─────────────────────────────────────
    case 'login':
        if ($method === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['password'] ?? '';
            $stmt  = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user  = $stmt->fetch();
            if ($user && password_verify($pass, $user['password'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                header('Location:'.BASE.'/dashboard'); exit;
            }
            $error = 'Email ou mot de passe incorrect.';
        }
        require VIEWS.'/auth/login.php'; break;

    case 'register':
        if ($method === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $pass     = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';
            if ($pass !== $confirm) { $error = 'Les mots de passe ne correspondent pas.'; }
            else {
                $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $chk->execute([$email]);
                if ($chk->fetch()) { $error = 'Cet email est déjà utilisé.'; }
                else {
                    $pdo->prepare("INSERT INTO users (username,email,password,role,created_at) VALUES (?,?,?,'user',NOW())")
                        ->execute([$username, $email, password_hash($pass, PASSWORD_BCRYPT)]);
                    $u = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                    $u->execute([$email]);
                    $user = $u->fetch();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role'];
                    header('Location:'.BASE.'/dashboard'); exit;
                }
            }
        }
        require VIEWS.'/auth/registre.php'; break;

    case 'logout':
        session_destroy();
        header('Location:'.BASE.'/login'); exit;

    // ─ DASHBOARD ─────────────────────────────────
    case 'dashboard':
        requireLogin();
        // Stats
        $totalReservations  = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
        $activeSessions     = $pdo->query("SELECT COUNT(*) FROM sessions WHERE status='active'")->fetchColumn();
        $availableTables    = $pdo->query("SELECT COUNT(*) FROM tables_cafe WHERE status='available'")->fetchColumn();
        $totalTables        = $pdo->query("SELECT COUNT(*) FROM tables_cafe")->fetchColumn();
        $todayReservations  = $pdo->query("SELECT COUNT(*) FROM reservations WHERE reservation_date=CURDATE()")->fetchColumn();
        // Most played game
        $mpg = $pdo->query("SELECT g.name, COUNT(s.id) as cnt FROM sessions s JOIN games g ON s.game_id=g.id GROUP BY s.game_id ORDER BY cnt DESC LIMIT 1")->fetch();
        // Recent reservations
        $recentRes = $pdo->query("SELECT r.*, t.number as table_number FROM reservations r LEFT JOIN tables_cafe t ON r.table_id=t.id ORDER BY r.created_at DESC LIMIT 5")->fetchAll();
        // Active sessions list
        $activeList = $pdo->query("SELECT s.*, g.name as game_name, t.number as table_number, TIMESTAMPDIFF(MINUTE,s.start_time,NOW()) as elapsed FROM sessions s LEFT JOIN games g ON s.game_id=g.id LEFT JOIN tables_cafe t ON s.table_id=t.id WHERE s.status='active'")->fetchAll();
        // Popular games
        $popularGames = $pdo->query("SELECT g.name, g.id, COUNT(s.id) as cnt FROM games g LEFT JOIN sessions s ON g.id=s.game_id GROUP BY g.id ORDER BY cnt DESC LIMIT 6")->fetchAll();
        require VIEWS.'/admin/dashbord.php'; break;

    // ─ GAMES ─────────────────────────────────────
    case 'games':
        requireLogin();
        $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
        $search  = trim($_GET['search'] ?? '');
        $catFilter = $_GET['category'] ?? '';
        if ($p1 === 'create') {
            // Show create form
            require VIEWS.'/games/create.php'; break;
        }
        if ($p1 === 'store' && $method === 'POST') {
            $pdo->prepare("INSERT INTO games (name,category_id,nb_players,duration,difficulty,description,status) VALUES (?,?,?,?,?,?,?)")
                ->execute([$_POST['name'],$_POST['category_id'],$_POST['nb_players'],$_POST['duration'],$_POST['difficulty'],$_POST['description'],$_POST['status']]);
            flash('success','Jeu ajouté avec succès !');
            header('Location:'.BASE.'/games'); exit;
        }
        if ($p1 && is_numeric($p1) && $p2 === 'edit') {
            $game = $pdo->prepare("SELECT g.*,c.name as category_name FROM games g LEFT JOIN categories c ON g.category_id=c.id WHERE g.id=?");
            $game->execute([$p1]); $game = $game->fetch();
            if (!$game) { header('Location:'.BASE.'/games'); exit; }
            require VIEWS.'/games/edit.php'; break;
        }
        if ($p1 && is_numeric($p1) && $p2 === 'update' && $method === 'POST') {
            $pdo->prepare("UPDATE games SET name=?,category_id=?,nb_players=?,duration=?,difficulty=?,description=?,status=? WHERE id=?")
                ->execute([$_POST['name'],$_POST['category_id'],$_POST['nb_players'],$_POST['duration'],$_POST['difficulty'],$_POST['description'],$_POST['status'],$p1]);
            flash('success','Jeu modifié avec succès !');
            header('Location:'.BASE.'/games'); exit;
        }
        if ($p1 && is_numeric($p1) && $p2 === 'delete' && $method === 'POST') {
            $pdo->prepare("DELETE FROM games WHERE id=?")->execute([$p1]);
            flash('success','Jeu supprimé.');
            header('Location:'.BASE.'/games'); exit;
        }
        if ($p1 && is_numeric($p1)) {
            $game = $pdo->prepare("SELECT g.*,c.name as category_name FROM games g LEFT JOIN categories c ON g.category_id=c.id WHERE g.id=?");
            $game->execute([$p1]); $game = $game->fetch();
            if (!$game) { header('Location:'.BASE.'/games'); exit; }
            require VIEWS.'/games/show.php'; break;
        }
        // Games list
        if ($search !== '') {
            $sql = "SELECT g.*,c.name as category_name FROM games g LEFT JOIN categories c ON g.category_id=c.id WHERE g.name LIKE ? OR g.description LIKE ? ORDER BY g.name";
            $s = $pdo->prepare($sql); $s->execute(["%$search%","%$search%"]);
            $games = $s->fetchAll();
        } elseif ($catFilter !== '') {
            $s = $pdo->prepare("SELECT g.*,c.name as category_name FROM games g LEFT JOIN categories c ON g.category_id=c.id WHERE g.category_id=? ORDER BY g.name");
            $s->execute([$catFilter]); $games = $s->fetchAll();
        } else {
            $games = $pdo->query("SELECT g.*,c.name as category_name FROM games g LEFT JOIN categories c ON g.category_id=c.id ORDER BY g.created_at DESC")->fetchAll();
        }
        $totalGames     = $pdo->query("SELECT COUNT(*) FROM games")->fetchColumn();
        $availableCount = $pdo->query("SELECT COUNT(*) FROM games WHERE status='available'")->fetchColumn();
        $inUseCount     = $pdo->query("SELECT COUNT(DISTINCT game_id) FROM sessions WHERE status='active'")->fetchColumn();
        require VIEWS.'/games/index.php'; break;

    // ─ RESERVATIONS ──────────────────────────────
    case 'reservations':
        requireLogin();
        $tables = $pdo->query("SELECT * FROM tables_cafe ORDER BY number")->fetchAll();
        if ($p1 === 'create') {
            require VIEWS.'/reservations/create.php'; break;
        }
        if ($p1 === 'store' && $method === 'POST') {
            $tid  = $_POST['table_id'];
            $date = $_POST['reservation_date'];
            $time = $_POST['reservation_time'];
            $chk  = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE table_id=? AND reservation_date=? AND reservation_time=? AND status IN('pending','confirmed')");
            $chk->execute([$tid,$date,$time]);
            if ($chk->fetchColumn() > 0) {
                flash('error','Cette table est déjà réservée pour ce créneau.');
                header('Location:'.BASE.'/reservations/create'); exit;
            }
            $pdo->prepare("INSERT INTO reservations (client_name,phone,user_id,table_id,reservation_date,reservation_time,number_of_people,status) VALUES (?,?,?,?,?,?,?,'pending')")
                ->execute([$_POST['client_name'],$_POST['phone'],$_SESSION['user_id'],$tid,$date,$time,$_POST['number_of_people']]);
            flash('success','Réservation créée !');
            header('Location:'.BASE.'/reservations'); exit;
        }
        if ($p1 && is_numeric($p1) && $p2 === 'confirm' && $method === 'POST') {
            $pdo->prepare("UPDATE reservations SET status='confirmed' WHERE id=?")->execute([$p1]);
            flash('success','Réservation confirmée !');
            header('Location:'.BASE.'/reservations'); exit;
        }
        if ($p1 && is_numeric($p1) && $p2 === 'cancel' && $method === 'POST') {
            $pdo->prepare("UPDATE reservations SET status='cancelled' WHERE id=?")->execute([$p1]);
            flash('success','Réservation annulée.');
            header('Location:'.BASE.'/reservations'); exit;
        }
        if ($p1 && is_numeric($p1)) {
            $reservation = $pdo->prepare("SELECT r.*,t.number as table_number,t.capacity FROM reservations r LEFT JOIN tables_cafe t ON r.table_id=t.id WHERE r.id=?");
            $reservation->execute([$p1]); $reservation = $reservation->fetch();
            require VIEWS.'/reservations/show.php'; break;
        }
        // Reservations list
        $filter = $_GET['filter'] ?? 'all';
        switch ($filter) {
            case 'today':    $reservations = $pdo->query("SELECT r.*,t.number as table_number FROM reservations r LEFT JOIN tables_cafe t ON r.table_id=t.id WHERE r.reservation_date=CURDATE() ORDER BY r.reservation_time")->fetchAll(); break;
            case 'upcoming': $reservations = $pdo->query("SELECT r.*,t.number as table_number FROM reservations r LEFT JOIN tables_cafe t ON r.table_id=t.id WHERE r.reservation_date>=CURDATE() AND r.status IN('pending','confirmed') ORDER BY r.reservation_date,r.reservation_time")->fetchAll(); break;
            case 'mine':     $s = $pdo->prepare("SELECT r.*,t.number as table_number FROM reservations r LEFT JOIN tables_cafe t ON r.table_id=t.id WHERE r.user_id=? ORDER BY r.reservation_date DESC"); $s->execute([$_SESSION['user_id']]); $reservations = $s->fetchAll(); break;
            default:         $reservations = $pdo->query("SELECT r.*,t.number as table_number FROM reservations r LEFT JOIN tables_cafe t ON r.table_id=t.id ORDER BY r.reservation_date DESC,r.reservation_time DESC")->fetchAll();
        }
        $totalCount     = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
        $todayCount     = $pdo->query("SELECT COUNT(*) FROM reservations WHERE reservation_date=CURDATE()")->fetchColumn();
        $pendingCount   = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status='pending'")->fetchColumn();
        $confirmedCount = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status='confirmed'")->fetchColumn();
        require VIEWS.'/reservations/index.php'; break;

    // ─ SESSIONS ──────────────────────────────────
    case 'sessions':
        requireLogin();
        if ($p1 === 'create') {
            $games  = $pdo->query("SELECT * FROM games WHERE status='available' ORDER BY name")->fetchAll();
            $tables = $pdo->query("SELECT * FROM tables_cafe WHERE status='available' ORDER BY number")->fetchAll();
            $confirmedRes = $pdo->query("SELECT r.*,t.number as table_number FROM reservations r LEFT JOIN tables_cafe t ON r.table_id=t.id WHERE r.status='confirmed' AND r.reservation_date=CURDATE()")->fetchAll();
            require VIEWS.'/sessions/create.php'; break;
        }
        if ($p1 === 'start' && $method === 'POST') {
            $gid = $_POST['game_id'];
            $tid = $_POST['table_id'];
            $pdo->prepare("INSERT INTO sessions (game_id,table_id,start_time,status) VALUES (?,?,NOW(),'active')")->execute([$gid,$tid]);
            $pdo->prepare("UPDATE tables_cafe SET status='occupied' WHERE id=?")->execute([$tid]);
            flash('success','Session démarrée !');
            header('Location:'.BASE.'/sessions'); exit;
        }
        if ($p1 && is_numeric($p1) && $p2 === 'end' && $method === 'POST') {
            $s = $pdo->prepare("SELECT * FROM sessions WHERE id=?"); $s->execute([$p1]); $sess = $s->fetch();
            $pdo->prepare("UPDATE sessions SET end_time=NOW(),status='finished' WHERE id=?")->execute([$p1]);
            if ($sess) $pdo->prepare("UPDATE tables_cafe SET status='available' WHERE id=?")->execute([$sess['table_id']]);
            flash('success','Session terminée, table libérée !');
            header('Location:'.BASE.'/sessions'); exit;
        }
        if ($p1 === 'history') {
            $sessions = $pdo->query("SELECT s.*,g.name as game_name,t.number as table_number,TIMESTAMPDIFF(MINUTE,s.start_time,s.end_time) as duration_minutes FROM sessions s LEFT JOIN games g ON s.game_id=g.id LEFT JOIN tables_cafe t ON s.table_id=t.id WHERE s.status='finished' ORDER BY s.end_time DESC")->fetchAll();
            require VIEWS.'/sessions/history.php'; break;
        }
        // Active sessions dashboard
        $activeSessions = $pdo->query("SELECT s.*,g.name as game_name,t.number as table_number,t.capacity,TIMESTAMPDIFF(MINUTE,s.start_time,NOW()) as elapsed FROM sessions s LEFT JOIN games g ON s.game_id=g.id LEFT JOIN tables_cafe t ON s.table_id=t.id WHERE s.status='active' ORDER BY s.start_time")->fetchAll();
        $activeCount  = $pdo->query("SELECT COUNT(*) FROM sessions WHERE status='active'")->fetchColumn();
        $todayCount   = $pdo->query("SELECT COUNT(*) FROM sessions WHERE DATE(start_time)=CURDATE()")->fetchColumn();
        $totalCount   = $pdo->query("SELECT COUNT(*) FROM sessions")->fetchColumn();
        require VIEWS.'/sessions/index.php'; break;

    // ─ CATEGORIES ────────────────────────────────
    case 'categories':
        requireLogin();
        if ($p1 === 'store' && $method === 'POST') {
            $name = trim($_POST['name'] ?? '');
            if ($name !== '') $pdo->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$name]);
            flash('success','Catégorie ajoutée !');
            header('Location:'.BASE.'/categories'); exit;
        }
        if ($p1 && is_numeric($p1) && $p2 === 'delete') {
            $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$p1]);
            flash('success','Catégorie supprimée.');
            header('Location:'.BASE.'/categories'); exit;
        }
        $cats = $pdo->query("SELECT c.*,COUNT(g.id) as game_count FROM categories c LEFT JOIN games g ON c.id=g.category_id GROUP BY c.id ORDER BY c.name")->fetchAll();
        require VIEWS.'/admin/category.php'; break;

    // ─ 404 ───────────────────────────────────────
    default:
        http_response_code(404);
        require VIEWS.'/errors/404.php'; break;
}
