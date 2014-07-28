<?php
/**
 * Class Retirada
 * 
 * @package		Retirada
 * @package		Autohemo.model
 */
require_once(APP.'Model/Model.php');
class Retirada extends Model {
	/**
	 * Nome da tabela de cidades
	 * 
	 * @var		string	
	 * @access	public
	 */
	public $tabela		= 'retiradas';

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
		'data'		=> array
		(
			'tit'			=> 'dtRetirada',
			'notEmpty'		=> true,
			'mascEdit'		=> array('d','m','y','h','i'),
			'multMinu'		=> 5,
		),
		'reti_qtd'			=> array
		(
			'tit'			=> 'qtd. Retirada',
			'options'		=> array('2.50'=>'2,5ml', '5.00'=>'5ml', '10.00'=>'10ml', '15.00'=>'15ml', '20.00'=>'20ml')
		),
		'usuario_id'		=> array
		(
			'tit'			=> 'Paciente',
			'filtro'		=> true,
			'emptyFiltro'	=> '-- Todos os Pacientes --',
			'belongsTo' 	=> array
			(
				'Usuario'	=> array
				(
					'key'	=> 'id',
					'fields'=> array('id','nome'),
					'order'	=> array('nome'),
					'ajax'	=> 'usuarios/get_options/',
					'txtPesquisa' => 'Digite o nome do paciente para pesquisar ...',
					'limit'	=> 1000
				),
			),
		),
		'local_id'			=> array
		(
			'tit'			=> 'Local',
			'filtro'		=> true,
			'emptyFiltro'	=> '-- Todos os Locais de Retirada --',
			'belongsTo' 	=> array
			(
				'Local'		=> array
				(
					'key'	=> 'id',
					'fields'=> array('id','nome'),
					'order'	=> array('nome'),
					'ajax'	=> 'autohemo/locais/get_options/',
					'txtPesquisa' => 'Digite o nome do local para pesquisar ...',
					'limit'	=> 100
				),
			),
		),
		'Aplicacao' 		=> array
		(
				'type' 		=> 'hasmany',
				'tit' 		=> 'Aplicações',
				'keyFk' 	=> 'retirada_id',
				'fields' 	=> array('id','data','local_id','apli_qtd'),
				'order'		=> array('data'),
		),
	);

	/**
	 * Executa método antes de salvar a retirada
	 *
	 * - As retiradas só seram salvas se possuir alguma quantidade.
	 * - Caso a retirada NÃO possua quantidade mas possui ID (já foi salva antes), as aplicações desta serão excluídas
	 * - A quantidade das aplicações NÃO podem extrapolar a quantidade da retiradas
	 *
	 * @return boolean		Verdadeiro para continuar, Falso pra cancelar
	 */
	public function beforeSave()
	{
		$data 	= array();
		$delIds = array();
		$totRet = 0;
		$totApl = 0;
		foreach($this->data as $_l => $_arrMods)
		{
			if (isset($_arrMods['Retirada']['reti_qtd']))
			{
				$data[$_l] = $_arrMods;
				$totRet += $_arrMods['Retirada']['reti_qtd'];
			} elseif(isset($_arrMods['Retirada']['id']) && $_arrMods['Retirada']['id']>0)
			{
				$delIds[$_l]['Retirada']['id'] = $_arrMods['Retirada']['id'];
			}
		}
		if (!empty($delIds)) $this->delIds = $delIds;
		if (empty($data) && empty($delIds))
		{
			$this->erros['0'] = 'Nenhuma retirada foi informada !!!';
			return false;
		}

		// verificando as aplicações
		if (isset($this->aplicacoes))
		{
			foreach($this->aplicacoes as $_l => $_arrMods)
			{
				$totApl += isset($_arrMods['Aplicacao']['apli_qtd'])
					? $_arrMods['Aplicacao']['apli_qtd']
					: 0;
			}
			if ($totApl>$totRet)
			{
				$this->erros['0'] = 'A quantidade das aplicações NÃO podem extrapolar a quantidade retirada !!!';
				return false;
			}
		}

		$this->data = $data;
		return parent::beforeSave();
	}

	/**
	 * Executa método depois de salvar a retirada
	 * - Se foi identificada que a retirada foi ZERADA, então se exclui as aplicações dela.
	 *
	 * @return void
	 */
	public function afterSave()
	{
		if (isset($this->delIds))
		{
			if (!$this->exclude($this->delIds))
			{
				debug('Erro ao tentar excluir retiradas ...');
				debug($this->erro);
				debug($this->delIds);
				die(' NOJENTO !!!');
			} else
			{
				$this->msg = 'Excluir '.count($this->delIds).' retiradas ...';
			}
		}
		parent::afterSave();
	}

	/**
	 * Executa código depois do método delete
	 * - Exclui todas as aplicações da retirada excluída
	 *
	 * @return 	void
	 */
	public function afterExclude()
	{
		foreach($this->data as $_l => $_arrMods)
		{
			$idRetirada = $_arrMods['Retirada']['id'];
			$sql = 'DELETE FROM aplicacoes WHERE retirada_id='.$idRetirada;
			$this->query($sql);
		}
	}
}
