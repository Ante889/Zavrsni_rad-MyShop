<?php

if($_SERVER['SERVER_ADDR'] === '127.0.0.1'){
    $url = 'http://zavrsnirad.xyz/';
    $database = [
        'server' => 'localhost',
        'name' => 'myshop',
        'user' => 'ante',
        'password' => '101910'
    ];
}else{
    $url = 'http://www.ante-online.com/';
    $database = [
        'server' => 'localhost',
        'name' => 'ante_online',
        'user' => 'ante',
        'password' => 'H3Ng03qvbXLPO8d'
    ];
}

return [

    'siteTitle' => 'Book shop',
    'url' => $url,
    'database' => $database
];

    