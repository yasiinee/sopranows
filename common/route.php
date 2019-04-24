<?php
/**
 * Created by PhpStorm.
 * User: Jawhar
 * Date: 30/03/2019
 * Time: 13:03
 */

class ROUTE
{
    private $route_list = [];

    public function register ($method, $route, $callback, $callback_params){
        array_push($this->route_list,
                      [
                        'method'   => $method,
                        'route'    => $route,
                        'callback' => $callback,
                        'params'   => $callback_params
                      ]
        );
    }

    public function get ($route, $callback, $callback_params=[]){
        $this->register('GET',$route, $callback, $callback_params);
    }

    public function post ($route, $callback, $callback_params=[]){
        $this->register('POST',$route, $callback, $callback_params);
    }

    public function put ($route, $callback, $callback_params=[]){
        $this->register('PUT',$route, $callback, $callback_params);
    }

    public function delete ($route, $callback, $callback_params=[]){
        $this->register('DELETE',$route, $callback, $callback_params);
    }

    public function options ($route, $callback, $callback_params=[]){
        $this->register('OPTIONS',$route, $callback, $callback_params);
    }

    public function trace ($route, $callback, $callback_params=[]){
        $this->register('TRACE',$route, $callback, $callback_params);
    }

    private function exec($key)
    {
        global $_METHOD, $_PUT, $_DELETE;

        $input_data = [];
        switch ($_METHOD) {
            case 'GET'    : $input_data = $_GET;    break;
            case 'POST'   : $input_data = $_POST;   break;
            case 'PUT'    : $input_data = $_PUT;    break;
            case 'DELETE' : $input_data = $_DELETE; break;
            case 'OPTIONS': $input_data = $_GET; break; // Todo: what array as input?
            case 'TRACE'  : $input_data = $_GET; break; // Todo: what array as input?
        }
        $params = $this->route_list[$key]['params'];
        if (!empty($params)){
            call_user_func($this->route_list[$key]['callback'], $params, $input_data);
        } else{
            call_user_func($this->route_list[$key]['callback'], $input_data);
        }
    }

    public function run(){
        // Quel rute à executer selon l'url
        // $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $route = '/';  // To calculate from url

        global $_METHOD;
        foreach ($this->route_list as $key => $val){
            if (($val['method']===$_METHOD) AND ($val['route']===$route)){
                $this->exec($key);
            }
        }
    }



}