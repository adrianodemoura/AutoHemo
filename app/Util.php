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