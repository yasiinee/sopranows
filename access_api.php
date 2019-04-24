<?php


class ACCESS extends API {

    protected $fetch_mode = self::FETCH_ONE;
    protected $debug = true;
    public static $doc = "Check user access to given ressources";

    protected static $input = array(
        "url_list"   => "string:/ws/stock|/ws/stock_adjust",
    );

    protected $sql = "SELECT *  
                      FROM access
                      WHERE profile = 'admin'
                        AND allow='OUI'     
                        AND ( path like '/ws%'
                        or  path like '/ws/stock%')
                      ";

    protected static $output = Array(
        "*"=>"*",
    );

    protected function pre_treatment(){


    }

    protected function post_treatment(){

    }

}
