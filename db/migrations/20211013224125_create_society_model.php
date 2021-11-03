<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreateSocietyModel extends AbstractMigration
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
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        // Create Table Society
        $this->table('society')
            ->addColumn('s_name', 'string', ['null' => true])
            ->addColumn('about', 'text', [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG,
                'null' => true
            ])
            ->addColumn('tva_number', 'string', ['null' => true])
            ->addColumn('siret_number', 'string', ['null' => true])
            ->addColumn('naf_number', 'string', ['null' => true])
            ->addColumn('address', 'string', [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG,
                'null' => true
            ])
            ->addColumn('s_mail', 'string', ['null' => true])
            ->addColumn('postal_code', 'string', ['null' => true])
            ->addColumn('city', 'string', ['null' => true])
            ->addColumn('country', 'string', ['null' => true])
            ->addColumn('phone', 'string', ['null' => true])
            ->addColumn('phone_2', 'string', ['null' => true])
            ->addColumn('website', 'string', ['null' => true])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->create();

        $this->table('clients')
        ->addColumn('society_id', 'integer', ['null' => true])
        ->addForeignKey('society_id', 'society', 'id', [
            'delete' => 'SET_NULL',
            'update' => 'NO_ACTION'
        ])
        ->update();    
    }
}