<?php
/**
 * Created by PhpStorm.
 * User: rbinfo
 * Date: 09/04/2019
 * Time: 11:14
 */

include_once ('connect.php');
$api = $_GET['api'];

eval ("require_once ($api.'_api.php');");
//  require ???
eval("HtmlResponse($api::HELP());");

