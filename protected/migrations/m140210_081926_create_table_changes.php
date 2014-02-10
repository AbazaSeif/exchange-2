<?php

class m140210_081926_create_table_changes extends CDbMigration
{
	public function up()
	{
        $transaction=$this->getDbConnection()->beginTransaction();
        try {
            
            $this->createTable('changes', array(
                'id' => 'pk',
                'description' => 'text',
                'date' => 'timestamp',
                'user_id'=>'integer NOT NULL REFERENCES user(id)'
            ));
            $transaction->commit();
        } catch(Exception $e) {
            echo "Exception: ".$e->getMessage()."\n";
            $transaction->rollback();
            return false;
        }
	}

	public function down()
	{
		echo "m140210_081926_create_table_changes does not support migration down.\n";
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