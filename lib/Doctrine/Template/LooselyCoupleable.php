<?php

class LooselyCoupleable extends Doctrine_Template
{
  protected $_objectCache = array();

  public function setTableDefinition()
  {
    $this->hasColumn('obj_type', 'string', 48, array('type' => 'string', 'length' => 48));
    $this->hasColumn('obj_pk', 'integer', 4, array('unsigned' => true));
  }

  public function getObject()
  {
    $record = $this->getInvoker();

    if(false !== ($object = $this->getCachedObject($record->obj_type, $record->obj_pk)))
    {
      return $object;
    }
    else if(isset($record->obj_type) && isset($record->obj_pk))
    {
      $object = Doctrine_Core::getTable($record->obj_type)->find($record->obj_pk);
      $this->setCachedObject($record->obj_type, $record->obj_pk, $object);
      return $object;
    }
    else
    {
      return null;
    }
  }

  public function setObject($object)
  {
    $record = $this->getInvoker();
    $record->obj_type = $this->_findObjectType($object);
    $record->obj_pk   = $this->_findObjectPrimaryKey($object);

    $this->setCachedObject($record->obj_type, $record->obj_pk, $object);
  }

  public function getCachedObject($type, $pk)
  {
    if(array_key_exists($type, $this->_objectCache) && array_key_exists($pk, $this->_objectCache[$type]))
    {
      return $this->_objectCache[$type][$pk];
    }
    return false;
  }

  public function setCachedObject($type, $pk, $object)
  {
    if(!array_key_exists($type, $this->_objectCache))
    {
      $this->_objectCache[$type] = array();
    }
    $this->_objectCache[$type][$pk] = $object;
  }

  protected function _findObjectType($object)
  {
    return get_class($object);
  }

  protected function _findObjectPrimaryKey($object)
  {
    $identifier = $object->identifier();

    if(1 != count($identifier))
    {
      throw new Doctrine_Record_Exception("Couldn't set identifier. LooseCoupling does not support multi column primary keys!.");
    }

    return current($identifier);
  }
}
