<?php

class m131225_142023_create_field_new_transport extends CDbMigration
{
	public function up()
	{
	    $transaction=$this->getDbConnection()->beginTransaction();
            try
            {
                  $this->addColumn('transport', 'new_transport', 'int');
                
                $transaction->commit();
            }
            catch(Exception $e)
            {
                echo "Exception: ".$e->getMessage()."\n";
                $transaction->rollback();
                return false;
            }
	}

	public function down()
	{
		echo "m131225_142023_create_field_new_transport does not support migration down.\n";
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