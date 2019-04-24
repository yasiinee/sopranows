<?php
include_once('common.php');

class HTTP extends API
{
    protected $debug = true;

    protected static $input = array(
    );

    protected $sql = "SELECT 'ADI' ";

    protected static $output = Array(
        "*"   => '*',
    );

    protected function pre_treatment(){

    }
    protected function post_treatment(){


    }

}
