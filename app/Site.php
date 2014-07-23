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
	 * Layout 
	 *
	 * @var 	string
	 * @access 	public
	 */
	public $layout		= 'padrao';

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
	 * Script jQuery incrementados no layout
	 *
	 */
	public $onRead 		= array();

	/**
	 * Arquivos JS incrementados no layout
	 *
	 */
	public $headJs 		= array();

	/**
	 * Arquivos CSS incrementados no layout
	 *
	 */
	public $headCss 		= array();

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
	 * Retorna o nome da página corrente e configura os parâmetros depois da página
	 *
	 * @param 	string 	Página a ser exibida
	 * @return 	string 	Nome da página a ser renderizada
	 */
	public function getPagina()
	{
		$arrSel = explode('/',$_SERVER['PHP_SELF']);
		$arrPag = explode('/',$_SERVER['REQUEST_URI']);
		unset($arrPag['0']);
		$pagina = (count($arrSel)==2) ? $arrPag['1'] : $arrPag['2'];
		$pagina = empty($pagina) ? 'principal' : $pagina;
		$this->pagina = $pagina;

		// se possui parâmetros
		if (strpos($_SERVER['REQUEST_URI'], ':'))
		{
			$uri 	= $_SERVER['REQUEST_URI'];
			$params = substr($uri, strpos($uri, '/'), strlen($uri));
			if (!empty($params))
			{
				$arr = explode('/', $params);
				foreach($arr as $_l => $_tag)
				{
					$arrTag = explode(":", $_tag);
					if (isset($arrTag['1'])) $this->params[$arrTag['0']] = $arrTag['1'];
				}
			}
		}

		return $pagina;
	}

	/**
	 * Incrementa arquivos Js e CSS no layout padrão
	 *
	 * @param 	string 	$tipo 	Tipo do arquivo JS ou CSS
	 * @param 	string 	$arq 	Nome do arquivo
	 * @return 	void
	 */
	public function setHead($tipo='', $arq='')
	{
		switch($tipo)
		{
			case 'js':
				array_push($this->headJs,$arq);
				break;
			case 'css':
				array_push($this->headCss,$arq);
				break;
		}
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
			$this->sqls['Usuario'] = $this->Model->sqls;
			return false;
		}

		$_SESSION['Usuario']['id'] 		= $data['0']['Usuario']['id'];
		$_SESSION['Usuario']['nome'] 	= $data['0']['Usuario']['nome'];
		$_SESSION['Usuario']['email'] 	= $data['0']['Usuario']['email'];
		$_SESSION['Usuario']['perfilId']= $data['0']['Perfil']['id'];
		$_SESSION['Usuario']['perfil'] 	= $data['0']['Perfil']['nome'];
		redirect($this->base.'lista');
		return true;
	}

	/**
	 * Executa logout
	 *
	 * @return void
	 */
	public function sair()
	{
		session_destroy();
		redirect($this->base.'login');
	}

	/**
	 * Salva a mensage Flash na sessão
	 *
	 * @param 	string 	$texto 	Texto a ser salvo
	 * @param 	string 	$class 	Classe a ser usada na exibição da mensagem
	 * @return 	void
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
	 * @param 	array 		$data 	Matriz contendo minhas aplicações
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
			redirect($this->base.'aplicacoes/data:'.str_replace('/','-',substr($dataC,0,10)));
		}
	}

	/**
	 * Exibe a tela de Aplicações
	 * - A tela só deve ser exibida para usuário logados
	 *
	 * @return 	void
	 */
	public function aplicacoes()
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
		redirect($this->base.'aplicacoes/data:'.$dataC);
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

					// configuração do objeto email
					$Mail->isSmtp();
					$Mail->isHtml();
					$Mail->SMTPDebug 	= 2;
					$Mail->CharSet 		= "UTF-8";
					$Mail->SMTPAuth 	= true;
					switch($Email->default['porta'])
					{
						case 587:
							$Mail->SMTPSecure = 'tsl';
							break;
						default:
							$Mail->SMTPSecure = 'ssl';
					}
					$Mail->Host 		= $Email->default['smtp'];
					$Mail->Port 	 	= $Email->default['porta'];
					$Mail->Username 	= $Email->default['usuario'];
					$Mail->Password 	= $Email->default['senha'];
					$Mail->Subject 		= 'Mudança de senha';

					// remetente
					$Mail->Sender 		= $Email->default['usuario'];
					$Mail->FromName 	= $Email->default['nome'];

					// destinatário
					$Mail->AddAddress($email);

					// mensagen
					$msg = "";
					$msg .= "Caro Usuario ".$data['0']['Usuario']['nome'].", ";
					$msg .= "<br /><br />";
					$msg .= strtolower("
					Clique aqui para alterar sua senha ".$this->base."trocar_senha/email:"
					.$data['0']['Usuario']['email']
					."/codigo:".$data['0']['Usuario']['troc_senh_cod']);
					$msg .= "<br /><br />";
					$Mail->Body = $msg;

					// enviando
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

				$data['0']['Usuario']['senha'] = $_POST['data']['0']['Usuario']['senha'];
				$data['0']['Usuario']['troc_senh_cod'] = '';
				$data['0']['Usuario']['ativo'] = 1;
				if (!$Usuario->save($data))
				{
					die('Erro ao tentar trocar senha !!!');
				}
				$msg = 'A senha foi trocada com sucesso ...';
				$this->viewVars['msgOk'] 	= $msg;
				$this->sqls['Usuario'] = $Usuario->sqls;
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
					$Mail->Sender 		= $Email->default['usuario'];
					$Mail->FromName 	= $Email->default['nome'];

					// conta de e-mail 
					$Mail->Host 		= $Email->default['smtp'];
					$Mail->Port 	 	= $Email->default['porta'];
					$Mail->Username 	= $Email->default['usuario'];
					$Mail->Password 	= $Email->default['senha'];
					$Mail->Subject 		= "Novo Cadastro";

					// destinatário
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
						$this->viewVars['msgOk'] = 'As instruções para ativação da conta, foi enviada com sucesso para o e-mail '.$email;
					}
				}
			}

		}
	}

	/**
	 * Executa a instalação do sistema básico
	 *
	 * @return 	void
	 */
	public function instala_tabelas()
	{
		include_once(APP.'Model/Util.php');
		$Util = new Util();
		$sql = 'SELECT id from usuarios where id=1';
		$data = $Util->query($sql);
		if (count($data))
		{
			$this->setMsgFlash('O Sistema básico já foi instalado com sucesso !!!','msgOk');
			redirect($this->base.'login');
		} else
		{
			$Util->erros = array();
			$arq = APP.'Model/autohemo.sql';
			if (file_exists($arq))
			{
				$handle  = fopen($arq,"r");
				$texto   = fread($handle, filesize($arq));
				$sqls	 = explode(";",$texto);
				fclose($handle);

				// executando sql a sql
				foreach($sqls as $sql)
				{
					if (trim($sql))
					{
						$res = $Util->query($sql);
						if (!empty($Util->erros))
						{
							echo $sql.'<br />';
							die(debug($Util->erros));
						}
					}
				}
				
				// importando csv
				$tabs = array('cidades');
				foreach($tabs as $_l => $_tabela)
				{
					$arq = APP.'Model/'.$_tabela.'.csv';
					if (file_exists($arq))
					{
						if (!$Util->setPopulaTabela($arq,$_tabela)) die('erro ao importar '.$_tabela);
					}
				}
				$this->setMsgFlash('Sistema básico instalado com sucesso ...','msgOk');
				redirect($this->base.'login');
			} else
			{
				die('O arquivo '.$arq.', n&aacute;o foi localizado ...');
			}
		}
	}

	/**
	 * Exibe a tela de perfil do usuário logado e ainda salva seus dados no banco de dados
	 * 
	 * - somente para usuários logados
	 *
	 * @return void
	 */
	public function meu_perfil()
	{
		if (!isset($_SESSION['Usuario']))
		{
			$this->setMsgFlash('Somente para usuário autenticados','msgErro');
			redirect($this->base.'login');
		}

		require_once(APP.'Model/Usuario.php');
		$Usuario = new Usuario();

		if (isset($_POST['data']))
		{
			if (empty($_POST['data']['0']['Usuario']['senha'])) unset($_POST['data']['0']['Usuario']['senha']);
			if (!$Usuario->save($_POST['data']))
			{
				debug($Usuario->erros);
				die('Erro ao tentar salvar perfil do usuário ');
			} else
			{
				$this->setMsgFlash('Perfil atualizado com sucesso ...','msgOk');
				redirect($this->base.'meu_perfil');
			}
		}
		
		$params['where']['Usuario.id'] = $_SESSION['Usuario']['id'];
		$this->viewVars['data'] = $Usuario->find('all',$params);
		$this->viewVars['esquema']['Usuario'] = $Usuario->esquema;
		$this->sqls['Usuario'] = $Usuario->sqls;
	}

	/**
	 * Retorna uma lista para o get_options de uma campo do model corrente
	 * exemplo:
	 * http://localhost/autohemo/get_options/model:cidade/texto:belo/campo:nome/fields:id,nome,uf
	 *
	 * @return void
	 */
	public function get_options()
	{
		$this->layout = 'ajax';
		$model = ucfirst(strtolower($this->params['model']));
		$texto = isset($this->params['texto']) ? rawurldecode($this->params['texto']) : '';
		$campo = isset($this->params['campo']) ? $this->params['campo'] : 'nome';
		$fields= isset($this->params['fields']) ? $this->params['fields'] : null;

		require_once(APP.'Model/'.$model.'.php');
		$Model = new $model();
		$params['where'][$model.'.'.$campo.' LIKE'] = $texto;
		if (!empty($fields)) $params['fields'] = explode(',',$fields);
		$this->viewVars['data'] = $Model->find('list',$params);
		$this->sqls['Usuario'] = $Model->sqls;
	}

	/**
	 * Lista
	 * Exibe as aplicações dos usuários num dbGrid
	 *
	 * - Acesso permitido apenas para usuários autenticados
	 * - rel001 Relatório analítico com filtros por usuários, data[inicio|fim] e locais de retirada e aplicação
	 * - O Relatório dever paginar o resultado.
	 * - Usuário administrador pode escolher um usuário ou todos, do contrário o filtro é obrigatório.
	 * - Os Relatórios devem possuir ferramenta para exportação em CSV
	 *
	 * @param 	string 	$nome 	Nome do relatório
	 * @return 	void
	 */
	public function lista()
	{
		if (!isset($_SESSION['Usuario']))
		{
			$this->setMsgFlash('Somente para usuário autenticados','msgErro');
			redirect($this->base.'login');
		}

		// variáveis locais
		$nome 	= isset($this->params['nome']) ? $this->params['nome'] : 'rel001';
		$pag 	= isset($this->params['pag']) ? $this->params['pag'] : 1;
		$data 	= array();
		$params = array();
		$filtros= array();

		// filtro padrão
		$filtros['data_ini']['tit'] 	= 'Data Inicial';

		$filtros['data_fim']['tit'] 	= 'Data Final';

		$filtros['usuario_id']['tit'] 	= 'Usuário';
		$filtros['usuario_id']['value'] = $_SESSION['Usuario']['id'];
		$filtros['usuario_id']['options']['0'] = '-- Todos os Usuários --';

		$filtros['local_id']['tit'] 	= 'Local da Aplicação';
		$filtros['local_id']['value'] 	= 0;
		$filtros['local_id']['options']['0'] = '-- Todos os Locais --';

		$filtros['retirada_id']['tit'] 	= 'Local da Retirada';
		$filtros['retirada_id']['value']= 0;
		$filtros['retirada_id']['options']['0'] = '-- Todos os Locais --';

		// buscando os locais para popular as opçoes da retirada e aplicacao
		require_once('Model/Local.php');
		$Local = new Local();
		$params['fields']= array('Local.nome','Local.retirada','Local.aplicacao');
		$params['order'] = array('Local.nome');

		$locais = $Local->find('all',$params);
		$arrLocais = array();
		foreach($locais as $_l => $_arrMods)
		{
			$arrLocais[$_arrMods['Local']['id']] = $_arrMods['Local']['nome'];
			foreach($_arrMods as $_mod => $_arrCmps)
			{
				if ($_arrCmps['retirada']==1)
				{
					$filtros['retirada_id']['options'][$_arrCmps['id']] = $_arrCmps['nome'];
				}
				if ($_arrCmps['aplicacao']==1)
				{
					$filtros['local_id']['options'][$_arrCmps['id']] = $_arrCmps['nome'];
				}
			}
		}
		$this->sqls['Local'] = $Local->sqls;

		// definindo o layout
		$this->layout = isset($this->params['lay']) ? $this->params['lay'] : 'padrao';

		// opções de buca do relatório
		$dataIni['dia'] = '01';
		$dataIni['mes'] = date('m');
		$dataIni['ano'] = date('Y');
		$dataFim['dia'] = date('t');
		$dataFim['mes'] = date('m');
		$dataFim['ano'] = date('Y');
		$filtros['data_ini']['value'] 	= '01'.'/'.date('m').'/'.date('Y');
		$filtros['data_fim']['value'] 	= date('t/m/Y');
		$params 	= array();
		$params['order'] = array('Usuario.nome','Aplicacao.data');
		$params['where']['Aplicacao.data BETWEEN'] = array($dataIni, $dataFim);

		if (!isset($_POST['data']) || $_SESSION['Usuario']['perfilId']>1)
		{
			$params['where']['Aplicacao.usuario_id'] = $_SESSION['Usuario']['id'];
		}

		// definindo o layout
		if ($this->layout=='padrao') $params['pag'] = $pag;

		// incrementando o filtro com base no filtr opostado
		if (isset($_POST['data']))
		{
			foreach($_POST['data'] as $_l => $_arrMods)
			{
				foreach($_arrMods['Filtro'] as $_cmp => $_vlr)
				{
					switch($_cmp)
					{
						case 'data_fim':
						case 'data_ini':
							$dataIni = $_POST['data'][$_l]['Filtro']['data_ini'];
							$dataFim = $_POST['data'][$_l]['Filtro']['data_fim'];
							if (!empty($dataIni) && !empty($dataFim))
							{
								$params['where']['Aplicacao.data BETWEEN'] = array($dataIni, $dataFim);
							}
							$filtros[$_cmp]['value'] = $_vlr['dia'].'/'.$_vlr['mes'].'/'.$_vlr['ano'];
							break;
						case 'retirada_id':
							if (!empty($_vlr))
							{
								$params['where']['Retirada.local_id'] = $_vlr;
							}
							$filtros[$_cmp]['value'] = $_vlr;
							break;
						default:
							if (!empty($_vlr))
							{
								$params['where']['Aplicacao.'.$_cmp] = $_vlr;
							}
							$filtros[$_cmp]['value'] = $_vlr;
							break;
					}
				}
			}
		}

		// opções especifica de cada relatório
		switch($nome)
		{
			case 'rel001':
				$this->viewVars['campos'] 	= array('Usuario.nome','Aplicacao.data','Retirada.nome','Retirada.reti_qtd','Local.nome','Aplicacao.apli_qtd');
				break;
		}

		// instanciando obejto de aplicação
		require_once(APP.'Model/Aplicacao.php');
		$Aplicacao 	= new Aplicacao();

		// recuperando os dados
		$data = $Aplicacao->find('all',$params);
		foreach($data as $_l => $_arrMods)
		{
			$data[$_l]['Retirada']['nome'] = $arrLocais[$_arrMods['Retirada']['local_id']];
		}

		// implementando usuários
		$filtros['usuario_id']['options'] = $Aplicacao->esquema['usuario_id']['options'];
		array_unshift($filtros['usuario_id']['options'], '-- Todos os Usuários --');
		if ($_SESSION['Usuario']['perfilId']>1) $filtros['usuario_id']['input']['disabled'] = 'disabled';

		// atualizando a view
		$this->sqls['Aplicacao'] 	= $Aplicacao->sqls;
		$this->viewVars['data']  	= $data;
		$this->viewVars['esquema'] 	= $Aplicacao->esquema;
		$this->viewVars['paginacao']= $Aplicacao->pag;
		$this->viewVars['filtros'] 	= $filtros;
	}

	/**
	 * Exibe a lista de uma pesquisa para o layout ajax
	 * 
	 * Exemplo para buscar todas as cidades com nome que contenha BELO e uf MG
	 * http://localhost/autohemo/lista_ajax/model:Cidade/campos:id,nome,uf/ordem:Cidade.nome/filtro:Cidade.nome=BELO,Cidade.uf=MG
	 * 
	 * @return	string
	 */
	public function lista_ajax()
	{
		$this->layout = 'ajax';
		$modelClass 	= isset($this->params['model']) 	? $this->params['model'] 	: null;
		$campos		 	= isset($this->params['campos']) 	? $this->params['campos'] 	: '';
		$ordem		 	= isset($this->params['ordem']) 	? $this->params['ordem'] 	: '';
		$filtro		 	= isset($this->params['filtro']) 	? $this->params['filtro'] 	: '';
		$separador		= isset($this->params['separador']) ? $this->params['separador']: '/';
		$pagina 		= isset($this->params['pag']) 		? $this->params['pag'] 		: 1;
		$inputId 		= isset($this->params['inputId']) 	? $this->params['inputId'] 	: null;

		require_once('Model/'.$modelClass.'.php');
		$Model = new $modelClass();
		$params['fields']		= !empty($campos) 	? explode(',',$campos) 	: null;
		$params['order'] 		= !empty($ordem) 	? explode(',',$ordem) 	: null;
		$params['pag']			= $pagina;

		if (!empty($filtro))
		{
			$_filtro = explode(',',$filtro);
			foreach($_filtro as $_l => $_string)
			{
				$s = explode('=',$_string);
				$params['where'][$s['0'].' LIKE'] = rawurldecode($s['1']);
			}
		}
		$lista = $Model->find('all',$params);
		$this->sqls[$modelClass] 	= $Model->sqls;
		$this->viewVars['lista']	= $lista;
		$this->viewVars['s']		= $separador;
		//$this->viewVars['debug']	= true;
		$this->viewVars['inputId'] 	= $inputId;
	}
}
