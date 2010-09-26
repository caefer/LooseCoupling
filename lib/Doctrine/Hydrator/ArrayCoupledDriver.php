<?php
class Doctrine_Hydrator_ArrayCoupledDriver extends Doctrine_Hydrator_ArrayDriver
{
  protected $_looseObjects = array();

  public function hydrateResultSet($stmt)
  {
    $results = parent::hydrateResultSet($stmt);
    $this->_collectObjects();
    return $results;
  }

  protected function _gatherRowData(&$data, &$cache, &$id, &$nonemptyComponents)
  {
    $rowData = parent::_gatherRowData($data, $cache, $id, $nonemptyComponents);
    foreach(array_keys($rowData) as $alias)
    {
      if(array_key_exists('obj_type', $rowData[$alias]) && array_key_exists('obj_pk', $rowData[$alias])
         && !empty($rowData[$alias]['obj_type']) && !empty($rowData[$alias]['obj_pk']))
      {
        $this->_looseObjects[$rowData[$alias]['obj_type']][$rowData[$alias]['obj_pk']] = $rowData[$alias]['obj_pk'];
        $rowData[$alias]['Object'] = &$this->_looseObjects[$rowData[$alias]['obj_type']][$rowData[$alias]['obj_pk']];
      }
    }
    return $rowData;
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
                       ->whereIn('o.'.$identifier[0], $ids)
                       ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

      foreach($objects as $objectData)
      {
        $this->_looseObjects[$type][$objectData['id']] = $objectData;
      }
    }
  }
}
