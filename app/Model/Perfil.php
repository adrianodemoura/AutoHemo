<?php
/**
 * Class Perfil
 * 
 * @package		Perfil
 * @package		Autohemo.Model
 */
/**
 * Include files
 */
require_once(APP.'Model/Model.php');
class Perfil extends Model {
	/**
	 * Nome da tabela de cidades
	 * 
	 * @var		string	
	 * @access	public
	 */
	public $tabela		= 'perfis';

	/**
	 * Chave primária do model usuários
	 * 
	 * @var		array
	 * @access	public
	 */
	public $primaryKey 	= array('id');

	/**
	 * Propriedade de cada campo da tabela usuários
	 * 
	 * @var		array
	 * @acess	public
	 */
	public $esquema 	= array
	(
		'id'			=> array
		(
			'tit'		=> 'Id',
		),
		'nome'			=> array
		(
			'tit'		=> 'Nome',
			'notEmpty'	=> true,
			'pesquisar'	=> '&',
		)
	);

	/**
	 * Executa código antes de excluir um perfil no banco
	 *
	 * - Perfil ADMINISTRADOR não pode ser excluído
	 * 
	 * @return boolean
	 */
	public function beforeExclude()
	{
		if (isset($this->data['0'][$this->name]['id']) && $this->data['0'][$this->name]['id']==1)
		{
			$this->erro = 'O Perfil ADMINISTRADOR não pode ser excluído !!!';
			return false;
		}
		return true;
	}

	/**
	 * Executa código depois do método save do cadastro de usuários
	 *
	 * - Limpa o cacheInfo de cada usuário
	 *
	 * @return 	void
	 */
	public function afterSave()
	{
		foreach($this->data as $_l => $_arrMods)
		{
			$id = isset($_arrMods['Perfil']['id'])
				? $_arrMods['Perfil']['id']
				: 0;
			if ($id>0)
			{
				appUses('cache','Memcache');
				$Cache 	= new Memcache();
				$chave 	= 'modulos'.$id;
				$res 	= $Cache->delete($chave);
			}
		}
	}
}
