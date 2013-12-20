face
====
[![Build Status](https://drone.io/github.com/laemons/face/status.png)](https://drone.io/github.com/laemons/face/latest)

Be aware that face is under active development.

Face is an ORM built under a few purposes :
 * Performances : unlike some ORM face tries to add as few layers as possible then performances are not impacted a lot.
 * Ease of use  : Face is an ORM. That means that it aims to speed up application development and
 makes developers experience more comfortable with database interactions.
 * Understandable : Face doesn't try to reinvent the wheel. It does what you ask him to do and you don't have to learn
 or setup hundred of component for starting a new project. Moreover it wont make more than you ask it to do.
 * Powerful : performances are something, but we still thing about strength. Face can join your data
 and you can  write complex queries thanks to a custom language close of SQL


Face is tested and safe enough for production. Right know it is perfectly suited for little and medium projects.


Quick Overview
--------------

### SelectBuilder

An api is available to select datas from the db

```php
$fQuery = Tree::faceQueryBuilder();
// execute the query through the pdo object
$trees = Face\ORM::execute($fQuery, $pdo);
// => $trees is an array of Tree from the DB
```


```php
$fQuery = Tree::faceQueryBuilder();
$fQuery->join("Lemon"); // now join some lemons
$fQuery->where("~age >= 5 AND ~Lemon.mature = 1 "); // only mature lemons and trees aged of  5 years or more

$trees = Face\ORM::execute($fQuery, $pdo);
// => $trees is an array of Tree and each Tree contains the joined Lemon

foreach($trees as $tree){
    $lemons = $tree->getLemons();
    echo "Tree aged of " $tree->getAge() . " years has " . count($lemons) . " mature lemons <br/>";
}

// Tree aged of 10 years has 20 lemons
// Tree aged of 6 years has 7 lemons
```


### FaceQL

FaceQL is an important feature of Face. That's a Query language close of SQL.
It allows you to create complex queries that the Select Builder can't build.

```php
use \Face\Sql\Query\FaceQL;
$fql=FaceQL::parse(
    "SELECT::* FROM::Keyword ".
    "JOIN::Article AND ~Article.lang=:lang ".
    "WHERE (~Article.date < DATE(NOW()) OR ~Article.date IS NULL ) ".
    "GROUP BY ~Article.id ".
    "HAVING count(~Article.id)>3"
);
```

### Insertions and Updates

```php
use \Face\Sql\Query\SimpleInsert;

// prepare a tree
$tree = new Tree();
$tree->setAge(20);

// Insert it
$insert = new SimpleInsert($parsing);
$insert->execute($pdo);
// Get the generated id
$insertId = $pdo->lastInsertId();
```

```php
use \Face\Sql\Query\SimpleUpdate;

// prepare an existing tree (assuming that tree with id 1 exists in the db)
$tree = new Tree();
$tree->setId(1);
$tree->setAge(20);

// update it
$update = new SimpleUpdate($parsing);
$update->execute($pdo);
```

Support
----------

You may [open an issue](https://github.com/laemons/face/issues) or tweet [@Sneakybobito](https://twitter.com/SneakyBobito) (that's me) for support


See
--------

Site and docs are available at : http://face-orm.org (documentation is still being written)

simple benchmark is available at : https://github.com/laemons/ORM-benchmark



Roadmap
---------

Important
 * many to many seamless relationship
 * implied hydration
 * cache model implementation
 * performances updates on repetitive tasks
 * chain update/insert/delete
 * improving FaceQL
 * support for subquery
 * support for transactions
 * support for debug optimisation
 * fast queryall/queryone

Later
 * annotation models reader
 * face admin - crud
 * easy cache
 * graphical generator/customizer/visualizer
 * graphical grid editor api (e.g for jquery datatable)

Future
 * embryo for workbench .mwb files
