<?php

return [

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

];