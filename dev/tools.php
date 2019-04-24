<?php
/**
 * Dev tools :
 * - Logging
 * - Debugging
 */


// Logging and Debugging tools
class Log{


   public static function txt(){
       Return (static::$doc

   }

}



/**
 * Return SQL if needed
 *
 */
function debug_sql($query, $params){
    if (isset($_GET['sql'])) {
        echo $query.'<hr><pre>';
        var_dump($params);
        echo '</pre><hr>';
    }
}

/**
 * Logging to txt file
 *
 */
function Txtlog($logline, $include_POST = false){
    $logfile = fopen('log/log.txt', 'a');
    if ($include_POST) $logline = $logline . json_encode($_POST);
    fwrite($logfile,
        date("Y-m-d H:i:s") . ' |' . $_SERVER['REMOTE_ADDR'] . '| ' . $logline . "\n");
}
