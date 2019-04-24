<?php
include('common/common.php');


header('Content-Type: application/json; charset=utf-8');
header($_SERVER["SERVER_PROTOCOL"]." 200");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

//echo "file path ==>".LOG_FILE_PATH;
readfile(LOG_FILE_PATH);

// Empty file after reading
if (!isset($_GET['keep'])) {
    if (filesize(LOG_FILE_PATH) > 0) file_put_contents(LOG_FILE_PATH, '');

}
else{
    // nothing

}
exit;

