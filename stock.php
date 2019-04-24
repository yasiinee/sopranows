<?php

include_once('connect.php');
include_once('stock_api.php');
include_once('stock_update_api.php');

function get_stock($data)
{
    ExecAPI(STOCK::class, $data);
}


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
$route->get('/','ExecAPI', STOCK::class);
$route->put('/','ExecAPICascade', [STOCK_UPDATE::class, STOCK::class]);
// $route->post('/', 'post_stock');
$route->run();

/*


*/

/*
SWITCH ($_METHOD) {
    CASE 'GET' :
        include('stock_api.php');
        ExecAPI(STOCK::class, $_GET );
        break;

    CASE 'PUT' :
        include('stock_api.php');
        include('stock_update_api.php');
        ExecAPIList(
            [
                [ STOCK_UPDATE::class, $_PUT ],
                [ STOCK::class,        $_PUT ]
            ],
            true
        );

        break;
}
