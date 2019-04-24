<?php

include_once('config.php');


/**
 * Instantiate and exceute API: compatible with router
 */
function ExecAPI ($api_class, $input_data, $send_response=true)
{
    global $connexion;
    global $user;
    $api = new $api_class($connexion, $input_data, $user);
    // Check if Help needed: "/help" is the last part of url's path
    if ('/HELP' === strtoupper(substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), -5))) {
        // Return help documentation of API
        HtmlResponse($api_class::HELP());
    }
    else {
        // Execute API queries and return result
        $api->Run();
        if (!$api->success) {
            // Error occured => Die !
            E($api->last_message, $api->output_data);
        }
        // If here, Succeeded
        if ($send_response===true) {
            Response($api->output_data);
        }else{
            // Just return the output of the API execution to be used by the caller script
            return ($api->output_data);
        }
    }
};

/**
 * Instantiate and exceute API
 */
function ExecAPIList ($api_list, $last_result=true)
{
    global $connexion;
    global $user;
    $output_data = array();
    $last_output_data = array();
    $connexion->beginTransaction();
    try {
        foreach ($api_list as list($api_class, $api_input)) {
            // Get values from previous API result if they start with ":"
            foreach ($api_input as $inner_key => $val) {
                if (strpos($val, ':') === 0) {
                    $data_key = ltrim($val, ':');
                    if (array_key_exists('data', $last_output_data)) {
                        if (array_key_exists($data_key, $last_output_data['data'])) {
                            $api_input[$inner_key] = $last_output_data['data'][$data_key];
                        }
                    }
                }
            }
            $api = new $api_class($connexion, $api_input, $user);
            // Execute API queries
            $api->Run();
            array_push($output_data, $api->output_data);
            $last_output_data = $api->output_data;
            if (!$api->success) {
                // Error occured
                $connexion->rollback();
                E($api->last_message, $output_data);
            }
        }
        // Succeeded all APIs
        $connexion->commit();
        if ($last_result) {
            //$output_data = array_merge($output_data, end($output_data));
            $output_data = end($output_data);
        }
        Response($output_data);
    } catch (Exception $e) {
        $connexion->rollback();
        E("Exception: " . $e->getMessage(), $output_data);
    }
};


/** Cascade execution of API list: compatible with router
 *
 */
function ExecAPICascade ($_api_list, $input_data, $last_result=true)
{
    $api_list=[];
    foreach ($_api_list as $api) {
       array_push($api_list, [$api, $input_data]);
    }
    global $connexion;
    global $user;
    $output_data = array();
    $last_output_data = array();
    $connexion->beginTransaction();
    try {
        foreach ($api_list as list($api_class, $api_input)) {
            // Get values from previous API result if they start with ":"
            foreach ($api_input as $inner_key => $val) {
                if (strpos($val, ':') === 0) {
                    $data_key = ltrim($val, ':');
                    if (array_key_exists('data', $last_output_data)) {
                        if (array_key_exists($data_key, $last_output_data['data'])) {
                            $api_input[$inner_key] = $last_output_data['data'][$data_key];
                        }
                    }
                }
            }
            $api = new $api_class($connexion, $api_input, $user);
            // Execute API queries
            $api->Run();
            array_push($output_data, $api->output_data);
            $last_output_data = $api->output_data;
            if (!$api->success) {
                // Error occured
                $connexion->rollback();
                E($api->last_message, $output_data);
            }
        }
        // Succeeded all APIs
        $connexion->commit();
        if ($last_result) {
            //$output_data = array_merge($output_data, end($output_data));
            $output_data = end($output_data);
        }
        Response($output_data);
    } catch (Exception $e) {
        $connexion->rollback();
        E("Exception: " . $e->getMessage(), $output_data);
    }
};


/**
 * Generate a JSON response and die
 * @param $message
 * @param bool $status
 */
function Response($message='', $data=array(), $success=true ,$code=200){
    // Accept $data as first parameter
    if (is_array($message)) {
        $data = $message;
        $message = '';
    }
    $json = $data;
    $json['status']  = $success;
    $json['message'] = $message;
    header('Content-Type: application/json; charset=utf-8');
    header($_SERVER["SERVER_PROTOCOL"]." $code");
    //header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    echo json_encode($json);
    die;
}

/**
 * Generate a HTML response and die
 * @param $html
 * @param bool $status
 */
function HtmlResponse($html='', $code=200){
    header('Content-Type: text/html; charset=utf-8');
    header($_SERVER["SERVER_PROTOCOL"]." $code");
    echo $html;
    die;
}

/**
 * Generate an error response and die
 * @param $message
 * @param bool $status
 */
function E($message='', $data=Array(), $code=500){
    if (!is_array($data)) $code=$data; // Swap parameters to allow shorter calls
    $json = $data;
    $json['status']  = false;
    $json['message'] = "Erreur: ".$message;
    header('Content-Type: application/json; charset=utf-8');
    header($_SERVER["SERVER_PROTOCOL"]." $code");
    echo json_encode($json);
    die;
};


function GetHttpGlobals(){
    global $_DELETE, $_PUT, $_METHOD;
    $_METHOD = $_SERVER['REQUEST_METHOD'];
    $_DATA = [];
    parse_str(file_get_contents('php://input'),$_DATA);
    SWITCH ($_METHOD) {
        CASE 'DELETE' : $_DELETE = $_DATA;   break;
        CASE 'PUT'    : $_PUT    = $_DATA;   break;
    }
}


function LogHttpRequest ()
{
    include_once('http.php');
    global $_DELETE, $_PUT, $_METHOD;
    $url         = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $headers     = apache_request_headers();
    $user_agent  = (isset($headers['User-agent'])) ? $headers['User-agent'] : '';
    $in_data = [];
    SWITCH ($_METHOD) {
        CASE 'GET'    : $in_data = $_GET;   break;
        CASE 'POST'   : $in_data = $_POST;  break;
        CASE 'DELETE' : $in_data = $_DELETE;break;
        CASE 'PUT'    : $in_data = $_PUT;   break;
    }
    $in_data = array_merge(Array( "method" => $_METHOD,
                                  "url"    => $url,
                                  "User-Agent" => $user_agent),
                            $in_data);
    ExecAPI('HTTP',$in_data,false);
};


