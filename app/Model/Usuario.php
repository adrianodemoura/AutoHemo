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
	);

	/**
	 * Autentica o usuário no banco de dados
	 * 
	 * @param	string	$e	e-mail
	 * @param	string	$s	Senha
	 */
	public function autentica($e='', $s='')
	{
		$s = md5($s.SALT);
		$opcs = array();
		$opcs['where']['email'] = $e;
		$opcs['where']['senha'] = $s;
		$data = $this->find('all',$opcs);
		return $data;
	}
}