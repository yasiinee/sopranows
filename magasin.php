<?php

include_once ('connect.php');

/*
// =================== ROUTING V2 ================================
include_once ('common/route.php');
$route = new ROUTE();
$route->get('/magasin', MAGASIN::class, ROUTE::API_ROUTE);
$route->put('/magasin/list', get_magasin_list);
$route->run();
*/

function get_magasin_list($input_data){
    include('magasin_api.php');
    include('magasin_add_api.php');
    ExecAPIList(
        [
            [ MAGASIN_ADD::class, $input_data ],
            [ MAGASIN::class,     $input_data ]
        ],
        true
    );
}

SWITCH ($_METHOD) {

    CASE 'GET' :
            include('magasin_api.php');
            ExecAPI(MAGASIN::class, $_GET);
        break;

    CASE 'POST' :
        include('magasin_api.php');
        include('magasin_add_api.php');
        ExecAPIList(
            [
                [ MAGASIN_ADD::class, $_POST ],
                [ MAGASIN::class,     $_POST ]
            ],
            true
        );
        break;

    CASE 'PUT' :
        include('magasin.php');
        include('magasin_add_api.php');
        include('magasin_delete_api.php');
        ExecAPIList(
            [
                [ MAGASIN_DELETE::class, $_PUT ],
                [ MAGASIN_ADD::class,     $_PUT ]
            ],
            true
        );
        break;

    CASE 'DELETE' :
        global $_DELETE;
        include('magasin_api.php');
        include('magasin_delete_api.php');
        ExecAPIList(
            [MAGASIN_DELETE::class, MAGASIN::class],
            [$_DELETE, $_DELETE],
            true
        );
        break;


}