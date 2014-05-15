<?php

class LooselyCoupledListener extends Doctrine_Record_Listener
{
  protected $_options = array();

  public function __construct($options)
  {
    $this->_options = $options;
  }

  public function postDelete(Doctrine_Event $event)
  {
    $subject = $event->getInvoker();
    $type = get_class($subject);
    $pk = current($subject->identifier());

    foreach ($this->_options as $modelClass)
    {
      Doctrine_Query::create()
        ->delete($modelClass.' o')
        ->addWhere('o.obj_type = ?', $type)
        ->addWhere('o.obj_pk = ?', $pk)
        ->execute();
    }
  }

  public function preSave(Doctrine_Event $event)
  {
    $record = $event->getInvoker();

    foreach($this->_options as $alias => $model)
    {
      $collection = $record[$alias];
      $inserts = $collection->getInsertDiff();

      foreach($inserts as $key => $insert)
      {
        $collection->remove($key);
      }

      $collection->takeSnapshot();

      foreach($inserts as $key => $insert)
      {
        $collection->add($insert, $key);
      }
    }
  }

  public function preDqlSelect(Doctrine_Event $event)
  {
    $query = $event->getQuery();

    $relations = array_keys($this->_options);
    
    $components = $this->_getDqlCallbackComponents($query);
    foreach ($components as $alias => $component)
    {
      if (isset($component['relation']) && $event->getInvoker()->getTable()->getComponentName() == $component['relation']['localTable']->getComponentName() && $component['table']->hasTemplate('LooselyCoupleable')) {
        $query->addPendingJoinCondition($alias, $alias.'.obj_type = "'.$component['relation']['localTable']->getComponentName().'"');
      }
    }
  }

  protected function _getDqlCallbackComponents($query)
  {
    $params = $query->getParams();
    $componentsBefore = array();
    if ($query->isSubquery())
    {
      $componentsBefore = $query->getQueryComponents();
    }

    $copy = $query->copy();
    $copy->getSqlQuery($params, 0);
    $componentsAfter = $copy->getQueryComponents();

    if ($componentsBefore !== $componentsAfter)
    {
      return array_diff($componentsAfter, $componentsBefore);
    }
    else
    {
      return $componentsAfter;
    }
  }
}
