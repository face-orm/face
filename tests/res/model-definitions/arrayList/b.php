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
            "class"         =>  "C",
            "relation"      =>  "hasOne"
        ]
    ]

];