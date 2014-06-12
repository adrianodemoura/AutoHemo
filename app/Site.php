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
		if (!strpos('ttp:',$_SERVER['HTTP_HOST']))
		{
			$base = 'http://';
		} elseif(isset($_SERVER['REQUEST_SCHEME'])) $base .= $_SERVER['REQUEST_SCHEME'].'://';

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
	 * Liga ou desliga o modo debug
	 *
	 * @return void
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
			// salvando as regtiradas e as aplicações
			include_once(APP.'Model/Retirada.php');
			$Retirada 	= new Retirada();
			$dataC 		= $_POST['data']['0']['Retirada']['data'];

			if (isset($_POST['aplicacao'])) $Retirada->aplicacoes = $_POST['aplicacao'];

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
						$msg = 'A Retirada e as aplicações foram salvas com sucesso !!!';
					} else
					{
						$msg 	= 'A Retirada foi salva com sucesso, mas nenhuma aplicação foi criada ...';
					}
					$this->sqls['Aplicacao'] = $Aplicacao->sqls;
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
	 * Exibe a tela de controle
	 *
	 * @return 	void
	 */
	public function controle()
	{
		if (!isset($_SESSION['Usuario']))
		{
			$this->setMsgFlash('Acesso permitido somente para usuários autenticados !!!','msgErro');
			redirect($Site->base.'login');
		} 
		$data 	= $this->getMinhasAplicacoes();
		$locais = $this->getLocais();

		// locais de retirada
		$locaisRetiradas = array();
		foreach($locais as $_l => $_arrMods)
		{
			if ($_arrMods['Local']['retirada']) $locaisRetiradas[$_arrMods['Local']['id']] = $_arrMods['Local']['nome'];
		}

		$this->viewVars['data'] 	= $data;
		$this->viewVars['locais']	= $locais;
		$this->viewVars['locaisRetiradas'] = $locaisRetiradas;
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
		$Retirada = new Retirada();

		if (!$Retirada->exclude($data))
		{
			debug($Retirada->erro);
			die('erro ao tentar excluir retirada !!!');
		} else
		{
			$this->sqls['Retirada'] = $Retirada->sqls;
		}

		$this->setMsgFlash('Retirada limpada com sucesso !!!','msgOk');
		redirect($this->base.'controle/data:'.$dataC);
	}

	/**
	 * Envia uma e-mail pro usuário para refazer a senha
	 *
	 * @param 	string 	$email 	e-mail a ter a senha relembrada
	 * @return 	void
	 */
	public function muda_senha()
	{
		if (isset($_POST['data']['email']))
		{
			$email 		= $_POST['data']['email'];
			require_once('Model/Usuario.php');
			$Usuario 	= new Usuario();
			$params['where']['Usuario.email'] = $email;
			$data 		= $Usuario->find('first',$params);
			if (isset($data['0']['Usuario']))
			{
				unset($data['0']['Usuario']['troca_senha']);
				unset($data['0']['Usuario']['perfiL_id']);
				unset($data['0']['Usuario']['tele_resi']);
				unset($data['0']['Usuario']['celular']);
				unset($data['0']['Usuario']['aniversario']);
				unset($data['0']['Usuario']['cidade']);
				unset($data['0']['Usuario']['senha']);
				unset($data['0']['Usuario']['perfil_id']);
				unset($data['0']['Usuario']['troc_senh']);
				$data['0']['Usuario']['troc_senh_cod'] = encripta(date('d/m/Y H:i:s'));
				if (!$Usuario->save($data))
				{
					die('Erro ao tentar salvar código de validação de nova senha !!!');
				} else // enviando o e-mail
				{
					require_once(APP.'Config/email.php');
					$Email = new Email_Config();

					require_once(APP.'Vendor/PHPMailer/class.phpmailer.php');
					$Mail = new PHPMailer();
					$Mail->isSmtp();
					$Mail->isHtml();
					//$Mail->SMTPDebug 	= true;
					$Mail->CharSet 		= "UTF-8";
					$Mail->SMTPAuth 	= true;
					$Mail->SMTPSecure 	= 'ssl';
					$Mail->Host 		= $Email->default['smtp'];
					$Mail->Port 	 	= $Email->default['porta'];
					$Mail->Username 	= $Email->default['usuario'];
					$Mail->Password 	= $Email->default['senha'];
					$Mail->Subject 		= 'Mudança de senha';

					$Mail->AddAddress($email);

					$msg = "";
					$msg .= "Caro Usuario ".$data['0']['Usuario']['nome'].", ";
					$msg .= "<br /><br />";
					$msg .= strtolower("
					Clique aqui para alterar sua senha ".$this->base."trocar_senha/email:"
					.$data['0']['Usuario']['email']
					."/codigo:".$data['0']['Usuario']['troc_senh_cod']);
					$msg .= "<br /><br />";

					$Mail->Body = $msg;
					$enviado = $Mail->Send();
					if (!$enviado)
					{
						$this->viewVars['msgErro'] = 'Não foi possível enviar o e-mail de validação. '.$Mail->ErrorInfo;
					} else
					{
						$this->viewVars['msgOk'] = 'As instruções foram enviadas com sucesso para o e-mail '.$email;
					}
				}
			} else
			{
				$this->viewVars['msgErro'] = 'O e-mail '.$email.', não possui cadastro ainda !!!';
			}
		}
	}

	/**
	 * Exibe a tela pra o usuário trocar a senha
	 *
	 * @param 	string 	$codigo 	Código de ativação que foi enviado pelo e-mail do usuário
	 * @return 	void
	 */
	public function trocar_senha()
	{
		$codigo = isset($this->params['codigo']) ? $this->params['codigo'] : null;
		$codigo = isset($_POST['codigo']) 
			? $_POST['codigo'] 
			: $codigo;

		if (empty($codigo))
		{
			$this->setMsgFlash('Código de autenticação inválido !!!','msgErro');
			redirect($this->base.'login');
		}

		include_once('Model/Usuario.php');
		$Usuario = new Usuario();
		$params['where']['Usuario.troc_senh_cod'] 	= $codigo;
		$data = $Usuario->find('first',$params);
		if (!count($data))
		{
			$this->setMsgFlash('O Código de autenticação não foi validado pelo sistema !!!','msgErro');
			redirect($this->base.'login');
		} else
		{
			$this->viewVars['id'] 		= $data['0']['Usuario']['id'];
			$this->viewVars['nome'] 	= $data['0']['Usuario']['nome'];
			$this->viewVars['email'] 	= $data['0']['Usuario']['email'];
			$this->viewVars['codigo'] 	= $codigo;

			if (isset($_POST['data']) && !empty($_POST['data']['0']['Usuario']['senha']))
			{
				unset($data['0']['Usuario']['perfil_id']);
				unset($data['0']['Usuario']['email']);
				unset($data['0']['Usuario']['tele_resi']);
				unset($data['0']['Usuario']['celular']);
				unset($data['0']['Usuario']['aniversario']);
				unset($data['0']['Usuario']['cidade']);
				unset($data['0']['Usuario']['troc_senh']);
				unset($data['0']['Usuario']['troca_senha']);

				$data['0']['Usuario']['senha'] = encripta($_POST['data']['0']['Usuario']['senha']);
				$data['0']['Usuario']['troc_senh_cod'] = '';
				$data['0']['Usuario']['ativo'] = 1;
				if (!$Usuario->save($data))
				{
					die('Erro ao tentar trocar senha !!!');
				}
				$msg = 'A senha foi trocada com sucesso ...';
				$this->viewVars['msgOk'] 	= $msg;
			} elseif(isset($_POST['data']) && empty($_POST['0']['Usuario']['senha']))
			{
				$this->viewVars['msgErro'] = 'O Campo senha é de preenchimento obrigatório !!!';
			}
		}
	}

	/**
	 * Cria um novo registro do sistema
	 *
	 * @param string $nome Nome do usuário
	 * @param string $email e-mail do usuário
	 * @return void
	 */
	public function novo_registro()
	{
		$nome = isset($_POST['data']['0']['Usuario']['nome']) 
			? $_POST['data']['0']['Usuario']['nome']
			: null;
		$email = isset($_POST['data']['0']['Usuario']['email']) 
			? $_POST['data']['0']['Usuario']['email']
			: null;
		$this->viewVars['nome'] 	= $nome;
		$this->viewVars['email'] 	= $email;

		if (!empty($nome) && !empty($email))
		{
			// verifica se o e-mail já foi cadastrado
			require_once(APP.'Model/Usuario.php');
			$Usuario = new Usuario();
			$params['where']['Usuario.email'] = $email;
			$data = $Usuario->find('first',$params);
			$this->sqls['Usuario'] = $Usuario->sqls;
			if (count($data))
			{
				$this->viewVars['msgErro'] = 'Este e-mail já se encontra cadastrado !!!';
			} else // se não, cria o registro mas com status inativo
			{
				$data = $_POST['data'];
				$data['0']['Usuario']['ativo'] 			= 0;
				$data['0']['Usuario']['perfil_id'] 		= 3;
				$data['0']['Usuario']['senha'] 			= encripta('123mudar');
				$data['0']['Usuario']['troc_senh_cod'] 	= encripta(date('d/m/Y H:i:s'));
				if (!$Usuario->save($data))
				{
					$this->viewVars['msgErro'] = 'Não foi possível salvar o novo registro ';
				} else
				{
					require_once(APP.'Config/email.php');
					$Email = new Email_Config();

					require_once(APP.'Vendor/PHPMailer/class.phpmailer.php');
					$Mail = new PHPMailer();
					$Mail->isSmtp();
					$Mail->isHtml();
					$Mail->CharSet 		= "UTF-8";
					$Mail->SMTPAuth 	= true;
					$Mail->SMTPSecure 	= 'ssl';

					// remetente
					$Mail->From 		= "nao_responda@site.com.br";
					$Mail->Sender 		= $Email->default['usuario'];
					$Mail->FromName 	= $Email->default['nome'];

					// conta de e-mail 
					$Mail->Host 		= $Email->default['smtp'];
					$Mail->Port 	 	= $Email->default['porta'];
					$Mail->Username 	= $Email->default['usuario'];
					$Mail->Password 	= $Email->default['senha'];
					$Mail->Subject 		= "Novo Cadastro";

					$Mail->AddAddress($email);

					$msg = "";
					$msg .= "Caro ".$data['0']['Usuario']['nome'].", ";
					$msg .= "<br /><br />";
					$msg .= "No dia ".date('d/m/Y')." aproximadamente as ".date('H')." horas e ".date('i')." minutos, 
					foi feita uma solicitação de conta no nosso site, caso não reconheça este pedido, 
					favor desconsiderar este e-mail, o pedido será cancelado em até uma semana.<br /><br />
					Do contrário, clique no link abaixo para ativar sua conta.<br /><br />";
					$msg .= strtolower($this->base."trocar_senha/email:"
					.$data['0']['Usuario']['email']
					."/codigo:".$data['0']['Usuario']['troc_senh_cod']);
					$msg .= "<br /><br />

					att.<br />
					Administrador AutoHemo";

					$Mail->Body = $msg;
					$enviado = $Mail->Send();
					if (!$enviado)
					{
						$this->viewVars['msgErro'] = 'Não foi possível enviar o e-mail de validação. '.$Mail->ErrorInfo;
					} else
					{
						$this->viewVars['msgOk'] = 'As instruções para ativação da conta, foi enviada com sucesso para seu o '.$email;
					}
				}
			}

		}
	}
}
