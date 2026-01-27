<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

$routes->get('registro', 'Home::registro');
$routes->post('guardar', 'Home::guardar');
$routes->get('eliminar/(:any)', 'Home::eliminar/$1');
$routes->get('servicios', 'Home::servicios');
$routes->get('detalles', 'Home::detalles');
$routes->get('contratar', 'Home::contratar');
$routes->get('validacion', 'Home::validacion');
$routes->post('procesar_validacion', 'Home::procesar_validacion');
$routes->get('prueba_error', 'Home::prueba_error');