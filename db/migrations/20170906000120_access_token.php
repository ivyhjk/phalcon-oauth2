<?php


use Phinx\Migration\AbstractMigration;

class AccessToken extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('oauth2_access_tokens', [
            'engine' => 'InnoDB',
            'id' => true,
            'signed' => true
        ]);

        $table->addColumn('client_id', 'integer', [
            'null' => false,
            'signed' => true
        ]);

        $table->addColumn('user_id', 'integer', [
            'null' => false,
            'signed' => true
        ]);

        $table->addColumn('identifier', 'string', [
            'limit' => 80,
            'null' => false
        ]);

        $table->addColumn('expires_at', 'timestamp', [
            'null' => false
        ]);

        $table->addColumn('revoked', 'boolean', [
            'default' => 0,
            'null' => false,
            'signed' => true
        ]);

        $table->addColumn('revoked_reason', 'string', [
            'limit' => 200,
            'null' => true
        ]);

        $table->addColumn('created_at', 'timestamp', [
            'default' => 'CURRENT_TIMESTAMP',
            'null' => true
        ]);

        $table->addColumn('modified_at', 'timestamp', [
            'null' => true,
            'updated' => 'CURRENT_TIMESTAMP'
        ]);

        $table->addIndex('client_id');
        $table->addIndex('user_id');
        $table->addIndex('identifier');

        $table->create();
    }
}
