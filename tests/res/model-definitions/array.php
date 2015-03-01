<?php

return [

    [
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
                "entity"    => "Lemon",
                "relation"  => "hasMany",
                "relatedBy" => "tree",
                "sql"   =>[
                    "join"  => ["id"=>"tree_id"]
                ]
            ],
            "leafs"=>[
                "entity"     => "Leaf",
                "relation"  => "hasMany",
                "relatedBy" => "tree",
                "sql"   =>[
                    "join"  => ["id"=>"tree_id"]
                ]
            ],

            "childrenTrees"=>[
                "entity"     => "Tree",
                "relation"  => "hasManyThrough",
                "relatedBy" => "parentTrees",
                "sql"   =>[
                    "join"  => ["id"=>"tree_parent_id"],
                    "throughTable" => "tree_has_parent"
                ]
            ],

            "parentTrees"=>[
                "entity"     => "Tree",
                "relation"  => "hasManyThrough",
                "relatedBy" => "childrenTrees",
                "sql"   =>[
                    "join"  => ["id"=>"tree_child_id"],
                    "throughTable" => "tree_has_parent"
                ]
            ]


        ]

    ],

    [
        "sqlTable"=>"seed",
        "name"=> "seed",
        "class"=> "Seed",

        "elements"=>[
            "id"=>[
                "identifier"=>true,
                "sql"=>[
                    "isPrimary" => true
                ]
            ],
            "lemon_id"=>[
            ],
            "fertil"=>[
            ],
            "lemon"=>[
                "entity"     =>  "Lemon",
                "relatedBy" => "seeds",
                "relation"  => "belongsTo",
                "sql"   =>[
                    "join"  => ["lemon_id"=>"id"]
                ]
            ]

        ]

    ],


    [
        "sqlTable"=>"lemon",
        "name"=> "lemon",
        "class"=> "Lemon",

        "elements"=>[
            "id"=>[
                "type"=>"value",
                "identifier"=>true,
                "sql"=>[
                    "columnName"=> "id",
                    "isPrimary" => true
                ]
            ],
            "tree_id"=>[
                "type"      => "value",
                "sql"=>[
                    "columnName" => "tree_id"
                ]
            ],
            "mature"=>[
                "type"      => "value",
                "sql"=>[
                    "columnName" => "mature"
                ]
            ],
            "tree"=>[
                "entity"     =>  "Tree",
                "relatedBy" => "lemons",
                "relation"  => "belongsTo",
                "sql"   =>[
                    "join"  => ["tree_id"=>"id"]
                ]
            ],
            "seeds"=>[
                "entity"     => "Seed",
                "relation"  => "hasMany",
                "relatedBy" => "lemon",
                "sql"   =>[
                    "join"  => ["id"=>"lemon_id"]
                ]
            ]

        ]

    ],

    [
        "sqlTable"=>"leaf",
        "name"=> "leaf",
        "class"=> "Leaf",

        "elements"=>[
            "id"=>[
                "type"=>"value",
                "identifier"=>true,
                "sql"=>[
                    "columnName"=> "id",
                    "isPrimary" => true
                ]
            ],
            "tree_id"=>[
                "type"      => "value",
                "sql"=>[
                    "columnName" => "tree_id"
                ]
            ],
            "length"=>[
                "type"      => "value",
                "sql"=>[

                ]
            ],
            "tree"=>[
                "entity"     =>  "Tree",
                "relation"  => "belongsTo",
                "relatedBy" => "leafs",
                "sql"   =>[
                    "join"  => ["tree_id"=>"id"]
                ]
            ],


        ]

    ],


    [

        "name" => "C",
        "class" => "C",

        "elements"=>[
            "name"=>[
                "propertyName"=>"name",
                "type"=>"value",
            ],
        ]

    ],



    [

        "name" => "B",
        "class" => "B",

        "elements"=>[
            "name"=>[
                "propertyName"=>"name",
                "type"=>"value",
            ],
            "c"=>[
                "type"          =>  "entity",
                "entity"         =>  "C",
                "relation"      =>  "hasOne"
            ]
        ]

    ],

    [

        "name" => "A",
        "class" => "A",

        "elements"=>[
            "a"=>[
                "propertyName"  =>  "a",
                "type"          =>  "value",
                "defaultMap"    =>  "a_column",
                "relation"      =>  "hasOne"
            ],
            "b"=>[
                "propertyName"  =>  "b",
                "type"          =>  "entity",
                "entity"         =>  "B",
                "relation"      =>  "hasOne"
            ]
        ]

    ]



];