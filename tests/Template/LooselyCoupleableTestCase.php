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
        $item->obj_type = 'Model';
        $item->obj_pk = 1;
        $item->save();

        $this->assertEqual($item->obj_type, 'Model');
        $this->assertEqual($item->obj_pk, 1);
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
