<?php

include_once('connect.php');
include_once('stock_api.php');
include_once('stock_update_api.php');

$route = new ROUTE();
$route->get('/','ExecAPICascade',
             [STOCK_UPDATE::class, STOCK::class]);
$route->run();
