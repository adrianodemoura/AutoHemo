<?php
/**
 * Class Local
 * 
 * @package		Local
 * @package		Autohemo.Model
 */
require_once(APP.'Model/Model.php');
class Local extends Model {
	/**
	 * Nome da tabela de cidades
	 * 
	 * @var		string	
	 * @access	public
	 */
	public $tabela		= 'locais';

	/**
	 * Chave primária do model usuários
	 * 
	 * @var		array
	 * @access	public
	 */
	public $primaryKey 	= array('id');

	/**
	 * Propriedade de cada campo da tabela salas
	 * 
	 * @var		array
	 * @acess	public
	 */
	public $esquema = array
	(
		'id'				=> array
		(
			'tit'			=> 'Id',
		),
		'nome'				=> array
		(
			'tit'			=> 'Nome',
			'notEmpty'		=> true,
			'pesquisar'		=> '&',
			'upperOff'		=> true,
		),
		/*'opcoesRetirada' 	=> array
		(
			'type' 			=> 'virtual',
			'options' 		=> array('5.00'=>'5ml', '10.00'=>'10ml', '20.00'=>'20ml'),
		),
		'opcoesAplicacao' 	=> array
		(
			'type' 			=> 'virtual',
			'options' 		=> array('2.50'=>'2.5ml', '5.00'=>'5ml', '10.00'=>'10ml', '20.00'=>'20ml'),
		)*/
	);
}
