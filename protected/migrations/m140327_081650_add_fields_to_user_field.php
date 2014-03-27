<?php

class m140327_081650_add_fields_to_user_field extends CDbMigration
{
	public function up()
	{
            $transaction=$this->getDbConnection()->beginTransaction();
            try {
                $this->addColumn('user_field', 'show_intl', 'bool default false');
                $this->addColumn('user_field', 'show_regl', 'bool default true');
                
                $transaction->commit();
            }
            
            catch(Exception $e) {
                echo "Exception: ".$e->getMessage()."\n";
                $transaction->rollback();
                return false;
            }
	}

	public function down()
	{
		echo "m140327_081650_add_fields_to_user_field does not support migration down.\n";
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