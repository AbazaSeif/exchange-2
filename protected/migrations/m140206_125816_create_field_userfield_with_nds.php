<?php

class m140206_125816_create_field_userfield_with_nds extends CDbMigration
{
    public function up()
    {
        $transaction=$this->getDbConnection()->beginTransaction();
        try {
            $this->addColumn('user_field', 'with_nds', 'bool');
            $transaction->commit();
        } catch(Exception $e) {
            echo "Exception: ".$e->getMessage()."\n";
            $transaction->rollback();
            return false;
        }
    }

    public function down()
    {
        echo "m140206_125816_create_field_userfield_with_nds does not support migration down.\n";
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