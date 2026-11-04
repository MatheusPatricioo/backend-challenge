<?php

use Phinx\Migration\AbstractMigration;

class CreateProductComments extends AbstractMigration
{
    public function up()
    {
        $this->table('product_comment')
            ->addColumn('product_id', 'integer', ['null' => false])
            ->addColumn('admin_user_id', 'integer', ['null' => false])
            ->addColumn('parent_id', 'integer', ['null' => true])
            ->addColumn('content', 'text', ['null' => false])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addForeignKey('product_id', 'product', 'id')
            ->addForeignKey('admin_user_id', 'admin_user', 'id')
            ->addForeignKey('parent_id', 'product_comment', 'id')
            ->create();

        $this->table('product_comment_like')
            ->addColumn('comment_id', 'integer', ['null' => false])
            ->addColumn('admin_user_id', 'integer', ['null' => false])
            ->addForeignKey('comment_id', 'product_comment', 'id')
            ->addForeignKey('admin_user_id', 'admin_user', 'id')
            ->addIndex(['comment_id', 'admin_user_id'], ['unique' => true])
            ->create();
    }

    public function down()
    {
        $this->table('product_comment_like')->drop()->save();
        $this->table('product_comment')->drop()->save();
    }
}
