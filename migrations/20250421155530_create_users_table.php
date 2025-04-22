<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    public function change(): void
    {
        if (!$this->hasTable('users')) {
            $table = $this->table('users');
            $table->addColumn('username', 'string', ['limit' => 255])
                ->addColumn('email', 'string', ['limit' => 255])
                ->addColumn('password', 'string', ['limit' => 255])
                ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addColumn('is_deleted', 'boolean', ['default' => false])
                ->create();
        }
    }
}
