<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearMensajes extends Migration
{
    public function up() {
    $this->forge->addField([
        'id'      => ['type' => 'SERIAL', 'unsigned' => true], // SERIAL es mejor para Postgres
        'nombre'  => ['type' => 'VARCHAR', 'constraint' => '100'],
        'mensaje' => ['type' => 'TEXT'],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->createTable('mensajes');
}

    public function down()
    {
        //
    }
}
