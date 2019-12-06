<?php
//自动加载类  php >5.1
spl_autoload_register(function($class) {
    //echo '---------------'.$class.'---------------';
    include  str_replace('\\','/',$class) . '.php';
});

//include 'app/Entry.php';
(new \app\Entry())->handler();