<?php
/**
 * Cnnnect to database and check user
 */
include_once('common/common.php');
include_once('common/user.php');
include_once('common/DB.php');
include_once('common/api.php');
include_once('common/route.php');
include_once('common/acl.php');
// Prepare HTTP Data Array ( Global variables )
GetHttpGlobals();

// Get token from header
$token = apache_request_headers()['x-access-token'];

// Create User Object from token
$UserObj = new USER($token);
$user = $UserObj->user;

// Connect to DB
$connexion = DB::connect();

LogHttpRequest();

// ACL
ACL::load_access($connexion);
if (! ACL::check_access($UserObj->profile,
                  parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) )
{
    return E("Access denied for Profile:$UserObj->profile on path:".parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
}








