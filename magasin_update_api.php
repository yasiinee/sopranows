<?php
include_once('common/common.php');

    class MAGASIN_UPDATE extends API {
    //protected $paged = true;
    protected $debug = false;
    //protected $page_count = 20; // optional (default 20)
    public static $doc = "Liste des magasins";

    protected static $input = array(
        "id"=>"number",
        "code" => "optional:string",
        "nom" => "optional:string",
        "image" => "optional:string",
        "adresse" => "optional:string",
        "type" => "optional:string",
    );

    protected $sql = "UPDATE magasin
                      SET code='{{code}}', nom='{{nom}}', image='{{image}}', adresse='{{adrrese}}', type='{{type}}'
                      WHERE id='{{id}}'";

    protected static $output = Array(
        "*"=>"*",
    );

    protected function pre_treatment(){

    }

    protected function post_treatment(){

    }

}
