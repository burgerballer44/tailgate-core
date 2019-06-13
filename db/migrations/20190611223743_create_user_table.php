<?php

use Phinx\Migration\AbstractMigration;

class CreateUserTable extends AbstractMigration
{
    public function up()
    {
        $user = $this->table('user', ['id' => 'user_id', 'collation' => 'utf8mb4_unicode_ci', 'signed' => false]);
        $user->addColumn('username', 'string', ['limit' => 20])
            ->addColumn('password_hash', 'string', ['limit' => 255])
            ->addColumn('email', 'string', ['limit' => 100])
            ->addColumn('status', 'string', ['limit' => 100])
            ->addColumn('role', 'string', ['limit' => 30])
            ->addColumn('created_at', 'datetime')
            ->addIndex(['username', 'email'], ['unique' => true])
            ->save();
    }

    public function down()
    {
        $this->table('user')->drop()->save();
    }
}