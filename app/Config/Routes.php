<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->post('auth/register', 'Auth::register');
$routes->post('auth/login', 'Auth::login');

$routes->get('profile', 'UserController::profile', ['filter' => 'auth']);
