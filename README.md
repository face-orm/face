face
====
[![Build Status](https://travis-ci.org/face-orm/face.svg?branch=develop)](https://travis-ci.org/face-orm/face)
[![Test Coverage](https://codeclimate.com/github/face-orm/face/badges/coverage.svg)](https://codeclimate.com/github/face-orm/face)
[![Code Climate](https://codeclimate.com/github/face-orm/face/badges/gpa.svg)](https://codeclimate.com/github/face-orm/face)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/face-orm/face?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

Be aware that face is under active development. The current development phase breaks compatibilities very often.

The documentation and the site are under refactoring until a stable version is published


Why an other ORM?
-----------------

For years I have been working with no ORM and I can say I hate ORM because they are heavy, longer to learn, slower...
but i love them because it makes your development much more easy and reliable.

Why this ORM? I tryed many ORM and I wanted something that is:
- easy to learn: take the example of doctrine, the doc is really, really, really vast and it takes times to learn.
- powerfull: If I use an ORM, I dont want to define join columns on every query. Small ORMs use to be bad at this
- flexible: Flexible means configurable, it means adaptable, that can fit most of use cases. Something that is not static
- performant: big ORM are cool, really, but they are bad for big and loaded applications
- transparent: Transparent means "do not run query that I dont want you to run, just run what I explicitly ask you to run"

Taking this in consideration I built something with as few layers as possible but that still uses adapters and interfaces,
that can manage 1:1, 1:n, n:n relations, and that remains performant.




Quick Overview
--------------


### SelectBuilder

**That was not up to date anymore, doc needs to be rewritten**

### FaceQL

**CURRENT STATE**: refactoring in order to use a real parser.

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

That is equivalent to the following request ( + hydration): 


```sql
SELECT * FROM keyword 
  JOIN article on article.id = keyword.article_id AND article.lang=:lang
  WHERE article.data < DATE(NOW()) OR article.data IS NULL
  GROUP BY article.id
  HAVING COUNT(article.id)>3
```


### Insertions and Updates

**That was not up to date anymore, doc needs to be rewritten**

Configure the orm
-----------------

### The Loader

```php

// prepare the cache
$redis = new Redis();
$redis->connect("127.0.0.1");
$cache = new \Face\Cache\RedisCache($redis);

// the loader
$cacheableLoader = new \Face\Core\FaceLoader\FileReader\PhpArrayReader( "path/to/models/definitions/" );
$cacheableLoader->setCache($cache);

// the config
$config = new \Face\Config();
$config->setFaceLoader($cacheableLoader);

// Most of time we want to make this simple by using a global config
$config::setDefault($config);

// TODO: not global config

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

You found a bug or weird behaviours ? Issue tracker is where you have to go: 
[open an issue](https://github.com/face-orm/face/issues)

You want need some help to understand something, need some advices, or you just want to discuss, 
[join the gitter room](https://gitter.im/face-orm/face?utm_source=share-link&utm_medium=link&utm_campaign=share-link)


Documentation
-------------

Documentation is under renovation. It is coming back as soon as it's ready.


Benchmarking
------------

simple benchmark is available at : [https://github.com/laemons/ORM-benchmark](https://github.com/laemons/ORM-benchmark)


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
