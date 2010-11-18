<?php

class LooselyCoupleable extends Doctrine_Template
{
  protected $_objectCache = array();

  public function setTableDefinition()
  {
    $this->hasColumn('obj_type', 'string', 48, array('type' => 'string', 'length' => 48));
    $this->hasColumn('obj_pk', 'integer', 4, array('unsigned' => true));
    $this->getInvoker()->unshiftFilter(new Doctrine_Record_Filter_LooselyCoupleable());
  }
}
