face
====
[![Build Status](https://drone.io/github.com/face-orm/face/status.png)](https://drone.io/github.com/face-orm/face/latest)
[![Code Climate](https://codeclimate.com/github/face-orm/face/badges/gpa.svg)](https://codeclimate.com/github/face-orm/face)

Be aware that face is under active development. The current master branch has broken compatibilities with versiosn ``0.1.1-BETA``

The documentation and the site are under refactoring.

Face is an ORM built under a few purposes :
 * Performances : unlike some ORM, it tries to add as few layers as possible and doesn't impact performances, but it tries to keep enough flexibility to fit all cases.
 * Ease of use  : Face is an ORM. That means that it aims to speed up application development and
 makes developers experience more comfortable with database interactions.
 * Understandable : Face is very transparent. It wont run hidden queries, or other hidden task.
 * Feature Rich : performances are something, but it still thinks about strength and functionalities. 
Face includes powerful workflow to deal with relation and complex requests. 


Face is tested and safe enough for production. Right know it is perfectly suited for little and medium projects but it mays lack of maturity for biggest projects.


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

FaceQL is an important feature of Face. That's a Query language that is a mix between SQL and Face notation. 

It allows you to create complex queries that the Select Builder can't build. 
Actually everything that you can do with SQL is possible with FaceQL.



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

That is equivalent to the following request ( + hydration) : 


```sql
SELECT * FROM keyword 
  JOIN article on article.id = keyword.article_id AND article.lang=:lang
  WHERE article.data < DATE(NOW()) OR article.data IS NULL
  GROUP BY article.id
  HAVING COUNT(article.id)>3
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



Configure the orm
-----------------

### The Loader

```php

// prepare the cache
$redis = new Redis();
$redis->connect("127.0.0.1");
$redis->flushAll();
$cache = new \Face\Cache\RedisCache($redis);

// the loader
$cacheableLoader = new \Face\Core\FaceLoader\FileReader\PhpArrayReader( "path/to/models/definitions/" );
$cacheableLoader->setCache($cache);

// the config
$config = new \Face\Config();
$config->setFaceLoader($cacheableLoader);

// set it as default
$config::setDefault($config);

```

### Write entity definition

```php

<?php
// path/to/models/definitions/tree.php

return [

    "sqlTable"=>"tree",
    "name"=> "tree",
    "class"=> "Tree",
    "elements"=>[
        "id"=>[
            "identifier"=>true,
            "sql"=>[
                "columnName"=> "id",
                "isPrimary" => true
            ]
        ],
        
        "age",
        
        "lemons"=>[
            "class"     => "Lemon",
            "relation"  => "hasMany",
            "relatedBy" => "tree",
            "sql"   =>[
                "join"  => ["id"=>"tree_id"]
            ]
        ],
        "leafs"=>[
            "class"     => "Leaf",
            "relation"  => "hasMany",
            "relatedBy" => "tree",
            "sql"   =>[
                "join"  => ["id"=>"tree_id"]
            ]
        ],
        "childrenTrees"=>[
            "class"     => "Tree",
            "relation"  => "hasManyThrough",
            "relatedBy" => "parentTrees",
            "sql"   =>[
                "join"  => ["id"=>"tree_parent_id"],
                "throughTable" => "tree_has_parent"
            ]
        ],
        "parentTrees"=>[
            "class"     => "Tree",
            "relation"  => "hasManyThrough",
            "relatedBy" => "childrenTrees",
            "sql"   =>[
                "join"  => ["id"=>"tree_child_id"],
                "throughTable" => "tree_has_parent"
            ]
        ]
    ]
];


```


### The model

```php
<?php
// lib/Tree.php

class Tree {
    use \Face\Traits\EntityFaceTrait;
    public $id;
    public $age;
    public $lemons=array();
    public $leafs=array();
    public $childrenTrees=array();
    public $parentTrees = array();
    
    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function getAge() {
        return $this->age;
    }
    public function setAge($age) {
        $this->age = $age;
    }
    public function getLemons() {
        return $this->lemons;
    }
    public function setLemons($lemons) {
        $this->lemons = $lemons;
        echo "TREE : ".$this->id." SET LEMON : ".$lemons->getId().PHP_EOL;
    }
    public function getLeafs() {
        return $this->leafs;
    }
    public function setLeafs($leafs) {
        $this->leafs = $leafs;
    }
}

```



Support
-------

The best place to get support is to [open an issue](https://github.com/laemons/face/issues)


Documentation
-------------

Documentation is under renovation. It is coming back very soon.


Benchmarking
------------

simple benchmark is available at : https://github.com/laemons/ORM-benchmark


Roadmap
-------

Important
 * Literals selectors
 * Limited queries
 * implied hydration
 * cache model implementation
 * chain update/insert/delete
 * improving FaceQL
 * support for subquery
 * support for transactions
 * support for debuging
 * fast queryall/queryone/query n
 * limit on something
 * datatable extension
 * Better exception messages 
