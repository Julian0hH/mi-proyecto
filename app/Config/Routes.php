<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ========================================
// RUTAS PÚBLICAS PRINCIPALES
// ========================================
$routes->get('/', 'Home::index');

// ========================================
// AUTENTICACIÓN
// ========================================
$routes->get('login', 'AuthController::login');
$routes->post('login/procesar', 'AuthController::procesarLogin');
$routes->get('logout', 'AuthController::logout');

// ========================================
// GESTIÓN DE USUARIOS
// ========================================
$routes->get('registro', 'Home::registro');
$routes->post('guardar', 'Home::guardar');
$routes->get('eliminar/(:any)', 'Home::eliminar/$1');

// ========================================
// SERVICIOS
// ========================================
$routes->get('servicios', 'Home::servicios');
$routes->get('detalles', 'Home::detalles');
$routes->get('contratar', 'Home::contratar');

// ========================================
// VALIDACIÓN DE FORMULARIOS
// ========================================
$routes->get('validacion', 'Home::validacion');
$routes->post('procesar_validacion', 'Home::procesar_validacion');

// ========================================
// CARRUSEL (Público - Solo Vista)
// ========================================
$routes->get('carrusel', 'CarruselController::index');
$routes->get('carrusel/listar', 'CarruselController::listar');

// ========================================
// PROYECTOS (Público)
// ========================================
$routes->get('proyectos', 'ProyectosController::index');
$routes->get('proyectos/listar', 'ProyectosController::listar');

// ========================================
// PANEL DE ADMINISTRACIÓN (Protegido)
// ========================================
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    // Proyectos Admin
    $routes->get('proyectos', 'ProyectosController::admin');
    $routes->post('proyectos/crear', 'ProyectosController::crear');
    $routes->post('proyectos/actualizar/(:num)', 'ProyectosController::actualizar/$1');
    $routes->delete('proyectos/eliminar/(:num)', 'ProyectosController::eliminar/$1');
    
    // Carrusel Admin
    $routes->post('carrusel/subir', 'CarruselController::subir');
    $routes->post('carrusel/actualizar/(:num)', 'CarruselController::actualizar/$1');
    $routes->delete('carrusel/eliminar/(:num)', 'CarruselController::eliminar/$1');
});

// ========================================
// RUTAS DE DESARROLLO/DEBUG
// ========================================
$routes->get('prueba_error', 'Home::prueba_error');