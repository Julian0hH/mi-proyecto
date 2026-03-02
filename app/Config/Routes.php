<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ============================================================
// PORTAFOLIO PÚBLICO
// ============================================================
$routes->get('/', 'Home::index');
$routes->get('portafolio', 'Home::portafolio');
$routes->get('sobre-mi', 'Home::sobreMi');

// ============================================================
// SERVICIOS PÚBLICOS
// ============================================================
$routes->get('servicios', 'Home::servicios');
$routes->get('detalles/(:num)', 'Home::detalles/$1');
$routes->get('detalles', 'Home::detalles');
$routes->get('contratar/(:num)', 'Home::contratar/$1');
$routes->get('contratar', 'Home::contratar');

// ============================================================
// CONTACTO PÚBLICO
// ============================================================
$routes->get('contacto', 'ContactoController::formulario');
$routes->post('contacto/enviar', 'ContactoController::enviar');

// ============================================================
// PROYECTOS PÚBLICOS
// ============================================================
$routes->get('proyectos', 'ProyectosController::index');
$routes->get('proyectos/listar', 'ProyectosController::listar');

// ============================================================
// CARRUSEL (solo vista pública)
// ============================================================
$routes->get('carrusel', 'CarruselController::index');
$routes->get('carrusel/listar', 'CarruselController::listar');

// ============================================================
// AUTENTICACIÓN
// ============================================================
$routes->get('login', 'AuthController::login');
$routes->post('login/procesar', 'AuthController::procesarLogin');
$routes->get('logout', 'AuthController::logout');

// Recuperación de contraseña
$routes->get('recuperar-password', 'PasswordController::solicitar');
$routes->post('recuperar-password/enviar-codigo', 'PasswordController::enviarCodigo');
$routes->post('recuperar-password/verificar', 'PasswordController::verificarCodigo');
$routes->post('recuperar-password/cambiar', 'PasswordController::cambiarPassword');

// ============================================================
// REGISTRO DE USUARIOS
// ============================================================
$routes->get('registro', 'Home::registro');
$routes->post('guardar', 'Home::guardar');
$routes->get('eliminar/(:any)', 'Home::eliminar/$1');

// ============================================================
// VALIDACIÓN (formulario demo)
// ============================================================
$routes->get('validacion', 'Home::validacion');
$routes->post('procesar_validacion', 'Home::procesar_validacion');

// ============================================================
// PANEL DE ADMINISTRACIÓN (protegido con filtro auth)
// ============================================================
$routes->group('admin', ['filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->get('/', 'AdminController::dashboard');

    // Notificaciones
    $routes->get('notificaciones', 'AdminController::notificacionesJson');
    $routes->post('notificaciones/leida/(:num)', 'AdminController::marcarNotificacionLeida/$1');
    $routes->post('notificaciones/todas-leidas', 'AdminController::marcarTodasLeidas');

    // Simulador de errores
    $routes->get('simular-error', 'AdminController::simularError');

    // Sobre Mí
    $routes->get('sobre-mi', 'SobreMiController::index');
    $routes->post('sobre-mi/guardar', 'SobreMiController::guardar');

    // Servicios Admin
    $routes->get('servicios', 'ServiciosAdminController::index');
    $routes->get('servicios/listar', 'ServiciosAdminController::listar');
    $routes->post('servicios/crear', 'ServiciosAdminController::crear');
    $routes->post('servicios/actualizar/(:num)', 'ServiciosAdminController::actualizar/$1');
    $routes->delete('servicios/eliminar/(:num)', 'ServiciosAdminController::eliminar/$1');

    // Roles y Permisos
    $routes->get('roles', 'RolesController::index');
    $routes->get('roles/listar', 'RolesController::listarRoles');
    $routes->post('roles/actualizar/(:num)', 'RolesController::actualizarRol/$1');
    $routes->post('roles/asignar-usuario', 'RolesController::asignarRolUsuario');
    $routes->post('roles/toggle-usuario', 'RolesController::toggleUsuario');
    $routes->get('roles/usuarios', 'RolesController::listarUsuarios');

    // Contactos Admin (tabla avanzada)
    $routes->get('contactos', 'ContactoController::admin');
    $routes->get('contactos/listar', 'ContactoController::listar');
    $routes->get('contactos/ver/(:num)', 'ContactoController::ver/$1');
    $routes->post('contactos/actualizar/(:num)', 'ContactoController::actualizar/$1');
    $routes->delete('contactos/eliminar/(:num)', 'ContactoController::eliminar/$1');

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

// ============================================================
// DEBUG / DESARROLLO
// ============================================================
$routes->get('prueba_error', 'Home::prueba_error');
$routes->get('debug/env',    'DebugController::env');
