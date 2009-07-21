<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseBoardvotes extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('boardvotes');
    $this->hasColumn('voteid', 'integer', 4, array('type' => 'integer', 'unsigned' => '1', 'primary' => true, 'autoincrement' => true, 'length' => '4'));
    $this->hasColumn('userid', 'integer', 2, array('type' => 'integer', 'unsigned' => '1', 'default' => '0', 'notnull' => true, 'length' => '2'));
    $this->hasColumn('threadid', 'integer', 2, array('type' => 'integer', 'unsigned' => '1', 'default' => '0', 'notnull' => true, 'length' => '2'));
    $this->hasColumn('messageid', 'integer', 2, array('type' => 'integer', 'unsigned' => '1', 'default' => '0', 'notnull' => true, 'length' => '2'));
    $this->hasColumn('vote_date', 'timestamp', 25, array('type' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP', 'length' => '25'));
    $this->hasColumn('vote', 'integer', 2, array('type' => 'integer', 'length' => '2'));
  }

}