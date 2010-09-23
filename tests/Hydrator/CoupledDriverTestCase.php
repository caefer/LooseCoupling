<?php
class Doctrine_Hydrator_CoupledDriver_TestCase extends Doctrine_UnitTestCase
{
    public function setUp()
    {
        parent::setUp();
        Doctrine_Manager::getInstance()->registerHydrator('ArrayCoupled', 'Doctrine_Hydrator_ArrayCoupledDriver');
        Doctrine_Manager::getInstance()->registerHydrator('RecordCoupled', 'Doctrine_Hydrator_RecordCoupledDriver');
    }

    public function prepareTables()
    {
        $this->tables[] = "Article";
        $this->tables[] = "Image";
        $this->tables[] = "AggregateList";
        parent::prepareTables();
    }

    public function prepareData()
    {
      $article = new Article();
      $article->title = 'Title of the article';
      $article->save();

      $image = new Image();
      $image->filename = 'my-image.jpg';
      $image->save();

      $list1 = new AggregateList();
      $list1->listname = 'first list';
      $list1->obj_id = $article->id;
      $list1->obj_type = get_class($article);
      $list1->save();

      $list2 = new AggregateList();
      $list2->listname = 'second list';
      $list2->obj_id = $image->id;
      $list2->obj_type = get_class($image);
      $list2->save();
    }

    public function testMyTest()
    {
        $this->assertEqual(1, 1);
    }

    public function testArrayCoupling()
    {
      $q = Doctrine_Core::getTable('AggregateList')->createQuery('l');
      $result = $q->execute(array(), 'ArrayCoupled');
      $this->assertEqual(2, count($result));
      $this->assertEqual('Title of the article', $result[0]['Object']['title']);
      $this->assertEqual('my-image.jpg', $result[1]['Object']['filename']);
    }

    public function testRecordCoupling()
    {
      $q = Doctrine_Core::getTable('AggregateList')->createQuery('l');
      $result = $q->execute(array(), 'RecordCoupled');
      $this->assertEqual(2, count($result));
      $this->assertEqual('Article', get_class($result[0]->Object));
      $this->assertEqual('Title of the article', $result[0]->Object->title);
      $this->assertEqual('Image', get_class($result[1]->Object));
      $this->assertEqual('my-image.jpg', $result[1]->Object->filename);
    }
}

class Article extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('my_article');
    $this->hasColumn('title', 'string', 24, array('type' => 'string', 'length' => 24));
  }
}

class Image extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('my_image');
    $this->hasColumn('filename', 'string', 24, array('type' => 'string', 'length' => 24));
  }
}

class AggregateList extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('my_aggregate_list');
    $this->hasColumn('listname', 'string', 24, array('type' => 'string', 'length' => 24));
  }

  public function setUp()
  {
    parent::setUp();
    $this->actAs('LooselyCoupleable');
  }
}

