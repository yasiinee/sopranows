<?php
include_once('connect.php');
include_once('stock_api.php');
include_once('stock_update_api.php');

function update_stock($data){
    ExecAPIList(
        [
            [ STOCK_UPDATE::class, $data ],
            [ STOCK::class,        $data ]
        ],
        true
    );
}

$route = new ROUTE();

$route->get('/', 'update_stock');
$route->run();
