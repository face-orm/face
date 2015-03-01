<?php

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
            "entity"     => "lemon",
            "relation"  => "hasMany",
            "relatedBy" => "tree",
            "sql"   =>[
                "join"  => ["id"=>"tree_id"]
            ]
        ],
        "leafs"=>[
            "entity"     => "leaf",
            "relation"  => "hasMany",
            "relatedBy" => "tree",
            "sql"   =>[
                "join"  => ["id"=>"tree_id"]
            ]
        ],

        "childrenTrees"=>[
            "entity"     => "tree",
            "relation"  => "hasManyThrough",
            "relatedBy" => "parentTrees",
            "sql"   =>[
                "join"  => ["id"=>"tree_parent_id"],
                "throughTable" => "tree_has_parent"
            ]
        ],

        "parentTrees"=>[
            "entity"     => "tree",
            "relation"  => "hasManyThrough",
            "relatedBy" => "childrenTrees",
            "sql"   =>[
                "join"  => ["id"=>"tree_child_id"],
                "throughTable" => "tree_has_parent"
            ]
        ]


    ]

];