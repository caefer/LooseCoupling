<?php

class Doctrine_Template_LooselyCoupleable_TestCase extends Doctrine_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function prepareTables()
    {
        $this->tables[] = "LooselyCoupleableItem";
        $this->tables[] = "Model";
        parent::prepareTables();
    }

    public function prepareData()
    { }

    public function testMyTest()
    {
        $this->assertEqual(1, 1);
    }

    public function testForObjectRelationColumns()
    {
        $item = new LooselyCoupleableItem();
        $object = new Model();
        $object->save();
        $item->obj_type = get_class($object);
        $item->obj_pk = $object->id;
        $item->save();

        $this->assertEqual($item->obj_type, get_class($object));
        $this->assertEqual($item->obj_pk, $object->id);
        $this->assertEqual(get_class($item->getObject()), get_class($object));
    }

    public function testObjectSetter()
    {
        $item = new LooselyCoupleableItem();
        $object = new Model();
        $object->save();
        $item->setObject($object);
        $item->save();

        $this->assertEqual($item->obj_type, get_class($object));
        $this->assertEqual($item->obj_pk, $object->id);
        $this->assertEqual(get_class($item->getObject()), get_class($object));
    }
}

class LooselyCoupleableItem extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('my_loosely_coupleable_item');
    }

    public function setUp()
    {
        parent::setUp();
        $this->actAs('LooselyCoupleable');
    }
}

class Model extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('my_model');
    $this->hasColumn('name', 'string', 24, array('type' => 'string', 'length' => 24));
  }
}
