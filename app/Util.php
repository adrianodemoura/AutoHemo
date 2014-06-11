<?php
/**
 * Debug
 */
function debug($d)
{
	$t = debug_backtrace();
	echo '<pre>';
	echo $t['0']['file'].' ('.$t['0']['line'].")\n";
	echo print_r($d,true);
	echo '</pre>';
}

/**
 * Redirecionamento
 */
function redirect($url='')
{
	header('Location: '.$url);
	die();
}

/**
 * retorna a senha super encriptada
 *
 * @param 	string 	$senha  	Senha a ser encriptada
 * @return 	string 	$codifica 	Senha super encriptada
 */
function encripta($senha='')
{
	$salt 		= md5($senha.SALT);
	$codifica 	= crypt($senha,$salt);
 	$codifica	= hash('sha512',$codifica);
 	return $codifica; 
}