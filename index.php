<?php 
        // constantes locais
    define('APP','./app/');
    define('WWW','');

    include_once(APP.'bootstrap.php');
    include_once(APP.'View/Layouts/'.$Site->layout.'.phtml');
?>
<!-- tempo de execução <?= round(microtime(true)-$_SERVER['REQUEST_TIME'],4) ?> segundos -->