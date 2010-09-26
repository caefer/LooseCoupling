<?php
class Doctrine_Template_LooselyCoupleable extends Doctrine_Template
{
  protected $Object;

  public function setTableDefinition()
  {
    $this->hasColumn('obj_type', 'string', 48, array('type' => 'string', 'length' => 48));
    $this->hasColumn('obj_pk', 'integer', 4, array('unsigned' => true));
  }

  public function getObject()
  {
    $record = $this->getInvoker();

    if(isset($this->Object))
    {
      return $this->Object;
    }
    else if(isset($record->obj_type) && isset($record->obj_pk))
    {
      $this->Object = Doctrine_Core::getTable($record->obj_type)->find($record->obj_pk);
      return $this->Object;
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
    $this->Object   = $object;
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
