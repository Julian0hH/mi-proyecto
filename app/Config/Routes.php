<?php
use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Home::index');
$routes->post('guardar_mensaje', 'Home::guardar_mensaje');
$routes->get('registro', 'Home::registro');
$routes->post('guardar_usuario', 'Home::guardar_usuario');
$routes->get('eliminar/(:num)', 'Home::eliminar/$1');
$routes->get('servicios', 'Home::servicios');
$routes->get('detalles', 'Home::detalles');
$routes->get('validacion', 'Home::validacion');
$routes->post('procesar_validacion', 'Home::procesar_validacion');
$routes->get('prueba_error', 'Home::prueba_error');
$routes->get('contratar', 'Home::contratar');