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
      $this->getInvoker()->hasAccessor($alias, 'getLooselyCoupledRelation');
    }
  }

  public function getLooselyCoupledRelation($load, $field)
  {
    $record = $this->getInvoker();
    $table = $record->getTable();

    $identifier = $record->identifier();
    $identifier = array_shift($identifier);

    return Doctrine_Query::create()
      ->parseDqlQuery($table->getRelation($field)->getRelationDql(1))
      ->addWhere('obj_type = ?')
      ->execute(array($identifier, $table->getComponentName()));
  }
}
