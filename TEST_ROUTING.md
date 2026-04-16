# 🧪 Guide de Test du Routing

## Accès aux routes

### Via navigateur (GET)

```
http://localhost/Aji-nle3bo-Cafe-Manager/
http://localhost/Aji-nle3bo-Cafe-Manager/login
http://localhost/Aji-nle3bo-Cafe-Manager/register
http://localhost/Aji-nle3bo-Cafe-Manager/games
http://localhost/Aji-nle3bo-Cafe-Manager/games/1
http://localhost/Aji-nle3bo-Cafe-Manager/category
http://localhost/Aji-nle3bo-Cafe-Manager/reservations
http://localhost/Aji-nle3bo-Cafe-Manager/admin
```

### Via formulaires ou cURL (POST)

```bash
# Exemple avec cURL
curl -X POST http://localhost/Aji-nle3bo-Cafe-Manager/login \
  -d "email=test@example.com&password=password123"

curl -X POST http://localhost/Aji-nle3bo-Cafe-Manager/register \
  -d "username=john&email=john@example.com&password=pass123&confirm_password=pass123"

curl -X POST http://localhost/Aji-nle3bo-Cafe-Manager/games \
  -d "name=Chess&category_id=1&nb_players=2&difficulty=hard"

curl -X POST http://localhost/Aji-nle3bo-Cafe-Manager/category \
  -d "cat_name=Strategy"
```

---

## Checklist de vérification

- [ ] `/login` affiche le formulaire de connexion
- [ ] `POST /login` avec identifiants valides redirige vers `/games`
- [ ] `/register` affiche le formulaire d'inscription
- [ ] `POST /register` crée un utilisateur et redirige vers `/login`
- [ ] `/games` affiche la liste des jeux
- [ ] `/games/1` affiche le détail du jeu avec ID 1
- [ ] `/category` affiche les catégories
- [ ] `/reservations` affiche les réservations
- [ ] Routes inexistantes retournent **404**

---

## Base de données

Avant de tester, assurez-vous que :

1. **MySQL est démarré** (XAMPP)
2. **La base est créée** : 
   ```sql
   mysql -u root < Db.sql
   ```
3. **Les tables existent** : `users`, `games`, `categories`, `reservations`, `sessions`

---

## Erreurs possibles

| Erreur | Cause | Solution |
|--------|-------|----------|
| 404 Page not found | Route inexistante | Vérifiez l'URL et le fichier `router/routes.php` |
| 500 Controleur introuvable | Contrôleur n'existe pas | Vérifiez le nom dans `routes.php` |
| 500 Methode introuvable | Méthode n'existe pas dans le contrôleur | Vérifiez la méthode dans le contrôleur |
| Erreur de BD | Connexion DB échouée | Vérifiez `config/config.php` et MySQL |
| Vide/Erreur PHP | Erreur dans le code | Vérifiez les logs PHP |


