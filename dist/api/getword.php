<?php

require_once("_init.php");

$study = $inputData['study'] ?: 0;
$res = [];
$res['status'] = 'continue';
$res['mess'] = null;
//retrieve a next random word to repeat
$resulti = mysqli_query($dbHandler, "select count(id) as count from {$workTable} where study={$study}");
$rowi = mysqli_fetch_assoc($resulti);
if ($rowi['count'] > 0) {
    do {
        $resulti = mysqli_query($dbHandler, "select * from {$workTable} where study={$study} and done=0 order by random_order limit 1");
        if ($resulti) {
            $rowi = mysqli_fetch_assoc($resulti);
            if (!$rowi['id']) {//если все повторили, начинаем заново
                $resulti = mysqli_query($dbHandler, "select max(id) as maxid from {$workTable}");
                $rowi = mysqli_fetch_assoc($resulti);
                $numbers = range(1, $rowi['maxid']);
                shuffle($numbers);
                for ($i = 1; $i <= $rowi['maxid']; $i++) {
                    mysqli_query($dbHandler, "update {$workTable} set random_order='".$numbers[($i - 1)]."' where id='".$i."' and study={$study}");
                }
                mysqli_query($dbHandler, "update {$workTable} set done='0' where study={$study}");
                $res['mess'] = "Well done! You're the hero!";
            }
        } else {
            break;
        }
    } while (!$rowi['id']);
} else {
    $res['mess'] = "No words to repeat!";
    $res['status'] = 'stop';
}

echo(json_encode($rowi + $res));
