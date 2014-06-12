<?php
/**
 * Emailconfig
 * 
 * @package		Email_Config
 * @subpackage	Autohemo.email
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
		'nome' 		=> 'Administrador Autohemo',
		'smtp'		=> 'smtp.gmail.com',
		'usuario'	=> '',
		'senha'		=> '',
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
