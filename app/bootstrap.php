<?php
    // exibindo todos os erros
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // sessão
    session_name(md5('seg'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])); 
    session_start();

	// constantes locais
	define('APP','./app/');
	define('WWW','');

    // salt
    define('SALT','J1a537Me8LãO0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');

    // fuso-horário
    date_default_timezone_set('America/Sao_Paulo');

	include(APP.'Site.php');
	$Site = new Site();

    // jogamos o conteúdo do bloco numa variável, lá no layout ele será usado
    ob_start();
    if (method_exists($Site, $Site->pagina))
    {
        $action = $Site->pagina;
        $Site->$action();
        if (isset($Site->viewVars))
        {
            foreach($Site->viewVars as $_var => $_vlr) ${$_var} = $_vlr;
        }
    }
    include_once(APP.'View/'.$Site->pagina.'.phtml');
    $conteudo = ob_get_contents();
    ob_end_clean();

    // por segurança, o Model não vai pra view
    unset($Site->Model);
?>
