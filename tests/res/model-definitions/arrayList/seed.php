<?php

return [
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
            "class"     =>  "Lemon",
            "relatedBy" => "seeds",
            "relation"  => "belongsTo",
            "sql"   =>[
                "join"  => ["lemon_id"=>"id"]
            ]
        ]

    ]

];