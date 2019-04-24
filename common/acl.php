<?php
/**
 * Created by PhpStorm.
 * User: Jawhar
 * Date: 17/04/2019
 * Time: 18:15
 */

class ACL
{
    protected static $access = [];
    private static $db = null;
    private static $sql = "SELECT acl.id,
                                  acl.profile, 
                                  acl.path, 
                                  acl.allow
                             FROM access acl";

    static public function load_access($connexion){
        if (!empty(static::$access)) return false;
        static::$db = $connexion;
        static::reload_access();
    }

    static public function reload_access(){
        if (empty(static::$db)) return false;
        static::$access = [];
        // load from DB the access table
        $query = static::$db->prepare(static::$sql);
        if (!$query)            return false;
        if (!$query->execute()) return false;
        // Here Query executed successfully => get results
        static::$access = $query->fetchAll(PDO::FETCH_ASSOC);
    }

    static public function check_access($profile, $path){
        foreach (static::$access as $row){
            if ( ($row['profile']===$profile) AND ($row['path']===$path)){
                    return ( ($row['allow']==='OUI') ? true : false );
            }
        }
        return false;
    }
}