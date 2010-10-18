# LooseCoupling

LooseCoupling is a Doctrine 1 extension to allow relations to records of multiple types without foreign key constraints.

LooseCoupling provides a Doctrine behaviour and two Hydrator modes to be used in combination.

## Usage

To use this extension on any of your models you have to apply the behaviour to it.

    class YourModel extends Doctrine_Record
    {
      public function setTableDefinition()
      {
        ...
      }

      public function setUp()
      {
        $this->actAs('LooselyCoupleable');
      }
    }

Here the same as above using a schema.yml definition.

---
    YourModel:
      actAs: [LooselyCoupleable]
      columns:
        ...
The behaviour will add the two columns `obj_type` and `obj_pk` to your model and database table and provides two delegate methods `getObject()` and `setObject(Doctrine_Record $object)`.

<table>
    <tr>
        <th>id</th>
        <th>...</th>
        <th>obj_type</th>
        <th>obj_pk</th>
    </tr>
    <tr>
        <td>1</td>
        <td>...</td>
        <td>Image</td>
        <td>1</td>
    </tr>
    <tr>
        <td>2</td>
        <td>...</td>
        <td>Image</td>
        <td>2</td>
    </tr>
    <tr>
        <td>3</td>
        <td>...</td>
        <td>Article</td>
        <td>1</td>
    </tr>
</table>

> The only limitation is that only single column primary key models can be used!

The above table can be illustrated like this.

![LooseCoupling example illustration](http://yuml.me/diagram/scruffy/class/[YourModel]couples -.-[Image], [YourModel]couples -.-[Article].)

The template will also provide a virtual column `Object`.

You can now add any Doctrine_Record to YourModel as the following code demonstrates.

    $yourModel->Object = $image;
    // is the same as
    $yourModel->obj_type = get_class($image);
    $yourModel->obj_pk = $image->id;

You can also access the loosely related `Object`.

    echo $yourModel->Object;
    // will call the toString() method of the related record instance ($image in the case above)

You can also access the loosely related `Object`.

    echo $yourModel->Object;
    // will call the toString() method of the related record instance ($image in the case above)

The behaviour implements lazy loading for the `Object` property querying the database when you access the `Object` for the first time.

Of course you don't want and extra query each time you access the `Object` property for the first time. This can be quite costly especially when doing it inside a loop.

    $yourModels = Doctrine_Core::getTable('YourModel')->findAll();
    foreach($yourModels as $yourModel)
    {
      echo $yourModel->Object; // can be Image, Article, whatever, ..
    }

The above example will issue 1 query to the database to fetch all `YourModel` instances and 1 additional query per instance. Which makes __1 + N queries where N is the number of records in the YourModel table__.

This is where the two new hydrator modes come into play. Rewriting the above example using these new modes we get this.

    Doctrine_Manager::getInstance()->registerHydrator('ArrayCoupled', 'Doctrine_Hydrator_ArrayCoupled');
    Doctrine_Manager::getInstance()->registerHydrator('RecordCoupled', 'Doctrine_Hydrator_RecordCoupled');

    $yourModels = Doctrine_Core::getTable('YourModel')->findAll('RecordCoupled');
    foreach($yourModels as $yourModel)
    {
      echo $yourModel->Object; // can be Image, Article, whatever, ..
    }

By using the `RecordCoupled` hydrator the number of queries gets reduced to the minimum of 1 query to the database to fetch all `YourModel` instances and 1 additional query per type of related models. Which makes __1 + M queries where M is the number of different models related in the YourModel table__. For the table shown above this would be 3 instead of 4 queries.

There are two hydrator modes available `Doctrine_Hydrator_ArrayCoupled` and `Doctrine_Hydrator_RecordCoupled` which can replace the default `Doctrine_Core::HYDRATE_ARRAY` and `Doctrine_Core::HYDRATE_RECORD`.

Even when YourModel is only considered by i.e. a `leftJoin()` in one of your queries its loosely coupled relations will be made available.
