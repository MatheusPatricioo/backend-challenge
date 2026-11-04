<?php

use Phinx\Migration\AbstractMigration;

class CreateCategoryTranslations extends AbstractMigration
{
    public function up()
    {
        $this->table('category_translation')
            ->addColumn('category_id', 'integer', ['null' => false])
            ->addColumn('lang_code', 'string', ['null' => false])
            ->addColumn('label', 'string', ['null' => false])
            ->addForeignKey('category_id', 'category', 'id', ['delete' => 'CASCADE'])
            ->addIndex(['category_id', 'lang_code'], ['unique' => true])
            ->create();
    }

    public function down()
    {
        $this->table('category_translation')->drop()->save();
    }
}
