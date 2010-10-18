<?php

class Doctrine_Template_LooselyCoupledListener_TestCase extends Doctrine_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function prepareTables()
    {
        parent::prepareTables();
    }

    public function prepareData()
    { }

    public function testMyTest()
    {
        $this->assertEqual(1, 1);
    }
}
