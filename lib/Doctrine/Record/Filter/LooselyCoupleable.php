<?php

class Doctrine_Record_Filter_LooselyCoupleable extends Doctrine_Record_Filter
{
  public function filterSet(Doctrine_Record $record, $name, $value)
  {
    if('object' == strtolower($name))
    {
      $record->mapValue('Object', $value);
      $record->obj_type = $this->_findObjectType($value);
      $record->obj_pk   = $this->_findObjectPrimaryKey($value);
    }

    throw new Doctrine_Record_UnknownPropertyException(sprintf('*bah* Unknown record property / related component "%s" on "%s"', $name, get_class($record)));
  }

  public function filterGet(Doctrine_Record $record, $name)
  {
    if('object' == strtolower($name))
    {
      if($record->hasMappedValue('Object'))
      {
        return $record->get('Object');
      }
      else if($record['obj_type'] && $record['obj_pk'])
      {
        $record->mapValue('Object', $this->_findObject($record['obj_type'], $record['obj_pk']));
        return $record->get('Object');
      }
    }

    throw new Doctrine_Record_UnknownPropertyException(sprintf('*yuk* Unknown record property / related component "%s" on "%s"', $name, get_class($record)));
  }

  protected function _findObject($obj_type, $obj_pk)
  {
    return Doctrine_Core::getTable($obj_type)->find($obj_pk);
  }

  protected function _findObjectType($object)
  {
    return $object->getTable()->getComponentName();
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
