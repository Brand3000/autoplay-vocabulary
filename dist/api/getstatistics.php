<?php

require_once("_init.php");

if ($_SESSION['site']['login'] === $defLogin) {
//quantity of all the words
    $resulti = mysqli_query($dbHandler, "select count(id) as count from {$workTable}");
    if ($resulti) $cntAllWords = mysqli_fetch_array($resulti)['count']; else die(json_encode(['error' => ["message" => "Some problem with the table ".$workTable.""]]));

//quantity of en - words
    $resulti = mysqli_query($dbHandler, "select count(id) as count from {$workTable} where word RLIKE '^[a-zA-Z]'");
    if ($resulti) $cntEnWords = mysqli_fetch_array($resulti)['count']; else die(json_encode(['error' => ["message" => "Some problem with the table ".$workTable.""]]));

//quantity of repeated words
    $resulti = mysqli_query($dbHandler, "select count(id) as count from {$workTable} where study=0 and done=1");
    if ($resulti) $cntRepeatedWords = mysqli_fetch_array($resulti)['count']; else die(json_encode(['error' => ["message" => "Some problem with the table ".$workTable.""]]));

//quantity of words to study
    $resulti = mysqli_query($dbHandler, "select count(id) as count from {$workTable} where study=1");
    if ($resulti) $cntWordsToStudy = mysqli_fetch_array($resulti)['count']; else die(json_encode(['error' => ["message" => "Some problem with the table ".$workTable.""]]));
} else {
    die(json_encode([]));
}

echo(json_encode(["cntAllWords" => $cntAllWords, 'cntEnWords' => $cntEnWords, 'cntRepeatedWords' => $cntRepeatedWords, 'cntWordsToStudy' => $cntWordsToStudy, 'isUser' => ($_SESSION['site']['login'] === $defLogin)]));
