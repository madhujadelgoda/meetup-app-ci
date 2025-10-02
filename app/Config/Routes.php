<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->post('auth/register', 'Auth::register');
$routes->post('auth/login', 'Auth::login');

$routes->get('profile', 'UserController::profile', ['filter' => 'auth']);

$routes->group('', ['filter' => 'auth'], function($routes) {
  $routes->get('profile', 'UserController::profile');
  $routes->put('profile', 'UserController::updateProfile');
});


$routes->group('events', ['filter' => 'auth'], function($routes) {
  $routes->get('', 'EventController::index');         // list all
  $routes->post('', 'EventController::create');       // create event
  $routes->get('(:num)', 'EventController::show/$1'); // get single
  $routes->put('(:num)', 'EventController::update/$1'); // update
  $routes->delete('(:num)', 'EventController::delete/$1'); // delete
});


$routes->group('events', ['filter' => 'auth'], function($routes) {
    // existing routes...
    $routes->post('(:num)/join', 'EventController::join/$1');   // join event
    $routes->post('(:num)/leave', 'EventController::leave/$1'); // leave event
});
