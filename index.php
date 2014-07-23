<?php 
        // constantes locais
    define('APP','./app/');
    define('WWW','');

    include_once(APP.'bootstrap.php');
    include_once(APP.'View/Layouts/'.$Site->layout.'.phtml');
?>

<?php if (!in_array($Site->layout,array('ajax','csv'))) : ?>
<!-- tempo de execução <?= round(microtime(true)-$_SERVER['REQUEST_TIME'],4) ?> segundos -->
<?php endif ?>
