<?php

class MAGASIN extends API {
    // protected $paged = true;
    protected $fetch_mode = self::FETCH_ALL;
    protected $user_aware = true;

    public static $doc = "Liste des magasins";

    protected static $input = array(
        "id" => "optional:number",
    );

    protected $sql = "SELECT * FROM magasin";

    protected $sql_one = "SELECT * FROM magasin WHERE id='{{id}}' ";

    protected static $output = Array(
        "*"=>"*",
    );

    protected function pre_treatment(){
        if ( (isset($this->input_data['id']))){
            $this->sql = $this->sql_one;
        }
    }

    protected function post_treatment(){

    }

}
