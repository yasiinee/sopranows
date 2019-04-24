<?php


class STOCK extends API {
    //protected $paged = true;

    protected $debug = true;
    protected $page_count = 5; // optional (default 20)
    public static $doc = "Stock list";

    protected static $input = array(
        "type_article"   => "optional:set:consommable|ingredient|preparation",
    );

    protected $sql = "SELECT 
                      a.id ,
                      a.unite ,
                      a.nom ,  
                      a.prix ,  
                      a.seuil ,  
                      s.quantite  
                      FROM stock s 
                      Left JOIN article a 
                      ON s.article_id=a.id
                      WHERE a.type IN  {{sql_filtre_article}}";

    protected static $output = Array(
        "*"=>"*",
    );

    protected function pre_treatment(){
        $s_types_article = '';
        if (isset($this->input_data["type_article"])) {
            $sep = '';
            $types_article = explode('|',$this->input_data["type_article"]);
            foreach($types_article as $type_article) {
                $s_types_article .= $sep.$this->db->quote($type_article) ;
                $sep = ' , ';
            }
        }
        $s_types_article = (!empty($s_types_article))? $s_types_article : "'consommable','ingredient','preparation'";
        $sql_filtre_article =  " ( ".$s_types_article." ) ";
        $this->InjectSQL('sql_filtre_article', $sql_filtre_article);
    }

    protected function post_treatment(){

    }

}
