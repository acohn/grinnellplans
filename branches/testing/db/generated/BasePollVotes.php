<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BasePollVotes extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('poll_votes');
    $this->hasColumn('poll_vote_id', 'integer', 4, array('type' => 'integer', 'unsigned' => '1', 'primary' => true, 'autoincrement' => true, 'length' => '4'));
    $this->hasColumn('poll_choice_id', 'integer', 4, array('type' => 'integer', 'length' => '4'));
    $this->hasColumn('userid', 'integer', 4, array('type' => 'integer', 'length' => '4'));
    $this->hasColumn('created', 'timestamp', 25, array('type' => 'timestamp', 'length' => '25'));
  }

}