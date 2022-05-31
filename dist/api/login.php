<?php

require_once("_init.php");

if (!$res['isUser']) {
    $res['isUser'] = ((($inputData['login'] === $defLogin) && ($inputData['pwd'] === $defPwd)) ? true : false);
    
    if ($res['isUser']) {
        setcookie("autoplay_vocabulary", "1", time() + (60 * 60 * 24 * 10000), "/");
    }
}

echo(json_encode($res));
