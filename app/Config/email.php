<?php
/**
 * Database Config
 * 
 * @package		Database_Config
 * @subpackage	Site.database
 */
class Email_Config {
	/**
	 * Banco de Desenvolvimento
	 * 
	 * @var		array
	 * @acccess	public
	 */
	public $default = array
	(
		'smtp'		=> 'smtp.gmail.com',
		'usuario'	=> 'adrianodemoura@gmail.com',
		'senha'		=> 'Ta31260301',
		'porta'		=> 465
	);

	/**
	 * Executa código no start da classe DatabaseConfig
	 *
	 * define o banco do SGE conforme a máquina local
	 *
	 * @return void
	 */
	public function __construct($email='default')
	{
		$this->default = $this->$email;
	}
}
