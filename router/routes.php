<?php

$router = new \App\Router\Router();

// ===== ROUTES AUTH =====
$router->get('/', 'AuthController@showLoginForm');
$router->get('/login', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@Login');
$router->get('/register', 'AuthController@showRegisterForm');
$router->post('/register', 'AuthController@Register');
$router->get('/logout', 'AuthController@Logout');

// ===== ROUTES GAMES =====
$router->get('/games', 'GameController@index');
$router->get('/games/{id}', 'GameController@show');
$router->get('/games/filter', 'GameController@filter');
$router->post('/games', 'GameController@store');
$router->post('/games/delete', 'GameController@delete');
$router->get('/games/edit/{id}', 'GameController@update');
$router->post('/games/edit/{id}', 'GameController@update');

// ===== ROUTES CATEGORIES =====
$router->get('/category', 'CategorieController@index');
$router->post('/category', 'CategorieController@addCategory');
$router->get('/category/delete/{id}', 'CategorieController@deleteCategory');

// ===== ROUTES RESERVATIONS =====
$router->get('/reservations', 'ReservationController@index');
$router->get('/reservations/create', 'ReservationController@create');
$router->post('/reservations', 'ReservationController@store');
$router->get('/reservations/{id}', 'ReservationController@show');
$router->post('/reservations/{id}/cancel', 'ReservationController@cancel');

// ===== ROUTES ADMIN =====
$router->get('/admin', 'AdminController@dashboard');
$router->get('/admin/users', 'AdminController@getAllUsers');
$router->get('/admin/games', 'AdminController@getAllGames');
$router->get('/admin/reservations', 'AdminController@getAllReservations');

// ===== ROUTES SESSIONS =====
$router->get('/sessions', 'SessionController@index');
$router->get('/sessions/{id}', 'SessionController@show');
$router->post('/sessions', 'SessionController@store');

return $router;
