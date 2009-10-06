<?php

class SplitPlanText extends Doctrine_Migration_Base
{
	public function up()
	{
		$this->createTable('plans', array(
             'id' => 
             array(
              'type' => 'integer',
              'autoincrement' => '1',
              'primary' => '1',
              'length' => '2',
             ),
             'user_id' => 
             array(
              'type' => 'integer',
              'length' => '2',
             ),
             'plan' => 
             array(
              'type' => 'clob',
              'length' => 196605,
             ),
             'edit_text' => 
             array(
              'type' => 'string',
              'length' => 65535,
             ),
             ), array(
             'primary' => 
             array(
              0 => 'id',
             ),
             ));
    }

    public function down()
    {
		$this->dropTable('plans');
    }
}
