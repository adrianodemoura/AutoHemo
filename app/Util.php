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

/**
 * Retorna uma data randômica
 * 
 * @return	string
 */
function randomDate($start_date, $end_date)
{
    // Convert to timetamps
    $min = strtotime($start_date);
    $max = strtotime($end_date);

    // Generate random number using above bounds
    $val = rand($min, $max);

    // Convert back to desired date format
    return date('Y-m-d H:i:s', $val);
}
