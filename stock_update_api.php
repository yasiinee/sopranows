<?php


    class STOCK_UPDATE extends API {
    //protected $paged = true;
    protected $debug = true;
    protected $fetch_mode = self::FETCH_ALL;

    public static $doc = "Liste des magasins";

    protected static $input = array(
        "id"=>"number",
        "quantite" => "number",
        "commentaire" => "string",
    );

    protected $sql = " UPDATE stock
                      SET   quantite = {{quantite}}
                      WHERE article_id = {{id}}";

    protected static $output = Array(
        "*"=>"*",
    );

    protected function pre_treatment(){

    }

    protected function post_treatment(){

    }

}
