<?php

return [

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
            "class"         =>  "B",
            "relation"      =>  "hasOne"
        ]
    ]

];