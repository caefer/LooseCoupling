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
      $this->getInvoker()->hasAccessor($alias, 'fetchLooselyCoupledRelation');
    }
  }

  public function fetchLooselyCoupledRelation($load, $field)
  {
    $record = $this->getInvoker();
    $record->clearAccessor($field);

    if($record->hasReference($field))
    {
      return $record->reference($field);
    }

    $record->loadReference($field);
    $collection = $this->filterWrongRelations($record, $field);
    $record->setRelated($field, $collection);

    return $collection;
  }

  protected function filterWrongRelations($record, $field)
  {
    $collection = $record->reference($field);
    foreach($collection as $key => $related)
    {
      if($related['obj_type'] != $record->getTable()->getComponentName())
      {
        $collection->remove($key);
      }
    }
    return $collection;
  }
}
