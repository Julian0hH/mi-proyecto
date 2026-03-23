<?php

namespace App\Controllers;

use App\Libraries\JwtHelper;
use App\Models\UsuarioAppModel;
use App\Models\PermisosPerfilModel;
use App\Traits\InputSanitizer;

class AuthController extends BaseController
{
    use InputSanitizer;
    public function login()
    {
        if (session()->get('admin_logueado')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Login',  'url' => '#',          'active' => true],
        ];

        return view('login_view', $data);
    }

    public function procesarLogin()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'usuario' => [
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required'   => 'El usuario o correo es obligatorio',
                    'min_length' => 'Mínimo 3 caracteres',
                ],
            ],
            'password' => [
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required'   => 'La contraseña es obligatoria',
                    'min_length' => 'La contraseña debe tener al menos 6 caracteres',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
        if (!$this->verificarCaptcha($recaptchaResponse)) {
            return redirect()->back()->withInput()
                ->with('error', 'Verificación de seguridad fallida. Intenta nuevamente.');
        }

        $usuario  = $this->sanitize($this->request->getPost('usuario'));
        $password = $this->request->getPost('password');

        if ($this->hasDangerous($usuario)) {
            return redirect()->back()->withInput()
                ->with('error', 'Caracteres no permitidos en el usuario.');
        }

        $appModel = new UsuarioAppModel();
        $appUser  = null;

        $appUser = $appModel->buscarPorUsuario($usuario);
        if (empty($appUser)) {
            $appUser = $appModel->buscarPorCorreo($usuario);
        }

        if (!empty($appUser)) {
            if (!password_verify($password, $appUser['strPwd'] ?? '')) {
                return redirect()->back()->withInput()
                    ->with('error', 'Usuario o contraseña incorrectos');
            }

            if (!($appUser['idEstadoUsuario'] ?? true)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Tu cuenta está inactiva. Contacta al administrador.');
            }

            $permisos  = [];
            $userRutas = [];
            $idPerfil  = $appUser['idPerfil'] ?? null;
            if ($idPerfil) {
                $permisosModel = new PermisosPerfilModel();
                $permisos = $permisosModel->obtenerPorPerfil((int)$idPerfil);
                foreach ($permisos as $p) {
                    if (!empty($p['strRuta'])) {
                        $userRutas[$p['strRuta']] = [
                            'bitAgregar'  => (bool)($p['bitAgregar']  ?? false),
                            'bitEditar'   => (bool)($p['bitEditar']   ?? false),
                            'bitConsulta' => (bool)($p['bitConsulta'] ?? false),
                            'bitEliminar' => (bool)($p['bitEliminar'] ?? false),
                            'bitDetalle'  => (bool)($p['bitDetalle']  ?? false),
                        ];
                    }
                }
            }

            
            $isAdmin = (bool)($appUser['perfiles']['bitAdministrador'] ?? false);

            $jwt = JwtHelper::generate([
                'uid'      => $appUser['id'],
                'tipo'     => 'app',
                'perfil'   => $idPerfil,
                'is_admin' => $isAdmin,
            ]);

            session()->set([
                'admin_logueado'  => true,
                'admin_id'        => $appUser['id'],
                'admin_email'     => $appUser['strCorreo'] ?? '',
                'admin_nombre'    => $appUser['strNombreUsuario'],
                'admin_rol'       => $isAdmin ? 'admin' : 'app',
                'user_type'       => 'app',
                'user_permisos'   => $permisos,
                'user_rutas'      => $userRutas,
                'jwt_token'       => $jwt,
            ]);

            return redirect()->to(base_url('admin/dashboard'))
                ->with('success', '¡Bienvenido, ' . $appUser['strNombreUsuario'] . '!');
        }

        $legacyModel = new \App\Models\UsuarioModel();

        $legacyUser = $legacyModel->buscarPorEmail($usuario);

        if (empty($legacyUser) || !password_verify($password, $legacyUser['password_hash'] ?? '')) {
            return redirect()->back()->withInput()
                ->with('error', 'Usuario o contraseña incorrectos');
        }

        $rolNombre = $legacyUser['roles']['nombre'] ?? 'usuario';
        if ($rolNombre !== 'admin') {
            return redirect()->back()->withInput()
                ->with('error', 'No tienes permisos para acceder al panel de administración');
        }

        if (!($legacyUser['activo'] ?? true)) {
            return redirect()->back()->withInput()
                ->with('error', 'Tu cuenta está inactiva. Contacta al administrador.');
        }

        $legacyModel->actualizarUltimoLogin($legacyUser['id']);

        $jwt = JwtHelper::generate([
            'uid'      => $legacyUser['id'],
            'tipo'     => 'admin',
            'perfil'   => null,
            'is_admin' => true,
        ]);

        session()->set([
            'admin_logueado' => true,
            'admin_id'       => $legacyUser['id'],
            'admin_email'    => $legacyUser['email'],
            'admin_nombre'   => $legacyUser['nombre'],
            'admin_rol'      => 'admin',
            'user_type'      => 'admin',
            'user_permisos'  => [],
            'jwt_token'      => $jwt,
        ]);

        return redirect()->to(base_url('admin/dashboard'))
            ->with('success', '¡Bienvenido, ' . $legacyUser['nombre'] . '!');
    }

    private function verificarCaptcha($response): bool
    {
        $secret = getenv('RECAPTCHA_SECRETKEY');
        if (empty($secret)) {
            return true;
        }
        if (empty($response)) {
            return false;
        }
        try {
            $client = \Config\Services::curlrequest();
            $verify = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret'   => $secret,
                    'response' => $response,
                    'remoteip' => $this->request->getIPAddress(),
                ],
                'timeout'     => 5,
                'http_errors' => false,
            ]);
            $body = json_decode($verify->getBody());
            return isset($body->success) && $body->success === true;
        } catch (\Throwable $e) {
            log_message('error', 'Recaptcha verification error: ' . $e->getMessage());
            return false;
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Sesión cerrada correctamente');
    }
}
