<?php

class m140115_083137_add_auth extends CDbMigration
{
	public function up()
	{
            $transaction=$this->getDbConnection()->beginTransaction();
            try
            {
                $this->execute('
                    create table \'AuthItem\'
                    (
                       "name"                 varchar(64) not null,
                       "type"                 integer not null,
                       "description"          text,
                       "bizrule"              text,
                       "data"                 text,
                       primary key ("name")
                    );
                ');
                $this->execute('
                    CREATE TABLE [AuthItemChild] (
                        [parent] varchar(64) NOT NULL CONSTRAINT [parent] REFERENCES [AuthItem]([name]) ON DELETE cascade ON UPDATE cascade, 
                        [child] varchar(64) NOT NULL CONSTRAINT [child] REFERENCES [AuthItem]([name]) ON DELETE cascade ON UPDATE cascade, 
                        CONSTRAINT [sqlite_autoindex_AuthItemChild_1] PRIMARY KEY ([parent], [child]));
                ');
                $this->execute('
                    CREATE TABLE [AuthAssignment] (
                        [itemname] varchar(64) NOT NULL CONSTRAINT [itemname] REFERENCES [AuthItem]([name]) ON DELETE cascade ON UPDATE cascade, 
                        [userid] varchar(64) NOT NULL CONSTRAINT [userid] REFERENCES [user_groups]([id]) ON DELETE cascade ON UPDATE cascade, 
                        [bizrule] text, 
                        [data] text, 
                        CONSTRAINT [sqlite_autoindex_AuthAssignment_1] PRIMARY KEY ([itemname], [userid]));
                ');
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
            $transaction=$this->getDbConnection()->beginTransaction();
            try
            {
                $this->dropTable('AuthItemChild');
                $this->dropTable('AuthAssignment');
                $this->dropTable('AuthItem');
                $transaction->commit();
            }
            catch(Exception $e)
            {
                echo "Exception: ".$e->getMessage()."\n";
                $transaction->rollback();
                return false;
            }
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