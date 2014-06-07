<?php
/**
 * Class Site
 * @package 	Site
 * @subpackage 	Site.site
 */
include(APP.'Util.php');
class Site {
	/**
	 * Url do site
	 *
	 * @var 	string
	 * @access 	public
	 */
	public $base 		= '';

	/**
	 * Charset do site
	 *
	 * @var 	string
	 * @access 	public
	 */
	public $charset		= 'utf-8';

	/**
	 * Título da página
	 *
	 * @var 	string
	 * @access 	public
	 */
	public $titulo		= 'Auto Hemoterapia';	

	/**
	 * Título do sistema
	 *
	 * @var 	string
	 * @access 	public
	 */
	public $sistema 	= 'Auto Hemoterapia';

	/**
	 * Página principal
	 *
	 */
	public $pagina 		= 'principal';

	/**
	 * Esquema
	 *
	 */
	public $esquema		= array();

	/**
	 * Sqls
	 *
	 */
	public $sqls 		= array();

	/**
	 * Método start da classe
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->base 	= $this->getBase();
		$this->pagina 	= $this->getPagina();
	}

	/**
	 * Metodo end da classe
	 */
	public function __destruct()
	{
	}

	/**
	 * Retorna a url do sistema
	 * 
	 * @return	string	$base 	Url do Sistema
	 */
	public function getBase()
	{
		$base = '';
		$base = $_SERVER['REQUEST_SCHEME'].'://';
		$base .= $_SERVER['HTTP_HOST'];
		$base .= str_replace('index.php', '', $_SERVER['PHP_SELF']);
		return $base;
	}

	/**
	 * Retorna o nome da página corrente
	 */
	public function getPagina()
	{
		$pagina 		= $_SERVER['REQUEST_URI'];
		$aqui 			= explode('/', $_SERVER['PHP_SELF']);
		foreach($aqui as $_l => $_tag) $pagina = str_replace($_tag, '', $pagina);

		$pagina 		= str_replace('//', '', $pagina);
		$pagina 		= empty($pagina) ? 'principal' : $pagina;
		if (strpos($pagina, '/'))
		{
			$params = substr($pagina, strpos($pagina, '/'), strlen($pagina));
			if (!empty($params))
			{
				$arr = explode('/', $params);
				foreach($arr as $_l => $_tag)
				{
					$arrTag = explode(":", $_tag);
					if (isset($arrTag['1'])) $this->params[$arrTag['0']] = $arrTag['1'];
				}
			}
			$pagina = substr($pagina, 0, strpos($pagina, '/'));
		}
		$this->pagina = $pagina;
		return $pagina;
	}

	/**
	 * Executa a autenticação no banco de dados
	 *
	 * @param 	string 	$email 	e-mail a ser testado
	 * @param 	string 	$senha 	Senha a ser testada
	 * @
	 */
	public function autentica($email='', $senha='')
	{
		if (isset($_SESSION['Usuario']))
		{
			$this->msg = 'O Usuário já foi autenticado';
			redirect($this->base.'principal');
		}
		include_once(APP.'Model/Usuario.php');
		$this->Model 	= new Usuario();
		$data 			= $this->Model->autentica($email, $senha);
		if (!count($data))
		{
			$this->msgErro = 'Usuário inválido !!!';
			if (!empty($this->Model->erros))
			{
				foreach($this->Model->erros as $_l => $_arrProp)
				{
					switch ($_arrProp['codigo'])
					{
						case '1049':
						case '1045':
							redirect($this->base.'instalacao');
							break;
						case '1146':
							redirect($this->base.'instala_tabelas');
							break;
					}
				}
			}
			return false;
		}

		$_SESSION['Usuario']['id'] 		= $data['0']['Usuario']['id'];
		$_SESSION['Usuario']['nome'] 	= $data['0']['Usuario']['nome'];
		$_SESSION['Usuario']['email'] 	= $data['0']['Usuario']['email'];
		$_SESSION['Usuario']['perfilId']= $data['0']['Perfil']['id'];
		$_SESSION['Usuario']['perfil'] 	= $data['0']['Perfil']['nome'];
		redirect($this->base.'controle');
		return true;
	}

	/**
	 * Executa logo
	 */
	public function sair()
	{
		session_destroy();
		redirect($this->base.'login');
	}

	/**
	 * Salva uma mensagem flash
	 *
	 * @return void
	 */
	public function setMsgFlash($texto='', $class='msgOk')
	{
		$_SESSION['msgFlash']['texto'] = $texto;
		$_SESSION['msgFlash']['class'] = $class;
	}

	/**
	 * Retorna uma página com todas as aplicações do usuário logado
	 *
	 * @param 	integer 	$pag 	Página
	 * @param 	integer 	Id do usuário logado
	 */
	public function getMinhasAplicacoes()
	{
		// recuperando a data
		$dataRetirada = isset($this->params['data']) ? $this->params['data'] : date('d/m/Y');

		include_once(APP.'Model/Retirada.php');
		$Retirada = new Retirada();

		// recuperando as retiradas do usuário corrente na data selecionada
		$params = array();
		$params['pag'] = isset($this->params['pag']) ? $this->params['pag'] : 1;
		$params['where']['Retirada.usuario_id'] = $_SESSION['Usuario']['id'];
		$params['where']['Retirada.data LIKE'] 	= $dataRetirada;
		$data = $Retirada->find('all', $params);
		
		// recuperando as 30 últimas retiradas do usuário corrente
		$params = array();
		$params['fields'] 	= array('data');
		$params['where']['Retirada.usuario_id'] = $_SESSION['Usuario']['id'];
		$params['order'] 	= 'data';
		$params['direc'] 	= 'desc';
		$ultimas 	= array();
		$_ultimas 	= $Retirada->find('all', $params);
		foreach($_ultimas as $_l => $_arrMods)
		{
			$dataAnt = substr($_arrMods['Retirada']['data'], 0, 10);
			if (!in_array($dataAnt, $ultimas)) $ultimas[] = trim($dataAnt);
		}
		$this->ultimas = $ultimas;

		$this->sqls['Retirada'] 	= $Retirada->sqls;
		$this->esquema['Retirada'] 	= $Retirada->esquema;

		// recuperando as aplicações
		include_once(APP.'Model/Aplicacao.php');
		$Aplicacao = new Aplicacao();
		$this->esquema['Aplicacao'] = $Aplicacao->esquema;

		return $data;
	}

	/**
	 * Retorna os Locais de retirada e aplicação
	 * 
	 * @return void
	 */
	public function getLocais()
	{
		include_once(APP.'Model/Local.php');
		$Local = new Local();

		$params = array();
		$params['pag'] = isset($this->params['pag']) ? $this->params['pag'] : 1;
		$params['order'] = array('nome');
		//$params['where']['Local.retirada'] = 1;

		$data 	= $Local->find('all', $params);
		$this->sqls['Local'] 	= $Local->sqls;
		$this->esquema['Local'] = $Local->esquema;
		return $data;
	}

	/**
	 * Configura o debug
	 *
	 */
	public function debug()
	{
		if (isset($_SESSION['debug']))
		{
			unset($_SESSION['debug']);
		} else
		{
			$_SESSION['debug'] = true;
		}
	}	

	/**
	 * Salva as aplicações
	 *
	 * @param 	array 	$_POST['data'] 	Array com os dados a serem salvos
	 */
	public function salvar_retirada()
	{
		if (isset($_POST['data']))
		{
			include_once(APP.'Model/Retirada.php');
			$Retirada = new Retirada();
			$dataC = $_POST['data']['0']['Retirada']['data'];
			if ($Retirada->save($_POST['data']))
			{
				$msg 	= 'A Retirada foi salva com sucesso ...';
				$class 	= 'msgOk';

				$aplicacoes = isset($_POST['aplicacao']) ? $_POST['aplicacao'] : array();
				if (!empty($aplicacoes))
				{
					$idRetirada = $Retirada->data['0']['Retirada']['id'];
					foreach($aplicacoes as $_l => $_arrCmps)
					{
						$aplicacoes[$_l]['Aplicacao']['retirada_id'] = $idRetirada;
					}

					include_once(APP.'Model/Aplicacao.php');
					$Aplicacao = new Aplicacao();
					if ($Aplicacao->save($aplicacoes))
					{
						$msg = 'As aplicações foram salvas com sucesso ...';
					} else
					{
						$msg = 'Não foi possível salvar as aplicações !!!';
					}
					$this->sqls['Aplicacao'] = $Aplicacao->sqls;
					//debug($Aplicacao->data);
				}
			} else
			{
				$msg 	= 'Erro ao salvar retirada !!!';
				$class 	= 'msgErro';
				if (isset($Retirada->erros['0']))
				{
					$msg = $Retirada->erros['0'];
				}
			}
			$this->sqls['Retirada'] = $Retirada->sqls;

			$this->setMsgFlash($msg,$class);
			redirect($this->base.'controle/data:'.str_replace('/','-',substr($dataC,0,10)));
		}
	}

	/**
	 * Exclui uma retirada pela data
	 *
	 * @param 	string 	$data 	Data a ser excluída (vai pegar no parâmetro)
	 * @param 	integer $idUsuario 	Id do Usuário (vai pegar da sessão)
	 * @return 	void
	 */
	public function excluir_retirada()
	{
		$ids 	= explode(',',$this->params['ids']);
		$dataC 	= $this->params['data'];
		$data 	= array();
		foreach($ids as $_l => $_id)
		{
			$data[$_l]['Retirada']['id'] = $_id;
		}

		include_once(APP.'Model/Retirada.php');
		$this->Model = new Retirada();

		if (!$this->Model->exclude($data))
		{
			die('erro ao tentar excluir retirada');
		} else
		{
			$this->sqls['Retirada'] = $this->Model->sqls;
		}

		$this->setMsgFlash('Retirada limpada com sucesso !!!','msgOk');
		redirect($this->base.'controle/data:'.$dataC);
	}
}
