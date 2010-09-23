<?php
class Doctrine_Template_LooselyCoupleable extends Doctrine_Template
{
  public function setTableDefinition()
  {
    $this->hasColumn('obj_type', 'string', 48, array('type' => 'string', 'length' => 48));
    $this->hasColumn('obj_id', 'integer', 4, array('unsigned' => true));
  }
}
