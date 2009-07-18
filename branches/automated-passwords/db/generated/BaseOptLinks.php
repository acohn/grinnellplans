<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseOptLinks extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('opt_links');
    $this->hasColumn('id', 'integer', 20, array('type' => 'integer', 'autoincrement' => true, 'primary' => true, 'length' => '20'));
    $this->hasColumn('userid', 'integer', 2, array('type' => 'integer', 'unsigned' => '1', 'default' => '0', 'notnull' => true, 'length' => '2'));
    $this->hasColumn('linknum', 'integer', 1, array('type' => 'integer', 'unsigned' => '1', 'length' => '1'));
  }

}