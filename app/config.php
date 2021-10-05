<?php
$localhost= false;
if($_SERVER['SERVER_ADDR'] === '127.0.0.1'){
    $url = 'http://zavrsnirad.xyz/';
    $localhost= true;

}

return [
    'localhost' => $localhost,
    'siteTitle' => 'Book shop',
    'url' => 'http://zavrsnirad.xyz/',
    'database' => [
        'server' => 'localhost',
        'name' => 'myshop',
        'user' => 'ante',
        'password' => '101910'
    ]
];

    