<?php 
    $config = include __DIR__ . '/config.php';
    $token = md5(time());
    touch(__DIR__ . "/tokens/$token");
    $url = $config['app_url'] . "?token=$token";
    echo $url;