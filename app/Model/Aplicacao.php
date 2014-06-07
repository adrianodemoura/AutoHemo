<?php
/**
 * Class Aplicacao
 * 
 * @package		Aplicacao
 * @package		Autohemo.Model
 */
require_once(APP.'Model/Model.php');
class Aplicacao extends Model {
	/**
	 * Nome da tabela de cidades
	 * 
	 * @var		string	
	 * @access	public
	 */
	public $tabela		= 'aplicacoes';

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
		'apli_qtd'			=> array
		(
			'tit'			=> 'qtd.Aplicada',
			'options'		=> array('2.50'=>'2,5ml', '5.00'=>'5ml', '10.00'=>'10ml', '15.00'=>'15ml', '20.00'=>'20ml')
		),
		'data'				=> array
		(
			'tit' 			=> 'dt.Aplicação',
			'notEmpty'		=> true,
			'mascEdit'		=> array('d','m','y','h','i'),
			'multMinu'		=> 5,
		),
		'local_id'			=> array
		(
			'tit'			=> 'Local',
			'filtro'		=> true,
			'notEmpty'		=> true,
			'emptyFiltro'	=> '-- Todos os Locais de Aplicação --',
			'belongsTo' 	=> array
			(
				'Local'		=> array
				(
					'key'	=> 'id',
					'fields'=> array('id','nome'),
					'order'	=> array('nome')
				),
			),
		),
		'usuario_id'			=> array
		(
			'tit'			=> 'Paciente',
			'filtro'		=> true,
			'notEmpty'		=> true,
			'emptyFiltro'	=> '-- Todos os Pacientes --',
			'belongsTo' 	=> array
			(
				'Usuario'		=> array
				(
					'key'	=> 'id',
					'fields'=> array('id','nome'),
					'order'	=> array('nome')
				),
			),
		),
		'retirada_id'		=> array
		(
			'tit'			=> 'Retirada',
			'filtro'		=> true,
			'notEmpty'		=> true,
			'emptyFiltro'	=> '-- Todos os Pacientes --',
			'belongsTo' 	=> array
			(
				'Retirada'	=> array
				(
					'key'	=> 'id',
					'fields'=> array('id','nome'),
					'order'	=> array('nome')
				),
			),
		),
	);

	/**
	 * Executa método antes de salvar
	 *
	 * @return boolean		Verdadeiro para continuar, Falso pra cancelar
	 */
	public function beforeSave()
	{
		$data 	= array();
		$delIds = array();
		foreach($this->data as $_l => $_arrMods)
		{
			if (isset($_arrMods['Aplicacao']['apli_qtd']))
			{
				$data[$_l] = $_arrMods;
			} elseif(isset($_arrMods['Aplicacao']['id']) && $_arrMods['Aplicacao']['id']>0)
			{
				$delIds[$_l]['Aplicacao']['id'] = $_arrMods['Aplicacao']['id'];
			}
		}
		if (!empty($delIds)) $this->delIds = $delIds;
		if (empty($data))
		{
			$this->erros['0'] = 'Nenhuma quantidade foi informada na aplicação !!!';
			return false;
		}
		$this->data = $data;
		return parent::beforeSave();
	}

	/**
	 * Executa método depois de salvar a aplicação
	 *
	 * @return void
	 */
	public function afterSave()
	{
		if (isset($this->delIds))
		{
			if (!$this->exclude($this->delIds))
			{
				die('Erro ao tentar excluir aplicação ...');
			} else
			{
				$this->msg = 'Excluir '.count($this->delIds).' aplicações ...';
			}
		}
		parent::afterSave();
	}
}
