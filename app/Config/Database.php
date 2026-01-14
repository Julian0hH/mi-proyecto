<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations
     * and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * The name of the default database group.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     * Los valores se sobrescriben en el constructor con las variables de entorno.
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => '',
        'username'     => '',
        'password'     => '',
        'database'     => '',
        'DBDriver'     => 'Postgre', // Predefinido a Postgre para evitar error de MySQLi
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
        'port'         => 5432,
    ];

    /**
     * This database connection is used when
     * running PHPUnit database tests.
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => 'utf8_general_ci',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
    ];

    public function __construct()
    {
        parent::__construct();

        // --------------------------------------------------------------------
        // CARGA DE VARIABLES DE ENTORNO (RENDER / .ENV)
        // --------------------------------------------------------------------
        
        // Forzamos el driver a Postgre por si acaso
        $this->default['DBDriver'] = 'Postgre';

        // Leemos las variables. Si no existen (local sin .env), quedan vacías o usan el default de arriba.
        // El operador '?:' asigna el valor solo si getenv devuelve algo.
        
        if ($hostname = getenv('database.default.hostname')) {
            $this->default['hostname'] = $hostname;
        }

        if ($database = getenv('database.default.database')) {
            $this->default['database'] = $database;
        }

        if ($username = getenv('database.default.username')) {
            $this->default['username'] = $username;
        }

        if ($password = getenv('database.default.password')) {
            $this->default['password'] = $password;
        }

        if ($port = getenv('database.default.port')) {
            $this->default['port'] = (int)$port;
        }

        // Si necesitas especificar el esquema de Postgres (opcional)
        // $this->default['schema'] = 'public';

        // Ensure that we always set the database group to 'tests' if
        // we are currently running an automated test suite.
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}