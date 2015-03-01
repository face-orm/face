<?php

return [
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
            "entity"     =>  "tree",
            "relatedBy" => "lemons",
            "relation"  => "belongsTo",
            "sql"   =>[
                "join"  => ["tree_id"=>"id"]
            ]
        ],
        "seeds"=>[
            "entity"     => "seed",
            "relation"  => "hasMany",
            "relatedBy" => "lemon",
            "sql"   =>[
                "join"  => ["id"=>"lemon_id"]
            ]
        ]

    ]

];