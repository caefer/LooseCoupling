<?php

class LooselyCoupleable extends Doctrine_Template
{
  protected $_objectCache = array();
  protected $_options = array('type' => array('type'    => 'string',
                                              'length'  => 48,
                                              'primary' => false),
                              'pk'   => array('length'    => 4,
                                              'unsigned'  => true,
                                              'primary'   => false));

  public function setTableDefinition()
  {
    $this->hasColumn('obj_type', 'string',   $this->_options['type']['length'], $this->_options['type']);
    $this->hasColumn('obj_pk',   'integer',  $this->_options['pk']['length'],   $this->_options['pk']);
    $this->getInvoker()->unshiftFilter(new Doctrine_Record_Filter_LooselyCoupleable());
  }
}
