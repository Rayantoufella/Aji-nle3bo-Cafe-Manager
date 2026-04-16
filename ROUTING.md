# Routing Simplifié - Aji-nle3bo-Cafe-Manager

## 📋 Comment ça fonctionne

### Structure

```
public/index.php          → Point d'entrée unique (front-controller)
router/router.php         → Classe Router (dispatch les requêtes)
router/routes.php         → Déclaration de toutes les routes
app/controller/           → Contrôleurs (contiennent la logique)
```

### Flux d'une requête

1. `.htaccess` redirige tout vers `public/index.php?url=...`
2. `public/index.php` charge l'autoload et le routeur
3. Le routeur analyse la requête et cherche la route correspondante
4. Une route trouvée appelle le contrôleur + méthode
5. Le contrôleur exécute la logique et affiche la vue

---

## 🚀 Ajouter une route

### Format simple
```php
$router->get('/path', 'ControllerName@methodName');
$router->post('/path', 'ControllerName@methodName');
```

### Avec paramètres
```php
$router->get('/games/{id}', 'GameController@show');
// Appelle : GameController->show($id)
```

---

## ✅ Routes disponibles

### Auth
- `GET  /` → Accueil (login)
- `GET  /login` → Affiche formulaire de connexion
- `POST /login` → Traite la connexion
- `GET  /register` → Affiche formulaire d'inscription
- `POST /register` → Traite l'inscription
- `GET  /logout` → Déconnecte l'utilisateur

### Jeux
- `GET  /games` → Liste tous les jeux
- `GET  /games/{id}` → Affiche un jeu
- `GET  /games/filter` → Filtre les jeux
- `POST /games` → Crée un jeu
- `POST /games/delete` → Supprime un jeu
- `GET  /games/edit/{id}` → Affiche l'édition d'un jeu
- `POST /games/edit/{id}` → Met à jour un jeu

### Catégories
- `GET  /category` → Liste les catégories
- `POST /category` → Ajoute une catégorie
- `GET  /category/delete/{id}` → Supprime une catégorie

### Réservations
- `GET  /reservations` → Liste les réservations
- `GET  /reservations/create` → Formulaire de réservation
- `POST /reservations` → Crée une réservation
- `GET  /reservations/{id}` → Détails d'une réservation
- `POST /reservations/{id}/cancel` → Annule une réservation

### Admin
- `GET /admin` → Tableau de bord admin
- `GET /admin/users` → Liste des utilisateurs
- `GET /admin/games` → Liste des jeux
- `GET /admin/reservations` → Liste des réservations

### Sessions
- `GET  /sessions` → Liste les sessions
- `GET  /sessions/{id}` → Détails d'une session
- `POST /sessions` → Crée une session

---

## 🔧 Notes techniques

- **Namespaces supportés** : `App\Controller\*` et `App\Controllers\*`
- **Paramètres dans l'URL** : `{id}`, `{slug}`, etc.
- **404** : Retourne « Page not found »
- **500** : Erreur contrôleur ou méthode
- **BASE_URL** : Chemin du projet (défini automatiquement)

---

## 📝 Exemple d'ajout de contrôleur

1. Créer le fichier `app/controller/MyController.php`
2. Ajouter la route dans `router/routes.php` :
   ```php
   $router->get('/mypage', 'MyController@show');
   ```
3. Le routeur trouvera `App\Controller\MyController` ou `App\Controllers\MyController`

C'est tout ! 🎉

