<?php
include_once('common/common.php');

    class MAGASIN_DELETE extends API {
    //protected $paged = true;
    protected $debug = false;
    //protected $page_count = 20; // optional (default 20)
    public static $doc = "Liste des magasins";

    protected static $input = array(
        "id" => "number",
    );

    protected $sql = "DELETE FROM magasin WHERE id = '{{id}}'";

    protected static $output = Array(
        "*"=>"*",
    );

    protected function pre_treatment(){

    }

    protected function post_treatment(){

    }

}
