<?php
class Doctrine_Hydrator_RecordCoupledDriver extends Doctrine_Hydrator_RecordDriver
{
  protected $_looseObjects = array();
  protected $_applicableAliases = null;

  public function hydrateResultSet($stmt)
  {
    $this->rootAlias = key($this->_queryComponents);
    $results = parent::hydrateResultSet($stmt);
    $this->_collectObjects();
    return $results;
  }

  protected function _gatherRowData(&$data, &$cache, &$id, &$nonemptyComponents)
  {
    $rowData = parent::_gatherRowData($data, $cache, $id, $nonemptyComponents);
    foreach($this->getApplicableAliases($rowData) as $alias)
    {
      if(!empty($rowData[$alias]['obj_type']) && !empty($rowData[$alias]['obj_pk']))
      {
        $this->_looseObjects[$rowData[$alias]['obj_type']][$rowData[$alias]['obj_pk']] = $rowData[$alias]['obj_pk'];
        $rowData[$alias]['Object'] = &$this->_looseObjects[$rowData[$alias]['obj_type']][$rowData[$alias]['obj_pk']];
      }
    }
    return $rowData;
  }

  protected function getApplicableAliases($rowData = array())
  {
    if(is_null($this->_applicableAliases))
    {
      $this->_applicableAliases = array();
      foreach(array_keys($rowData) as $alias)
      {
        if(array_key_exists('obj_type', $rowData[$alias]) && array_key_exists('obj_pk', $rowData[$alias]))
        {
          $this->_applicableAliases[] = $alias;
        }
      }
    }

    return $this->_applicableAliases;
  }

  protected function _collectObjects()
  {
    foreach($this->_looseObjects as $type => $ids)
    {
      $table      = Doctrine_Core::getTable($type);
      $identifier = $table->getIdentifierColumnNames();

      if(1 != count($identifier))
      {
        throw new Doctrine_Hydrator_Exception("Couldn't hydrate. LooseCoupling does not support multi column primary keys!.");
      }

      $objects = $table->createQuery('o')
                       ->whereIn('o.'.$identifier[0], array_keys($ids))
                       ->execute(array(), Doctrine_Core::HYDRATE_RECORD);

      foreach($objects as $object)
      {
        $this->_looseObjects[$type][$object->id] = $object;
      }
    }
  }
}
