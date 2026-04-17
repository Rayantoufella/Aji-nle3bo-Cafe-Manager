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
$router->get('/games', 'GamesController@index');
$router->get('/games/filter', 'GamesController@filter');
$router->get('/games/{id}', 'GamesController@show');
$router->post('/games', 'GamesController@store');
$router->post('/games/delete', 'GamesController@delete');
$router->get('/games/edit/{id}', 'GamesController@update');
$router->post('/games/edit/{id}', 'GamesController@update');

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
$router->post('/admin/deleteUser', 'AdminController@deleteUser');
$router->post('/admin/editGame', 'AdminController@editGame');
$router->post('/admin/deleteGame', 'AdminController@deleteGame');
$router->post('/admin/cancelReservation', 'AdminController@cancelReservation');

// ===== ROUTES SESSIONS =====
$router->get('/sessions', 'SessionController@index');
$router->get('/sessions/{id}', 'SessionController@show');
$router->post('/sessions', 'SessionController@store');
// ===== ROUTES USER DASHBOARD =====
$router->get('/dashboard', 'UserController@dashboard');

return $router;
