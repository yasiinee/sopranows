<?php
include_once('common/common.php');

    class MAGASIN_ADD extends API {
    //protected $paged = true;
    protected $debug = false;
    //protected $page_count = 20; // optional (default 20)
    public static $doc = "Liste des magasins";

    protected static $input = array(
        "code" => "string",
        "nom" => "string",
        "image" => "string",
        "adresse" => "string",
        "type" => "string",
    );

    protected $sql = "INSERT INTO magasin 
                        (code, nom, image, adresse, type) 
                        VALUES
                        ('{{code}}', '{{nom}}', '{{image}}', '{{adresse}}', '{{type}}' )
                    ";

    protected static $output = Array(
        "*"=>"*",
    );

    protected function pre_treatment(){

    }

    protected function post_treatment(){

    }

}
