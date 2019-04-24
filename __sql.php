<?php
include('common/common.php');

class sql_API extends API
{
    // protected $debug = false;

    protected static $input = array(
        "action" => "set:table|sql",
        "q"      => "optional:string",
    );

    private $sql_list = array (
        // "version" => " SELECT @@version",
        "table" => " SELECT TOP 10 * FROM {{q}}  ",
        "sql"   => " {{q}} ",
    );

    protected static $output = Array(
        "*"   => '*',
    );

    protected function pre_treatment(){
        if ( (isset($this->input_data['action'])) AND
             (isset($this->sql_list[$this->input_data['action']]))){
                $this->sql = $this->sql_list[$this->input_data['action']];
        }
    }

}

$method_array = ($_SERVER['REQUEST_METHOD'] === 'POST') ?  $_POST : $_GET ;

$result = ExecAPI(sql_API::class , $method_array, API::FETCH_ALL, True, False);
header('Content-Type: application/json; charset=utf-8');
header($_SERVER["SERVER_PROTOCOL"]." 200");
header('Access-Control-Allow-Origin: * ');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// Accept $data as first parameter
$json = $result;
$json['status']  = "true";
$json['message'] = "";
echo json_encode($json);
die;