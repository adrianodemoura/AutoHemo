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
		'cidade_id'			=> array
		(
			'tit'			=> 'Cidade',
			'belongsTo' 	=> array
			(
				'Cidade'	=> array
				(
					'key'	=> 'id',
					'fields'=> array('id','nome','uf'),
					'order'	=> array('nome'),
					'where' => array('Cidade.uf'=>'{uf}')
				),
			),
		),
		'aniversario'		=> array
		(
			'tit'			=> 'Aniversário',
			'mascara'		=> '99/99',
		),
		'ativo' 			=> array
		(	'tit' 			=> 'Ativo',
			'options'		=> array('0'=>'Não', '1'=>'Sim'),
		),
		'aplicador' 		=> array
		(	'tit' 			=> 'Aplicador',
			'options'		=> array('0'=>'Não', '1'=>'Sim'),
		),
		'email' 			=> array
		(
			'tit' 			=> 'e-mail',
			'upperOff' 		=> true,
		),
		'senha' 			=> array
		(
			'tit' 			=> 'Senha',
			'upperOff'		=> true,
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
		$opcs 	= array();
		$opcs['where']['Usuario.email'] = $e;
		$opcs['where']['Usuario.senha'] = encripta($s);
		$data = $this->find('all',$opcs);
		return $data;
	}

	/**
	 * Executa código antes do método save
	 *
	 * return boolean 	Veradeiro se pode continuar
	 */
	public function beforeSave()
	{
		foreach($this->data as $_l => $_arrMods)
		{
			if (isset($_arrMods['Usuario']['senha']) && !empty($_arrMods['Usuario']['senha']))
			{
				$this->data[$_l]['Usuario']['senha'] = encripta($_arrMods['Usuario']['senha']);
			}
		}
		return parent::beforeSave();
	}
}