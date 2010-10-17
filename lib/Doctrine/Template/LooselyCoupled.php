<?php

class LooselyCoupled extends Doctrine_Template
{
  protected $_options = array();

  public function __construct($options)
  {
    $this->_options = $options;
    Doctrine_Manager::getInstance()->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, true);
  }

  public function setTableDefinition()
  {
    $this->addListener(new LooselyCoupledListener($this->_options));
    foreach($this->_options as $alias => $modelName)
    {
      $this->hasMany($modelName.' as '.$alias, array('local' => 'id', 'foreign' => 'obj_pk'));
    }
  }
}
