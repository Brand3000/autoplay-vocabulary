<?php

require_once("_init.php");

if ($inputData['wordId']) {
    mysqli_query($dbHandler, "update {$workTable} set done=1 where id='".$inputData['wordId']."'");
}
