# 🎲 Aji L3bo Café — Système de Gestion

> Application de gestion pour cafés de jeux de société | Architecture MVC PHP moderne

---

## 📋 Table des Matières

- [Contexte du Projet](#contexte-du-projet)
- [MCD — Modèle Conceptuel de Données](#mcd--modèle-conceptuel-de-données)
- [MLD — Modèle Logique de Données](#mld--modèle-logique-de-données)
- [Diagramme de Classes UML](#diagramme-de-classes-uml)
- [Structure MVC](#structure-mvc)
- [Modules & User Stories](#modules--user-stories)
- [Installation](#installation)
- [Équipe](#équipe)

---

## 🏪 Contexte du Projet

**Aji L3bo Café** (Casablanca) gère actuellement ses réservations sur papier et son inventaire dans un cahier. Notre mission : **digitaliser complètement** la gestion du café.

### Objectifs Techniques

| Pilier | Description |
|--------|-------------|
| 🛣️ **Router** | URLs propres (`/games/5`, `/reservations/create`) |
| 📦 **Namespaces** | PSR-4 (`App\Controllers\GameController`) |
| 🎼 **Composer** | Autoloading PSR-4, zéro `require_once` manuels |
| 🏗️ **MVC strict** | Models ↔ Controllers ↔ Views bien séparés |
| 🤝 **Agile Trinôme** | Standups, Jira, code reviews systématiques |

---

## MCD — Modèle Conceptuel de Données

```
┌─────────────┐         ┌────────────┐         ┌──────────────┐
│   Category  │         │    Game    │         │    Session   │
│─────────────│         │────────────│         │──────────────│
│ id          │ 1    N  │ id         │ 1    N  │ id           │
│ name        │◄────────│ category_id│────────►│ game_id      │
└─────────────┘categorise│ name       │planifie │ table_id     │
                         │ n_players  │         │ user_id      │
                         │ duration   │         │ start_time   │
                         │ difficulty │         │ end_time     │
                         │ status     │         │ status       │
                         └────────────┘         └──────────────┘
                                                       │
                                                       │ accueille
                                                       ▼
┌─────────────────┐      ┌────────────┐         ┌──────────────┐
│   Reservation   │      │    User    │         │    Table     │
│─────────────────│      │────────────│         │──────────────│
│ id              │      │ id         │         │ id           │
│ client_name     │ N  1 │ username   │ 1    N  │ number       │
│ phone           │◄─────│ email      │────────►│ capacity     │
│ user_id         │associe│ password  │participe│ status       │
│ table_id        │      │ role       │         └──────────────┘
│ reservation_date│      │ created_at │
│ reservation_time│      └────────────┘
│ number_of_people│             │
│ status          │             │ hérite
└─────────────────┘             ▼
                         ┌────────────┐
                         │   Admin    │
                         │────────────│
                         │ permissions│
                         └────────────┘
```

### Entités & Cardinalités

| Association | Entité 1 | Cardinalité | Entité 2 |
|-------------|----------|-------------|----------|
| `categorise` | Category | 1,N | Game |
| `planifie` | Game | 1,N | Session |
| `accueille` | Table | 1,N | Session |
| `participe` | User | 1,N | Session |
| `effectue` | User | 1,N | Reservation |
| `associe` | Table | 1,N | Reservation |

---

## MLD — Modèle Logique de Données

```sql
-- Table des catégories de jeux
--categories (
    --id          INT PRIMARY KEY AUTO_INCREMENT,
    --name        VARCHAR(100) NOT NULL
--)

-- Table des jeux
--games (
    --id              INT PRIMARY KEY AUTO_INCREMENT,
    --name            VARCHAR(150) NOT NULL,
    --categories_id   INT NOT NULL,
    --n_players       INT NOT NULL,
    --duration        INT NOT NULL,                        -- en minutes
    --difficulty      ENUM('easy','medium','hard','expert'),
    --description     TEXT,
    --status          ENUM('available','in_use','unavailable') DEFAULT 'available',
    --created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,

    --FOREIGN KEY (categories_id) REFERENCES categories(id) ON DELETE SET NULL
--)

-- Table des utilisateurs
--users (
   -- id          INT PRIMARY KEY AUTO_INCREMENT,
   -- username    VARCHAR(100) NOT NULL,
   -- email       VARCHAR(150) NOT NULL UNIQUE,
   -- password    VARCHAR(255) NOT NULL,                   -- hash bcrypt
   -- role        ENUM('client','admin') DEFAULT 'client',
   -- created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
--)

-- Table des tables du café
--tables (
   -- id          INT PRIMARY KEY AUTO_INCREMENT,
   -- number      INT NOT NULL UNIQUE,
   -- capacity    INT NOT NULL,
   -- status      ENUM('available','occupied','reserved') DEFAULT 'available'
--)

-- Table des réservations
--reservations (
   -- id                  INT PRIMARY KEY AUTO_INCREMENT,
   -- client_name         VARCHAR(150) NOT NULL,
   -- phone               VARCHAR(20) NOT NULL,
   -- user_id             INT,
   -- table_id            INT NOT NULL,
   -- reservation_date    DATE NOT NULL,
   -- reservation_time    TIME NOT NULL,
   -- number_of_people    INT NOT NULL,
   -- status              ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
   -- created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,

   -- FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE SET NULL,
   -- FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE
--)

-- Table des sessions de jeu
--sessions (
   -- id          INT PRIMARY KEY AUTO_INCREMENT,
   -- game_id     INT NOT NULL,
   -- table_id    INT NOT NULL,
   -- user_id     INT NOT NULL,
   -- start_time  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   -- end_time    DATETIME,
   -- status      ENUM('active','completed','cancelled') DEFAULT 'active',

   -- FOREIGN KEY (game_id)  REFERENCES games(id) ON DELETE CASCADE,
   -- FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE,
   -- FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE CASCADE
--)
```

---

## Diagramme de Classes UML

### Hiérarchie d'héritage

```
Database (classe parente)
    ├── User
    │     └── Admin
    ├── Game
    ├── Category
    ├── Table
    ├── Session
    └── Reservation
```

### Classes Détaillées

#### 🗄️ Database
```
«abstract»
Database
──────────────────────────────
# pdo : PDO
──────────────────────────────
+ __construct()
+ prepare(sql: string) : PDOStatement
+ execute(params: array) : bool
+ fetch() : array|false
+ fetchAll() : array
+ lastInsertId() : int
```

#### 🏷️ Category extends Database
```
Category
──────────────────────────────
# id   : int
# name : varchar
──────────────────────────────
+ getAll() : array
+ getById(id: int) : array
+ create(name: string) : bool
+ update(id: int, name: string) : bool
+ delete(id: int) : bool
+ getGamesByCategory(catId: int) : array
```

#### 👤 User extends Database
```
User
──────────────────────────────
# id         : int
# username   : varchar
# email      : varchar
# password   : varchar
# role       : enum
# created_at : datetime
──────────────────────────────
+ login(email, password) : bool
+ logout() : void
+ register(username, email, password) : bool
+ getById(id) : array
+ getByEmail(email) : array
+ updateProfile(id, username, email) : bool
+ changePassword(id, oldPwd, newPwd) : bool
+ isAdmin() : bool
+ getUsername() : string
+ getEmail() : string
+ getRole() : string
```

#### 🛡️ Admin extends User
```
Admin
──────────────────────────────
- permissions : array
──────────────────────────────
+ addPermission(perm) : void
+ hasPermission(perm) : bool
+ getPermissions() : array
+ getAllUsers() : array
+ deleteUser(userId) : bool
+ changeUserRole(userId, role) : bool
+ createAdmin(username, email, pwd) : bool
+ editGame(gameId, ...) : bool
+ deleteGame(gameId) : bool
+ getAllReservations() : array
+ cancelReservation(resId) : bool
+ viewLogs() : array
+ logAction(action, details) : void
```

#### 🎲 Game extends Database
```
Game
──────────────────────────────
# id            : int
# name          : varchar
# categories_id : int
# n_players     : int
# duration      : int
# difficulty    : enum
# description   : text
# status        : enum
# created_at    : datetime
──────────────────────────────
+ getAllGames() : array
+ getById(id) : array
+ create(...) : bool
+ update(id, ...) : bool
+ delete(id) : bool
+ getByCategory(catId) : array
+ search(keyword) : array
+ getByDifficulty(diff) : array
+ getByPlayerCount(count) : array
+ getCategory() : array
+ getSessions(gameId) : array
```

#### 🪑 Table extends Database
```
Table
──────────────────────────────
# id       : int
# number   : int
# capacity : int
# status   : enum
──────────────────────────────
+ getAll() : array
+ getById(id) : array
+ create(number, capacity) : bool
+ update(id, ...) : bool
+ delete(id) : bool
+ getAvailableTables() : array
+ getAvailableByCapacity(capacity) : array
+ getAvailableByDateTime(date, time) : array
+ updateStatus(id, status) : bool
+ getSessions(tableId) : array
+ getReservations(tableId) : array
+ isAvailable(tableId, date, time) : bool
```

#### 🕹️ Session extends Database
```
Session
──────────────────────────────
# id         : int
# game_id    : int
# table_id   : int
# user_id    : int
# start_time : datetime
# end_time   : datetime
# status     : enum
──────────────────────────────
+ startSession(gameId, tableId, userId) : int
+ endSession(sessionId) : bool
+ getById(id) : array
+ update(id, ...) : bool
+ getActive() : array
+ getByTable(tableId) : array
+ getByUser(userId) : array
+ getByGame(gameId) : array
+ getByDate(date) : array
+ getDuration(sessionId) : int
+ countByGame(gameId) : int
+ countByUser(userId) : int
```

#### 📅 Reservation extends Database
```
Reservation
──────────────────────────────
# id                : int
# client_name       : varchar
# phone             : varchar
# user_id           : int
# table_id          : int
# reservation_date  : date
# reservation_time  : time
# number_of_people  : int
# status            : enum
# created_at        : datetime
──────────────────────────────
+ create(...) : int
+ getById(id) : array
+ update(id, ...) : bool
+ cancel(id) : bool
+ getByUser(userId) : array
+ getByTable(tableId) : array
+ getByDate(date) : array
+ getUpcoming() : array
+ getPast() : array
+ checkAvailability(tableId, date, time) : bool
+ getAvailableTables(date, time, capacity) : array
+ isConflict(tableId, date, time) : bool
```

---

## Structure MVC

```
aji-l3bo-cafe/
│
├── app/
│   ├── controller/
│   │   ├── AdminController.php       ← Gestion admin (users, logs)
│   │   ├── AuthController.php        ← Login, logout, register
│   │   ├── CategorieController.php   ← CRUD catégories
│   │   ├── GamesController.php       ← CRUD jeux, filtres
│   │   ├── ReservationController.php ← Réservations, disponibilité
│   │   ├── SessionController.php     ← Démarrer / terminer sessions
│   │   └── TableController.php       ← Gestion des tables
│   │
│   ├── models/
│   │   ├── DatabaseModel.php         ← Connexion PDO (classe parente)
│   │   ├── AdminModel.php            ← Hérite UserModel
│   │   ├── CategoryModel.php
│   │   ├── GameModel.php
│   │   ├── ReservationModel.php
│   │   ├── SessionModel.php
│   │   ├── TableModel.php
│   │   └── UserModel.php
│   │
│   └── views/
│       ├── admin/
│       │   ├── dashboard.php         ← Dashboard admin
│       │   ├── addgames.php
│       │   ├── editgame.php
│       │   └── category.php
│       ├── auth/
│       │   ├── login.php
│       │   └── registre.php
│       ├── games/
│       │   ├── index.php             ← Liste des jeux
│       │   ├── show.php              ← Détail d'un jeu
│       │   ├── create.php
│       │   └── edit.php
│       ├── reservations/
│       │   ├── index.php
│       │   ├── create.php
│       │   └── show.php
│       ├── sessions/
│       │   └── index.php             ← Dashboard sessions actives
│       ├── user/
│       │   └── profile.php
│       ├── layout/
│       │   ├── header.php
│       │   └── footer.php
│       ├── img/
│       │   ├── logo.png
│       │   ├── login.jpg
│       │   └── registre.jpg
│       └── errors/
│           └── 404.php
│
├── config/
│   └── config.php                    ← DB credentials, constantes
│
├── public/
│   └── index.php                     ← Point d'entrée unique (Front Controller)
│
├── router/
│   ├── Router.php                    ← Classe de routing
│   └── routes.php                    ← Définition des routes
│
├── vendor/                           ← Composer autoload
├── assets/                           ← CSS, JS, images publiques
│
├── .htaccess                         ← Réécriture d'URLs (mod_rewrite)
├── composer.json                     ← PSR-4 autoloading
├── Db.sql                            ← Script de création de la BDD
└── .gitignore
```

### Flux d'une Requête HTTP

```
Navigateur
    │
    ▼ GET /games/5
.htaccess (mod_rewrite)
    │  → redirige tout vers public/index.php
    ▼
public/index.php
    │  → instancie Router
    ▼
router/Router.php
    │  → parse l'URL, trouve la route correspondante
    │  → routes.php : GET /games/{id} → GamesController@show
    ▼
app/controller/GamesController.php
    │  → appelle GameModel::getById(5)
    ▼
app/models/GameModel.php
    │  → requête SQL via DatabaseModel
    │  → retourne les données
    ▼
GamesController.php
    │  → passe les données à la vue
    ▼
app/views/games/show.php
    │  → affiche le HTML final
    ▼
Navigateur ← réponse HTML
```

---

## Namespaces PSR-4

```json
// composer.json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "App\\Controllers\\": "app/controller/",
            "App\\Models\\": "app/models/",
            "App\\Router\\": "router/"
        }
    }
}
```

Exemples d'utilisation :
```php
use App\Controllers\GamesController;
use App\Models\GameModel;
use App\Router\Router;
```

---

## Modules & User Stories

### 🎲 Module 1 : Catalogue de Jeux

| # | En tant que | Je veux | Route |
|---|-------------|---------|-------|
| US1 | Client | Voir tous les jeux disponibles | `GET /games` |
| US2 | Client | Voir le détail d'un jeu | `GET /games/{id}` |
| US3 | Admin | Ajouter / modifier / supprimer un jeu | `POST /games`, `PUT /games/{id}` |
| US4 | Client | Filtrer par catégorie | `GET /games?category=strategie` |

### 📅 Module 2 : Système de Réservations

| # | En tant que | Je veux | Route |
|---|-------------|---------|-------|
| US5 | Client | Voir les tables disponibles | `GET /reservations/available` |
| US6 | Client | Créer une réservation | `POST /reservations/create` |
| US7 | Client | Voir mes réservations | `GET /reservations/my` |
| US8 | Admin | Gérer toutes les réservations du jour | `GET /admin/reservations` |

### 🕹️ Module 3 : Gestion des Sessions

| # | En tant que | Je veux | Route |
|---|-------------|---------|-------|
| US9 | Admin | Démarrer une session | `POST /sessions/start` |
| US10 | Admin | Voir le dashboard des sessions actives | `GET /admin/sessions` |
| US11 | Admin | Terminer une session | `POST /sessions/{id}/end` |
| US12 | Admin | Consulter l'historique | `GET /admin/sessions/history` |

---

## Installation

### Prérequis
- PHP >= 8.1
- MySQL >= 5.7
- Composer
- Apache avec `mod_rewrite` activé

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/Rayantoufella/Aji-nle3bo-Cafe-Manager
cd Aji-nle3bo-Cafe-Manager

# 2. Installer les dépendances
composer install

# 3. Configurer la base de données
cp config/config.example.php config/config.php
# → Éditer config.php avec vos credentials DB

# 4. Importer le schéma SQL
mysql -u root -p aji_l3bo < Db.sql

# 5. Configurer Apache
# → Pointer le DocumentRoot vers /public
# → Activer mod_rewrite

# 6. Tester
php -S localhost:8000 -t public/
```

---

## Équipe & Répartition des Tâches

| Dev | Domaine | Fichiers |
|-----|---------|----------|
| **Dev 1** | Models BASE | `DatabaseModel.php`, `CategoryModel.php` |
| **Dev 2** | Models AUTH | `UserModel.php`, `AdminModel.php` |
| **Dev 3** | Models MÉTIER | `GameModel.php`, `TableModel.php` |
| **Dev 4** | Models SESSION/RÉSA | `SessionModel.php`, `ReservationModel.php` |
| **Dev 5** | Controllers AUTH | `AuthController.php` |
| **Dev 6** | Controllers MÉTIER | `GamesController.php`, `ReservationController.php`, `SessionController.php`, `AdminController.php` |
| **Dev 7** | Views AUTH | `auth/`, `user/profile.php`, `layout/` |
| **Dev 8** | Views MÉTIER | `games/`, `reservations/`, `sessions/`, `admin/` |

---

## Workflow Git

```
main
 └── develop
       ├── feature/auth-module
       ├── feature/games-catalog
       ├── feature/reservations
       └── feature/sessions-dashboard
```

- **Branches** : une par feature / US
- **Commits** : `feat:`, `fix:`, `refactor:`, `docs:`
- **Merge** : Pull Request obligatoire + code review avant merge

---

## Technologies

![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-PSR--4-885630?style=flat&logo=composer&logoColor=white)
![Apache](https://img.shields.io/badge/Apache-mod__rewrite-D22128?style=flat&logo=apache&logoColor=white)

---

*Aji L3bo Café — Projet Freelance | Formation DWWM 2025-2026*