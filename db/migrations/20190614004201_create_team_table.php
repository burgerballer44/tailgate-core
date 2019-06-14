<?php

use Phinx\Migration\AbstractMigration;

class CreateTeamTable extends AbstractMigration
{
    public function up()
    {
        $team = $this->table('team', ['id' => 'team_id', 'collation' => 'utf8mb4_unicode_ci', 'signed' => false]);
        $team->addColumn('designation', 'string', ['limit' => 100])
            ->addColumn('mascot', 'string', ['limit' => 50])
            ->addColumn('created_at', 'datetime')
            ->save();
    }

    public function down()
    {
        $this->table('team')->drop()->save();
    }
}
