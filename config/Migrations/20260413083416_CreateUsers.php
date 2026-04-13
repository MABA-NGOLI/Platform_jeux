<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateUsers extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/5/en/migrations.html#the-change-method
     *
     * @return void
     */
   /* public function change(): void
    {
        $table = $this->table('users');
        $table->create();
    }*/
        public function change(): void
    {
      $table = $this->table('users');

        $table->addColumn('username', 'string', [
            'limit' => 50
        ])
        ->addColumn('email', 'string', [
            'limit' => 100
        ])
        ->addColumn('password', 'string', [
            'limit' => 255
        ])
        ->addColumn('created', 'datetime', [
            'default' => 'CURRENT_TIMESTAMP'
        ])
        ->create();
}
}
