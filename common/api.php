<?php
/**
 * Class API
 *
 *
 */

class API
{
    const FETCH_ONE     = 'one';
    const FETCH_ALL     = 'all';
    const INSERT        = 'insert';
    const UPDATE        = 'insert'; // Same as insert
    const DELETE        = 'delete';
    const DATA_KEY      = 'data'; // Where Data goes within output array (for FETCH_ONE)
    const DATA_LIST_KEY = 'list'; // Where list goes within output data sub array (for FETCH_ALL)
    const PAGE_KEY      = 'page'; // Page info goes
    const LOG_FILE_PATH = '/home/itcan/soprano/ws/common/log.json';

    // Protected : to be overriden in child classes
    protected $debug      = true;
    protected $fetch_mode = self::FETCH_ALL;
    protected $paged      = false;
    protected $page_count = 20;
    protected $input_format  = array();
    protected $output_format = array();
    protected $input_data    = array();
    protected $user_aware    = true;
    protected $user          = array();
    protected $sql           = '';
    // Private
    private   $sql_result  = array();
    private   $page_info   = array();
    protected $db          = null;
    // Public
    public    $output_data  = array();
    public    $success      = true;
    public    $last_message = '';

    public static $doc = 'API not documented yet.';
    protected static $input  = array();
    protected static $output = array();


    public static function HELP(){
        Return (static::$doc
            .'<br/><h4>Input:</h4><pre>'.json_encode(static::$input, JSON_PRETTY_PRINT).'</pre>'
            .'<br/><h4>Output:</h4><pre>'.json_encode(static::$output, JSON_PRETTY_PRINT).'</pre>');
    }

    public function IsPaged(){
        $p = '';
        switch ($this->fetch_mode){
            case static::FETCH_ONE : $p = 'ONE';
                break;
            case static::FETCH_ALL : $p = 'LIST';
                if ($this->paged) {
                    $p .= ' [PAGED]';
                }
                break;
            case static::INSERT :    $p = 'INSERT';
                break;

            case static::DELETE :    $p = 'DELETE';
                break;

        }
        return ($p);
    }

    /**
     * Set default values for parameters that are not given within input_data array
     *
     */
    protected function SetInputDefaults($defaults){
        foreach($defaults as $key=>$value) {
            if (!in_array($key, $this->input_data)) {
                $this->input_data[$key] = $value;
            }
        }
    }



    /**
     * Optioanl pre treatment mainly for reformatting or extending inputs
     * to be executed before parsing input data
     */
    protected function pre_treatment(){
        // To be implemented when needed within child classes

    }


    /**
     * Optioanl post treatment on results
     * to be executed after executing main SQL
     */
    protected function post_treatment(){
        // To be implemented when needed within child classes
        // $this->output_data['Extra'] = 'Post processing at the API level ';
    }

    /**
     * Execute a generic  API
     *
     */
    public function Run()
    {
        $this->debug(array("API CLASS" => static::class));
        $this->pre_treatment();
        $this->ValidateInputs();
        $this->debug(array("Input format" => $this->input_format));
        $this->debug(array("Input data" => $this->input_data));
        if (!$this->success) return $this->log(false);
        $this->PopulateSQL();
        if (!$this->success) return $this->log(false);
        $this->debug(array("SQL" => str_replace(["\t","\r\n","   "],[" "," "," "] , $this->sql) ) );
        // Paged treatment
        if ($this->paged){
            $this->ExecuteSQLPaged();
        }else{
            $this->ExecuteSQL();
        }
        if (!$this->success) return $this->log(false);  // JAW: LOG even if SQL Failed
        $this->debug(array("SQL Result" => $this->sql_result));
        $this->debug(array("Output format" => $this->output_format));
        $this->PopulateOutput('data');  // The result now is put in $out_data
        if (!$this->success) return $this->log(false);
        $this->post_treatment();

        $this->Log();


    }



    /**
     * API constructor.
     */
    function __construct($connection, $input_data=array(), $user=array()) {

        $this->input_format  = static::$input;
        $this->output_format = static::$output;
        $this->db   = $connection;
        if ($this->user_aware){
            $this->user = $user;
        }
        $this->input_data = $input_data;
    }

    /**
     * Input parameters validation
     *
     * Check that all needed parameters are given
     * Validate parameters values againist optional specifiers; string, integer, etc ..
     */
    protected function ValidateInputs(){
        // Tolerate "limit" param not given
        if ((isset($this->input_format['limit'])) and (!array_key_exists('limit', $this->input_data)) ) $this->input_data['limit'] = 100;
        // Add default input format keys for paged mode
        if ($this->paged) {
            if (!isset($this->input_format['page_offset'])) $this->input_format['page_offset']= "optional:number";
            if (!isset($this->input_format['page_count']) ) $this->input_format['page_count'] = "optional:number";
        }
        // Validate each parameter
        foreach ($this->input_format as $key => $val){
            // Check if parameter is optional
            if (strpos(strtoupper($val),'OPTIONAL:')===false) {
                //Not Optional => check this one
                if (!array_key_exists($key, $this->input_data)) {
                    $this->EQ("Paramètre manquant. On s'attend a avoir le paramètre suivant : " . $key);
                }
            }
        }
    }


    /**
     * Populate SQL with values of parameters
     *
     * Start by searching parameters within GET query, then POST Data
     * Check that all parameters of the SQL are parsed and replaced, otherwise return error
     */
    protected function PopulateSQL(){
        // GET Query variables
        foreach ($this->input_data as $key => $val){
            $this->sql =  str_replace('{{'.$key.'}}', $val, $this->sql);
        }
        // USER Variables
        $this->debug(array("User" => $this->user));
        foreach ($this->user as $key => $val){
            // Change ":" selector by "." selector, to keep SQL code formatting in Webstorm Editor
            $this->sql =  str_replace('{{user:'.$key.'}}', '{{user.'.$key.'}}', $this->sql);
            $this->sql =  str_replace('{{user.'.$key.'}}', $val, $this->sql);
        }
    }

    /**
     * Execute populated SQL and register results if any
     *
     */
    protected function ExecuteSQL()
    {
        try {
            // GET Query variables
            $query = $this->db->prepare($this->sql);
            if (!$query){
                return $this->EQ($this->db->errorInfo()[2]);
            };
            if (!$query->execute()){
                return $this->EQ($query->errorInfo()[2]);
            };
            // Here Query executed successfully => get results
            if ($this->fetch_mode === self::FETCH_ALL) {
                $this->sql_result = $query->fetchAll(PDO::FETCH_ASSOC);
            }
            else if ($this->fetch_mode === self::FETCH_ONE) {
                $this->sql_result = $query->fetch(PDO::FETCH_ASSOC);
            }
            else if ($this->fetch_mode === self::INSERT){
                // Get count of affected rows as sql_result
                $this->sql_result = Array(
                    "row_count"=> $query->rowCount()
                );
            }
            else if ($this->fetch_mode === self::DELETE){
                // Get count of affected rows as sql_result
                $this->sql_result = Array(
                    "row_count"=> $query->rowCount()
                );
            }
        } catch (PDOException $e) {
            $this->EQ($e->getMessage() );
        }
    }


    /**
     * Execute populated SQL in paged mode and register results if any
     *
     */
    protected function ExecuteSQLPaged()
    {
        if (strpos($this->sql, 'row_number')===false){
            return $this->EQ('Paged SQL statement error: "row_number" missing while required for paged SQL queries.');
        }
        $page_offset = (isset($this->input_data['page_offset'])) ? $this->input_data['page_offset'] : 1;
        $this->page_count = (isset($this->input_data['page_count'])) ? $this->input_data['page_count'] : $this->page_count;
        $page_end    =  $page_offset + $this->page_count-1;
        $page_sql = "SELECT *
                        FROM ( 
                          $this->sql
                        )t
                       WHERE row_number BETWEEN $page_offset AND $page_end
                     ";

        $count_sql = "SELECT count(*) as total_count
                        FROM ( 
                          $this->sql
                        )t
                     ";

        try {
            // GET Query variables
            $query = $this->db->prepare($page_sql);
            if (!$query) {
                return $this->EQ($this->db->errorInfo()[2]);
            };
            if (!$query->execute()) {
                return $this->EQ($query->errorInfo()[2]);
            };
            // Here Query executed successfully => get results
            if ($this->fetch_mode === self::FETCH_ALL) {
                $this->sql_result = $query->fetchAll(PDO::FETCH_ASSOC);
            } else if ($this->fetch_mode === self::FETCH_ONE) {
                $this->sql_result = $query->fetch(PDO::FETCH_ASSOC);
            } else if ($this->fetch_mode === self::INSERT) {
                // Get count of affected rows as sql_result
                $this->sql_result = Array(
                    "row_count" => $query->rowCount()
                );
            } else if ($this->fetch_mode === self::DELETE) {
                // Get count of affected rows as sql_result
                $this->sql_result = Array(
                    "row_count" => $query->rowCount()
                );
            }
            // Calculate total count of results
            $query_count = $this->db->prepare($count_sql);
            if (!$query_count) {
                return $this->EQ($this->db->errorInfo()[2]);
            };
            if (!$query_count->execute()) {
                return $this->EQ($query_count->errorInfo()[2]);
            };
            // Here Query executed successfully => get total count
            $this->page_info["total_count"] = $query_count->fetch(PDO::FETCH_ASSOC)['total_count'];
            $this->page_info["page_offset"] = $page_offset;
            $this->page_info["page_count"]  = $this->page_count;
        } catch (PDOException $e) {
            $this->EQ($e->getMessage());
        }
    }

    /**
     * Helper recursive function that matches $results with their position in
     * output data following the $output specifier array
     *
     * @param $array
     * @param $result
     */
    protected function recursive_fill( &$out_format,  $out_data){
        foreach ($out_format as $key => $val) {
            if (is_array($val)) {
                $this->recursive_fill($out_format[$key], $out_data);
            } else if (is_string($val)) {
                // If not array then normally it is a string
                if ($key === '*') {
                    // All the results directly within this array(raw)
                    unset($out_format[$key]);
                    if (is_array($out_data)){
                        $out_format = array_merge($out_format, $out_data);
                    }
                }
                else if ($val === '*') {
                    // All the results as value of this key (raw)
                    $out_format[$key] = $out_data;
                }
                // Check if it's a specifier
                else if (strpos($val, ':1:')===0) {
                    // A unique value , not to set here
                    unset($out_format[$key]);
                }
                else if (strpos($val, ':')===0) {
                    $data_key = ltrim($val,':');
                    // Check if output parameter is optional
                    $optional_param = false;
                    if (strpos($data_key, 'optional:')===0){
                        $optional_param = true;
                        $data_key = substr($data_key,9);
                    }
                    if (array_key_exists($data_key, $out_data)) {
                        $out_format[$key] = $out_data[$data_key];
                    }
                    else if ($optional_param===true){
                        // Optional and not found Key => just unset it
                        unset ($out_format[$key]);
                    }
                }
            }
        }
    }


    /**
     * Populate the output array with results from exeuted SQL
     * following the output array structure
     *
     *
     */
    protected function PopulateOutput($data_key=self::DATA_KEY, $list_key=self::DATA_LIST_KEY, $page_key=self::PAGE_KEY)
    {
        // Check if it is an insert
        if ($this->fetch_mode === 'JAW_TEMP' /* self::INSERT */) {

        } // Check if sql_result is array of arrays
        else if ($this->fetch_mode === self::FETCH_ALL) {
            if (!isset($this->output_data[$data_key])) {
                $this->output_data[$data_key] = array();
            }
            $this->output_data[$data_key][$list_key] = array();
            // Array of arrays result from 'fetchAll'
            if (is_array($this->sql_result) and isset($this->sql_result[0]) and is_array($this->sql_result[0])) {
                foreach ($this->sql_result as $index => $row) {
                    // Format a row
                    $row_formatted = $this->output_format;
                    // Fill the template from the sql results
                    $this->recursive_fill($row_formatted, $row);
                    array_push($this->output_data[$data_key][$list_key], $row_formatted);
                }
                // Append "header" fields marked by ":1:field_name"
                foreach ($this->output_format as $key => $val) {
                    // Cleanup recurring fields
                    if (strpos($val, ':1:') === 0) {
                        // A unique value , add it to the root
                        $this->output_data[$data_key][$key] = substr($val, 2);
                    }
                }
                // We will take data from the first row
                $this->recursive_fill($this->output_data[$data_key], $this->sql_result[0]);
            }
        }
        else if (($this->fetch_mode === self::FETCH_ONE)
            or ($this->fetch_mode === self::DELETE)
            or ($this->fetch_mode === self::INSERT)) {
            // Simple array rsult from 'fetch'
            // Normalize unique fields ':1:xxx' => ':xxx'
            foreach ($this->output_format as $key => $val) {
                if ((is_string($val)) AND (strpos($val, ':1:') === 0)) {
                    $this->output_format[$key] = substr($val, 2);
                }
            }
            // Get intial empty data from output template array
            $this->output_data[$data_key] = $this->output_format;
            // Fill the template from the sql results
            $this->recursive_fill($this->output_data[$data_key], $this->sql_result);
        }
        if (isset($this->page_info) and !empty($this->page_info)) {
            // Add page informations for paged mode
            $this->output_data[$page_key] = $this->page_info;
        }
    }






    /**
     * Add variable to Debug array within output_data
     * @param $var
     *
     */
    protected function debug($var){
        if (!$this->debug) return;
        if (!isset($this->output_data['debug'])) {
            $this->output_data['debug'] = array();
        }
        array_push($this->output_data['debug'], $var);
    }

    /**
     * Quee error ; add error to 'errors' array in $output_data
     * @param $err
     *
     */
    protected function EQ($err, $key='errors'){
        $this->success = false;
        if (!isset($this->output_data[$key])) {
            $this->output_data[$key] = array();
        }
        array_push($this->output_data[$key], $err);
        $this->last_message = $err;
    }

    /**
     * Log JSON result
     *
     */
    protected function Log($success=True)
    {
        if (!$this->debug) return $success;
        $sep=', ';
        if (filesize(self::LOG_FILE_PATH) > 1000000) {
            file_put_contents(self::LOG_FILE_PATH, $sep.json_encode($this->output_data));
        }else {
            file_put_contents(self::LOG_FILE_PATH, $sep.json_encode($this->output_data) , FILE_APPEND);
        }
        return $success;
    }

    /**
     * Replace a tag in SQL by the given value and tolerate SQL injection
     * Useful for preprocessing phase , as we may build parts of SQL query at execution time
     * No escaping, no 'quote' funciton
     *
     * @param $key : tag to be replaced
     * @param $value : typically a portion of SQL query
     */
    protected function InjectSQL($key, $value)
    {
        // Replace tags in SQL without anti sql injection treatment (No quote function !)
        $this->sql = str_replace('{{'.$key.'}}', $value, $this->sql);
    }
}