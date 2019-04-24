<?php
include_once('common/common.php');

class TEMPLATE extends API
{
    // protected $debug = false;
    // protected $paged = true;
    // protected $page_count = 20; // optional (default 20)
    // public static $doc = "";

    protected static $input = array(
        // "param_name" => "string",
    );

    protected $sql = "SELECT  *
                        FROM  hh 
                       WHERE true  ";

    protected static $output = Array(
        //"Clé"   => ':field_name',

    );

    protected function pre_treatment(){

    }

    protected function post_treatment(){

    }

}
