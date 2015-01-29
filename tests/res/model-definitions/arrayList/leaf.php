<?php

return [
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
            "class"     =>  "Tree",
            "relation"  => "belongsTo",
            "relatedBy" => "leafs",
            "sql"   =>[
                "join"  => ["tree_id"=>"id"]
            ]
        ],


    ]

];