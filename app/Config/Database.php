<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;
    public string $defaultGroup = 'default';

    public array $default = [
        'DSN'          => '',
        'hostname'     => '',
        'username'     => '',
        'password'     => '',
        'database'     => '',
        'DBDriver'     => 'Postgre',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8',
        'DBCollat'     => 'utf8_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 6543,
    ];

    public function __construct()
    {
        parent::__construct();

        // Intentar leer de nombres con puntos (CodeIgniter style) 
        // o nombres simples (Render/Standard style)
        $this->default['hostname'] = getenv('DB_HOSTNAME') ?: getenv('database.default.hostname');
        $this->default['database'] = getenv('DB_DATABASE') ?: getenv('database.default.database');
        $this->default['username'] = getenv('DB_USERNAME') ?: getenv('database.default.username');
        $this->default['password'] = getenv('DB_PASSWORD') ?: getenv('database.default.password');
        
        $port = getenv('DB_PORT') ?: getenv('database.default.port');
        if ($port) {
            $this->default['port'] = (int)$port;
        }

        // Si después de todo el hostname sigue vacío, forzamos el de Supabase para que no de error de socket
        if (empty($this->default['hostname'])) {
            $this->default['hostname'] = 'aws-0-us-west-2.pooler.supabase.com';
        }
    }
}