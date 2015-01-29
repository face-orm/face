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