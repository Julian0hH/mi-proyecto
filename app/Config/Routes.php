<?php
use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Home::index');
$routes->get('registro', 'Home::registro');
$routes->post('guardar', 'Home::guardar');
$routes->get('eliminar/(:num)', 'Home::eliminar/$1');
$routes->get('servicios', 'Home::servicios');
$routes->get('detalles', 'Home::detalles');
$routes->get('validacion', 'Home::validacion');
$routes->post('procesar_validacion', 'Home::procesar_validacion');