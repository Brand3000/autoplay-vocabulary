<?php

require_once("_init.php");

$res = [];

if ($inputData['idToConnectWith']) {//если получили подтверждение объединения
    $resulti = mysqli_query($dbHandler, "select * from {$workTable} where id='".$inputData['wordId']."'");
    $rowAdvancedWord = mysqli_fetch_assoc($resulti);
    
    $resulti = mysqli_query($dbHandler, "select * from {$regularWordsTable} where id='".$inputData['idToConnectWith']."'");
    $rowi = mysqli_fetch_assoc($resulti);
    $isEnglish = ord($rowi['word']);
    $j = 0;
    for ($i = 1; $i <= 5; $i++) {
        if ($rowi['v'.$i]) $j++;
    }
    $query = "update {$regularWordsTable} set id='".$rowi['id']."'";
    for ($i = 1; $i <= 5; $i++) {
        $j++;
        if ($rowAdvancedWord['v'.$i]) $query .= ", v".$j."='".mb_strtolower($rowAdvancedWord['v'.$i])."'";
        if ($rowAdvancedWord['v'.$i]) $query .= ", v".$j."d='".$rowAdvancedWord['v'.$i.'d']."'";
        if (!$isEnglish) {
            if ($rowAdvancedWord['wordtype'.$i]) $query .= ", wordtype".$j."='".$rowAdvancedWord['wordtype'.$i]."'";
        }
    }
    $query .= " where id='".$inputData['idToConnectWith']."'";
    mysqli_query($dbHandler, $query);
    mysqli_query($dbHandler, "delete from {$workTable} where id='".$inputData['wordId']."'");
    $res['status'] = "success";
} else {
    if ($inputData['wordId']) {
        $resulti = mysqli_query($dbHandler, "select * from {$workTable} where id='".$inputData['wordId']."'");
        $rowi = mysqli_fetch_assoc($resulti);
        if ($rowi['id']) {
            $existingWord = [];
            $resulti = mysqli_query($dbHandler, "select * from {$regularWordsTable} where word='".addslashes($rowi['word'])."'");
            if ($resulti) {
                $tmpi = mysqli_fetch_assoc($resulti);
                if ($tmpi['id']) $existingWord = $tmpi;
            }
            
            if ($existingWord['id']) {
                $res['existingWord'] = $existingWord;
                $res['existingWord']['forword'] = $inputData['word'];
            }
            
            if (!$existingWord['id']) {
                $query = "insert into {$regularWordsTable} set word='".addslashes($rowi['word'])."'";
                for ($i = 1; $i <= 5; $i++) {
                    if ($rowi['v'.$i]) $query .= ", v".$i."='".addslashes($rowi['v'.$i])."'";
                    if ($rowi['v'.$i.'d']) $query .= ", v".$i."d='".addslashes($rowi['v'.$i.'d'])."'";
                    if ($rowi['wordtype'.$i]) $query .= ", wordtype".$i."='".addslashes($rowi['wordtype'.$i])."'";
                }
                $query .= ", done=0, study=0";
                $resulti = mysqli_query($dbHandler, "select max(random_order) as random_order from {$regularWordsTable}");
                $rowi = mysqli_fetch_assoc($resulti);
                $maxRandomOrder = $rowi['random_order'] + 1;
                $query .= ", random_order='".$maxRandomOrder."'";
                $resulti = mysqli_query($dbHandler, "select max(auto) as maxauto from {$regularWordsTable}");
                $rowi = mysqli_fetch_assoc($resulti);
                $maxAuto = $rowi['maxauto'] + 1;
                $query .= ", auto='".$maxAuto."'";
                mysqli_query($dbHandler, $query);
                mysqli_query($dbHandler, "delete from {$workTable} where id='".$inputData['wordId']."'");
                $res['status'] = "success";
            }
        }
    }
}

echo(json_encode($res));
