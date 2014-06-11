<?php
/**
 * Class Usuario
 * 
 * @package			Usuario
 * @subpackage		Site.Model
 */
require_once(APP.'Model/Model.php');
class Usuario extends Model {
	/**
	 * Nome da tabela de usuários
	 * 
	 * @var		string	
	 * @access	public
	 */
	public $tabela		= 'usuarios';

	/**
	 * Chave primária do model usuários
	 * 
	 * @var		array
	 * @access	public
	 */
	public $primaryKey 	= array('id');

	/**
	 * Campo principal
	 * 
	 * @var		array
	 * @access	public
	 */
	public $displayField= 'email';

	/**
	 * Propriedade de cada campo da tabela usuários
	 * 
	 * @var		array
	 * @acess	public
	 */
	public $esquema 		= array
	(
		'id' 				=> array
		(
			'tit' 			=> 'Id',
		),
		'perfil_id'			=> array
		(
			'tit'			=> 'Perfil',
			'belongsTo' 	=> array
			(
				'Perfil'	=> array
				(
					'key'	=> 'id',
					'fields'=> array('id','nome'),
					'order'	=> array('nome')
				),
			),
		),
		'email' 			=> array
		(
			'tit' 			=> 'e-mail',
			'upperOff' 		=> true,
		),
	);

	/**
	 * Autentica o usuário no banco de dados
	 * 
	 * @param	string	$e	e-mail
	 * @param	string	$s	Senha
	 */
	public function autentica($e='', $s='')
	{
		$s 		= encripta($s);
		$opcs 	= array();
		$opcs['where']['Usuario.email'] = $e;
		$opcs['where']['Usuario.senha'] = $s;
		$data = $this->find('all',$opcs);
		debug($opcs);
		return $data;
	}
}