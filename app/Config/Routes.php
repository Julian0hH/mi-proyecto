<?php
use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Home::index');
$routes->get('registro', 'Home::registro');
$routes->post('guardar', 'Home::guardar');
$routes->get('eliminar/(:num)', 'Home::eliminar/$1');