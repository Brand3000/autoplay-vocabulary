<?php

require_once("_init.php");

$res['words'] = array();
$resulti = mysqli_query($dbHandler, "select * from {$workTable} where study=1 and done=0 and word RLIKE '^[a-zA-Z]' order by random_order asc");
$enWords = mysqli_fetch_all($resulti, MYSQLI_ASSOC);
if ($enWords) $res['words'] = array_merge($res['words'], $enWords);
$resulti = mysqli_query($dbHandler, "select * from {$workTable} where study=1 and done=0 and word NOT RLIKE '^[a-zA-Z]' order by random_order asc");
$ruWords = mysqli_fetch_all($resulti, MYSQLI_ASSOC);
if ($ruWords) $res['words'] = array_merge($res['words'], $ruWords);

/*$res['ilist'] = array();
$result = mysqli_query($dbHandler, "select * from {$workTable} where study=0 and done=0 and ord>0 and advanced='".$advanced."' and word RLIKE '^[a-zA-Z]' order by ord desc");
while ($row = mysqli_fetch_assoc($result)) {
    $res['ilist'][] = $row;
}
$result = mysqli_query($dbHandler, "select * from {$workTable} where study=0 and done=0 and ord>0 and advanced='".$advanced."' and word NOT RLIKE '^[a-zA-Z]' order by ord desc");
while ($row = mysqli_fetch_assoc($result)) {
    $res['ilist'][] = $row;
}*/

echo(json_encode($res));
