<?php
/**
 * WS Parameters
 */


// Dev config
const DEV_MODE = true;
const DEV_TOKEN = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjp7InN0YXR1cyI6dHJ1ZSwibWVzc2FnZSI6ImNvbm5lY3Rpb24gT0siLCJJRCI6IjQiLCJSQSI6dHJ1ZSwiZGVzaWduYXRpb24iOiJyYyIsImxvZ2luIjoiYW5kcm9pZC5yYyIsImVudGl0ZSI6IkFHUiIsIlBDTF9NQVRSSUNVTEUiOiJBUkMiLCJERVBPVCI6IkdSIiwiU2VsZWN0X2FydGljbGUiOiIwIiwiREIiOiJBTkRST0lEIiwidmVyc2lvbiI6InYyIiwiaW1nX2ZvbGRlciI6Ikc6XFxBVk1fTE9HSUNJRUxTXFxBTkRST0lEXFxQREYiLCJ0eXBlX2NpYmxlIjoiMiIsImRlcG90cyI6IiggJ0dSJykiLCJjaGFudGllcnMiOiIoICdHUkMwMScsICdHUkMwMicpIn19.1kANV9ex9fdYzwBpY4dUxEWTDby79LiOqOWVyE-BlwY";


// Misc. Constants
const LOG_FILE_PATH = 'common/log.json';

// DB
const DB_Config = [
    'HOST'     => "itcansoprano.mysql.db",
    'USERNAME' => "itcansoprano",
    'PASSWORD' => "Soprano123",
    'DATABASE' => "itcansoprano"
];

// JWT
const JWT = [
    'key' => "izuefhbojvnpoayvbxlùwcvjbnqvi",
    'leeway' => 60,
    'iat' => 1356999524,
    'nbf' => 1357000000,
];



// PHP Error reporting
 error_reporting(E_ALL);

