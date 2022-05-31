<?php

require_once("_init.php");

if ($inputData['wordId']) {
    $resulti = mysqli_query($dbHandler, "select max(random_order) as random_order from {$workTable}");
    $rowi = mysqli_fetch_assoc($resulti);
    $maxRandomOrder = $rowi['random_order']+1;
    mysqli_query($dbHandler, "update {$workTable} set done=0, study=0, random_order={$maxRandomOrder} where id='".$inputData['wordId']."'");
}
