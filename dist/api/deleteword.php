<?php

require_once("_init.php");

if ($inputData['wordId']) {
    $resulti = mysqli_query($dbHandler, "delete from {$workTable} where id='".$inputData['wordId']."'");
}

echo(json_encode([]));
