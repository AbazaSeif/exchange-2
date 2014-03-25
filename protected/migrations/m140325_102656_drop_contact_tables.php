<?php

class m140325_102656_drop_contact_tables extends CDbMigration
{
	public function up()
	{
            $transaction = $this->getDbConnection()->beginTransaction();
            try {
                $this->dropTable('user_field_contact');
                $this->dropTable('user_contact');
                $this->addColumn('user', 'phone2', 'int after `phone`');
                $this->addColumn('user', 'parent', 'int');
                $this->addColumn('user', 'type_contact', 'int default 0');
                $transaction->commit();
            } catch (Exception $e) {
                echo "Exception: ".$e->getMessage()."\n";
                $transaction->rollback();
                return false;
            }
	}

	public function down()
	{
            echo "m140325_102656_drop_contact_tables does not support migration down.\n";
            return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}