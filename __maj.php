<?php
include('common/common.php');

class maj_API extends API
{
    // protected $debug = false;
    protected static $input = array(
        "maj" => "string",
    );

    private $sql_maj = array (
        "_1" => " ALTER TABLE sous_famille  ADD  gestion varchar(1) NULL  ",
        "_2" => " UPDATE sous_famille SET gestion = ( SELECT TOP 1 gestion FROM article a WHERE a.sous_famille = sous_famille.code_sous_famille)",
        "_3" => " ALTER TABLE ModelesPlanning ADD ID_user integer NULL",
        "_4" => " ALTER TABLE ModelesPlanningDetail ADD Qte_liste float null",
        "_5" => " CREATE TABLE reservation_panier (
                                type_panier  varchar(1) NULL, 
                                gestion      varchar(1) NULL, 
                                ID_user      integer NULL, 
                                code         varchar(30) NULL, 
                                code_depot   varchar(50) NULL,
                                date_edition DATE DEFAULT GETDATE(), 
                                qte_panier   integer NULL, 
                                ok_sql       INT IDENTITY )",

        "_6" => " CREATE TABLE ENUMERATION ( 
                                nom_table    varchar(100) NULL, 
                                colonne      varchar(100) NULL, 
                                valeur       varchar(50) NULL, 
                                alias        varchar(50) NULL, 
                                description  varchar(200) NULL, 
                                )",

        "_5_1" => " ALTER TABLE reservation_panier 
                          ADD ok_sql INT IDENTITY",

        "_5_2" => " ALTER TABLE reservation_panier 
                     ADD  date_du  DATE DEFAULT NULL,  date_au  DATE DEFAULT NULL ",


/*
        //  Change the structur of Panier in DB => use two tables !
        "8" => " CREATE TABLE reservation_panier (
                                type_panier  varchar(1) NULL, 
                                ID_user      integer NULL, 
                                type_res     integer NULL,
                                code_depot   varchar(50) NULL,
                                date_du      DATE DEFAULT NULL,
                                date_au      DATE DEFAULT NULL,
                                date_edition DATE DEFAULT GETDATE(), 
                                ok_sql       INT IDENTITY )",

        "9" => " CREATE TABLE reservation_panier_detail (
                                type_panier  varchar(1) NULL, 
                                ID_user      integer NULL, 
                                gestion      varchar(1) NULL, 
                                code         varchar(30) NULL, 
                                date_edition DATE DEFAULT GETDATE(), 
                                qte_panier   integer NULL, 
                                ok_sql       INT IDENTITY )",
*/
        // The good Create SQL for reservation panier

        "5_v2" => " CREATE TABLE reservation_panier (
                                type_panier  varchar(1) NULL, 
                                gestion      varchar(1) NULL, 
                                ID_user      integer NULL, 
                                code         varchar(30) NULL, 
                                code_depot   varchar(50) NULL,
                            ??    type_res   varchar(50) NULL,
                            ??    libelle_res   varchar(50) NULL,
                            ??    codereservant   varchar(50) NULL,
                            ??    libelle_reservant   varchar(50) NULL,
                                date_edition DATE DEFAULT GETDATE(), 
                                qte_panier   integer NULL, 
                                ok_sql       INT IDENTITY,F
                                date_du      DATE DEFAULT NULL,
                                date_au      DATE DEFAULT NULL)",

       "53" => " ALTER TABLE reservation_panier 
                     ADD  type_res          varchar(2) NULL, 
                          libelle_res       varchar(50) NULL, 
                          codereservant     varchar(50) NULL, 
                          libelle_reservant varchar(50) NULL  ",

        "8"  => " ALTER TABLE ModelesPlanningDetail ADD ID_user integer NULL",

        "9"  => " ALTER TABLE ModelesPlanningDetail  ADD  gestion varchar(1) NULL ",

    );



    protected static $output = Array(
        "Résultat"   => ':row_count',

    );

    protected function pre_treatment(){
        if (isset($this->input_data['maj'])){
            if (isset($this->sql_maj[$this->input_data['maj']])){
                $this->sql = $this->sql_maj[$this->input_data['maj']];
            }
        }
    }

    protected function post_treatment(){

    }

}

$maj_API = new maj_API($connexion, $_GET);

$maj_API->Run(API::INSERT);
if (!$maj_API->success){
    E($maj_API->last_message, $maj_API->output_data);  // Die
}else { // Everything gone allright
    Response('Mise à jours réussit', $maj_API->output_data);
}