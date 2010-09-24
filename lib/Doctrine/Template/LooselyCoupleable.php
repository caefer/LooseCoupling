<?php
class Doctrine_Template_LooselyCoupleable extends Doctrine_Template
{
  public function setTableDefinition()
  {
    $this->hasColumn('obj_type', 'string', 48, array('type' => 'string', 'length' => 48));
    $this->hasColumn('obj_id', 'integer', 4, array('unsigned' => true));
  }

  public function getObject()
  {
    $record = $this->getInvoker();

    if(isset($record->Object))
    {
      return $record->Object;
    }
    else if(isset($record->obj_type) && isset($record->obj_id))
    {
      $record->Object = Doctrine_Core::getTable($record->obj_type)->find($record->obj_id);
      return $record->Object;
    }
    else
    {
      return null;
    }

  }

  public function setObject($object)
  {
    $record = $this->getInvoker();
    $record->obj_type = get_class($object);
    $record->obj_id   = $object->id;
    $record->Object   = $object;
  }
}
