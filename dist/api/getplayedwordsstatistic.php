<?php

require_once("_init.php");

if ($_SESSION['site']['login'] === $defLogin) {
    $resulti = mysqli_query($dbHandler, "select count(id) as count from {$workTable}");
    $cntAllWords = mysqli_fetch_array($resulti)['count'];
    
    $resulti = mysqli_query($dbHandler, "select count(id) as count from {$workTable} where auto>0");
    $cntWordsToPlay = mysqli_fetch_array($resulti)['count'];
}

echo(json_encode(["cntWordsToPlay" => $cntWordsToPlay, "cntAllWords" => $cntAllWords]));
