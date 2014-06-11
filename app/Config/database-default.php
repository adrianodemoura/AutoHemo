<?php
/**
 * Database Config
 * 
 * @package		Database_Config
 * @subpackage	Autohemo.database
 */
class Database_Config {
	/**
	 * Banco de Desenvolvimento
	 * 
	 * @var		array
	 * @acccess	public
	 */
	public $default = array
	(
		'driver'	=> 'mysql',
		'persistent'=> false,
		'host' 		=> 'localhost',
		'user' 		=> 'hemo_us',
		'password' 	=> 'hemo_67',
		'database' 	=> 'hemo_bd',
		'charset' 	=> 'utf8'
	);

	/**
	 * Executa código no start da classe DatabaseConfig
	 *
	 * define o banco do SGE conforme a máquina local
	 *
	 * @return void
	 */
	public function __construct($banco='default')
	{
		$this->default = $this->$banco;
	}
}
