# 💡 EXEMPLE PRATIQUE : Ajouter une nouvelle route

## Scénario : Créer une page "À propos" (About)

### Étape 1️⃣ : Créer le contrôleur

Fichier : `app/controller/PageController.php`

```php
<?php

namespace App\Controller;

class PageController
{
    public function about()
    {
        // Récupérer les données si nécessaire
        $title = "À propos de nous";
        $content = "Nous sommes Aji L3bo Café...";
        
        // Afficher la vue
        require dirname(__DIR__) . '/views/pages/about.php';
    }
    
    public function contact()
    {
        require dirname(__DIR__) . '/views/pages/contact.php';
    }
}
?>
```

### Étape 2️⃣ : Ajouter les routes

Fichier : `router/routes.php`

Ajouter après les autres routes :

```php
// ===== ROUTES PAGES =====
$router->get('/about', 'PageController@about');
$router->get('/contact', 'PageController@contact');
$router->post('/contact', 'PageController@handleContact');
```

### Étape 3️⃣ : Créer la vue

Fichier : `app/views/pages/about.php`

```html
<?php require dirname(dirname(dirname(__DIR__))) . '/app/views/layout/header.php'; ?>

<div class="container">
    <h1><?= $title ?></h1>
    <p><?= $content ?></p>
</div>

<?php require dirname(dirname(dirname(__DIR__))) . '/app/views/layout/footer.php'; ?>
```

### Étape 4️⃣ : C'est tout ! ✅

Accédez maintenant à :
- `http://localhost/Aji-nle3bo-Cafe-Manager/about`
- `http://localhost/Aji-nle3bo-Cafe-Manager/contact`

---

## 🎯 Avec des paramètres

### Route paramétrée

```php
$router->get('/articles/{id}', 'ArticleController@show');
```

### Contrôleur

```php
public function show($id)
{
    // $id contient la valeur du paramètre
    $article = $this->articleModel->findById($id);
    require dirname(__DIR__) . '/views/articles/show.php';
}
```

### Appel

```
http://localhost/Aji-nle3bo-Cafe-Manager/articles/5
```

---

## 🔄 Avec redirection

```php
public function store()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Traiter les données
        $this->model->create($_POST);
        
        // Rediriger vers une autre page
        header('Location: ' . BASE_URL . '/articles');
        exit;
    }
}
```

---

## ✨ Points clés

✅ Toujours utiliser `BASE_URL` pour les redirections
✅ Mettre le namespace `App\Controller` ou `App\Controllers`
✅ Le constructeur ne doit pas avoir de paramètres obligatoires
✅ Les méthodes reçoivent les paramètres de l'URL


