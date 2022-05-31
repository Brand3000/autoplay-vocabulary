<?php

require_once("_init.php");

$res = [];
$res['new'] = 0;
$res['newWordWithDuplicate'] = [];
$pr_magic = ini_get('magic_quotes_gpc');

foreach ($inputData as $key => $value) {
    $inputData[$key] = trim($value);
    if (!$pr_magic) $inputData[$key] = addslashes($inputData[$key]);
}

if ($inputData['idToConnectWith']) {//если получили подтверждение объединения
    $resulti = mysqli_query($dbHandler, "select * from {$workTable} where id='".$inputData['idToConnectWith']."'");
    $rowi = mysqli_fetch_assoc($resulti);
    $isEnglish = ord($rowi['word']);
    $isEnglish = (($isEnglish >= 65 && $isEnglish <= 90) || ($isEnglish >= 97 && $isEnglish <= 122)) ? true : false;
    $j = 0;
    for ($i = 1; $i <= 5; $i++) {
        if ($rowi['v'.$i]) $j++;
    }
    $query = "update {$workTable} set id='".$rowi['id']."'";
    for ($i = 1; $i <= 5; $i++) {
        $j++;
        if ($inputData['translate'.$i]) $query .= ", v".$j."='".mb_strtolower($inputData['translate'.$i])."'";
        if ($inputData['description'.$i]) $query .= ", v".$j."d='".$inputData['description'.$i]."'";
        if (!$isEnglish) {
            if ($inputData['wordtype'.$i]) $query .= ", wordtype".$j."='".$inputData['wordtype'.$i]."'";
        }
    }
    $query .= " where id='".$inputData['idToConnectWith']."'";
    $res['query'] = $query;
    mysqli_query($dbHandler, $query);
    if ($inputData['wordId']) {
        mysqli_query($dbHandler, "delete from {$workTable} where id='".$inputData['wordId']."'");
    }
    $res['status'] = "success";
} else {
    if ($inputData['word'] && $inputData['translate1']) {
        $existingWord = [];
        if (!$inputData['wordId']) {
            $resulti = mysqli_query($dbHandler, "select * from {$workTable} where word='".$inputData['word']."'");
            if ($resulti) {
                $rowi = mysqli_fetch_assoc($resulti);
                if ($rowi['id']) $existingWord = $rowi;
            }
        } else {
            $resulti = mysqli_query($dbHandler, "select * from {$workTable} where word='".$inputData['word']."' and id!='".$inputData['wordId']."'");
            if ($resulti) {
                $rowi = mysqli_fetch_assoc($resulti);
                if ($rowi['id']) $existingWord = $rowi;
            }
        }
        
        if ($existingWord['id']) {
            $res['existingWord'] = $existingWord;
            $res['existingWord']['forword'] = $inputData['word'];
        }
        
        if (!$existingWord['id']) {
            $isEnglish = ord($inputData['word']);
            $isEnglish = (($isEnglish >= 65 && $isEnglish <= 90) || ($isEnglish >= 97 && $isEnglish <= 122)) ? true : false;
            if ($inputData['wordId'] > 0) $query = "update {$workTable} set word='".mb_strtolower($inputData['word'])."'"; else {
                $query = "insert into {$workTable} set word='".mb_strtolower($inputData['word'])."'";
            }
            if ($inputData['wordtype']) $query .= ", wordtype='".$inputData['wordtype']."'";
            for ($i = 1; $i <= 5; $i++) {
                if ($inputData['translate'.$i]) $query .= ", v".$i."='".mb_strtolower($inputData['translate'.$i])."'";
                if ($inputData['description'.$i]) $query .= ", v".$i."d='".$inputData['description'.$i]."'";
                if (!$isEnglish) {
                    if ($inputData['wordtype'.$i]) $query .= ", wordtype".$i."='".$inputData['wordtype'.$i]."'";
                }
            }
            if ($inputData['wordId']) {
                $query .= " where id='".$inputData['wordId']."' ";
            } else {
                $resulti = mysqli_query($dbHandler, "select max(random_order) as random_order from {$workTable}");
                $rowi = mysqli_fetch_assoc($resulti);
                $maxRandomOrder = $rowi['random_order'] + 1;
                $query .= ", random_order='".$maxRandomOrder."'";
                $res['cntNewWords'] = 1;
            }
            mysqli_query($dbHandler, $query);
            
            if (!$inputData['wordId']) {//if a new word, add it vice versa
                for ($i = 1; $i <= 5; $i++) {
                    if ($inputData['translate'.$i]) {
                        $postFix = "";
                        $resulti = mysqli_query($dbHandler, "select count(id) as count from {$workTable} where word='".$inputData['translate'.$i]."'");
                        if ($resulti) {
                            $rowi = mysqli_fetch_assoc($resulti);
                            if ($rowi['count'] > 0) $postFix .= " ".($rowi['count'] + 1).'v';
                        }
                        
                        $query = "insert into {$workTable} set word='".mb_strtolower($inputData['translate'.$i].$postFix)."'";
                        $query .= ", v1='".mb_strtolower($inputData['word'])."'";
                        if ($inputData['description'.$i]) $query .= ", v1d='".$inputData['description'.$i]."'";
                        if ($isEnglish) {
                            if ($inputData['wordtype'.$i]) $query .= ", wordtype1='".$inputData['wordtype'.$i]."'";
                        }
                        $maxRandomOrder++;
                        $query .= ", random_order='".$maxRandomOrder."'";
                        mysqli_query($dbHandler, $query);
                        $res['cntNewWords']++;
                        if ($postFix) {
                            $resulti = mysqli_query($dbHandler, "select * from {$workTable} where word='".$inputData['translate'.$i]."'");
                            $rowi = mysqli_fetch_assoc($resulti);
                            $res['newWordWithDuplicate'] = $rowi;
                        }
                    }
                }
            }
            
            $res['status'] = "success";
            if (!$inputData['wordId']) {
                $res['new'] = 1;
            }
        }
    }
}

echo(json_encode($res));
