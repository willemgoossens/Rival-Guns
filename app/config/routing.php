<?php
    /*************************************
     *
    *   ROUTING
    *   Wildcards:
    *   Numbers (zero or more) -> \d*
    *   Numbers (one or more) -> \d+
    *   Word characters (letters numbers & underscores - zero or more) -> \w*
    *   Word characters (letters numbers & underscores - one or more) -> \w+
    *
    **************************************/
    define('ROUTING', [
    "pinguins/test/(\w+)/(\d*)" => 'tests/index/$2/$1',
    "crimes/(\d+)/(\d+)" => "crimes/commit/$2/$1",
    "crimes/(\d+)" => "crimes/$1",
    "crimes" => 'crimeCategories/crimes',
    "prison(/\w*)" => 'prisons$1'
    ]);