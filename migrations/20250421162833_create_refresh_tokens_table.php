<?php

use Phinx\Migration\AbstractMigration;

class CreateRefreshTokensTable extends AbstractMigration
{
    public function change()
    {
        if (!$this->hasTable('refresh_tokens')) {
            $this->table('refresh_tokens')
                ->addColumn('email', 'string', ['limit' => 255])
                ->addColumn('token', 'string', ['limit' => 255])
                ->addColumn('expires_at', 'timestamp')
                ->addIndex(['email'])
                ->create();
        }
    }
}
