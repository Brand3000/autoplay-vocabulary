<?php

require_once("_init.php");

$res = [];
//retrieve a next random word to repeat
if ($inputData['wordId']) {
    mysqli_query($dbHandler, "update {$workTable} set auto='0' where id='".$inputData['wordId']."'");
}
$forceBreak = 0;
do {
    $resulti = mysqli_query($dbHandler, "select * from {$workTable} where auto>0 order by auto limit 1");
    if ($resulti) {
        $rowi = mysqli_fetch_assoc($resulti);
        if (!$rowi['id']) {//если все повторили, начинаем заново
            $resulti = mysqli_query($dbHandler, "select max(id) as maxid from {$workTable}");
            $rowi = mysqli_fetch_assoc($resulti);
            if ($rowi['maxid']) {
                $numbers = range(1, $rowi['maxid']);
                shuffle($numbers);
                for ($i = 1; $i <= $rowi['maxid']; $i++) {
                    mysqli_query($dbHandler, "update {$workTable} set auto='".$numbers[($i - 1)]."' where id='".$i."'");
                }
            } else {
                $forceBreak = 1;
            }
            $res['mess'] = "Well done! You're the hero!";
        }
    } else {
        break;
    }
} while (!$rowi['id'] && !$forceBreak);

$res = $res + $rowi;

echo(json_encode($res));
