<?php

unset($_REQUEST['HTTP_SESSION_VARS']);
unset($_REQUEST['_SESSION']);
unset($_REQUEST['_SERVER']);

session_start();

$defLogin = '';     //learner's login
$defPwd = '';       //learner's password

$dbhost = "localhost";      //Host of server DB
$dbusername = "";           //Login for connection to DB server
$dbpassword = "";           //Password for connection to DB server
$dbname = "_autoplay_vocabulary";   //DB name
if (($_SERVER['REMOTE_ADDR'] == "127.0.0.1") || ($_SERVER['REMOTE_ADDR'] == "::1")) {//use this block for your local web server
    $dbhost = "localhost";              //DB host for local servers
    $dbusername = "";                   //DB login for local servers
    $dbpassword = "";                   //DB password for local servers
    $dbname = "_autoplay_vocabulary";   //DB name for local servers
}

$googleApiKey = '';//Text-to-speach google api key

$regularWordsTable = "regular_words";   //the table for regular words
$advancedWordsTable = "advanced_words"; //the table for advanced words

if ($_COOKIE['autoplay_vocabulary'] == 1) {
    $_SESSION['site']['login'] = $defLogin;
    $_SESSION['site']['pwd'] = $defPwd;
}

$dbHandler = @mysqli_connect($dbhost, $dbusername, $dbpassword);
if ($dbHandler !== false) {
    if (mysqli_select_db($dbHandler, $dbname)) mysqli_set_charset($dbHandler, "utf8mb4"); else die(json_encode(['error' => ["message" => "Can't choose the DB"]]));
} else die(json_encode(['error' => ["message" => "Can't connect to the MySQL DB server"]]));

$inputData = json_decode(file_get_contents('php://input'), 1);
$workTable = ($inputData['appMode']) ? $advancedWordsTable : $regularWordsTable;

