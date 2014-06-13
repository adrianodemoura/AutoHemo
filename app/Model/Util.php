<?php
/**
 * Class Model
 * 
 * @package		Util
 * @package		Autohemo.Model
 */
require_once(APP.'Model/Model.php');
class Util extends Model {
	/**
	 * Popula uma tabela do banco com seu aquivo CSV
	 * 
	 * @parameter 	$arq	string	Caminho completo com o nome do arquivo
	 * @parameter	$tabela	string	Nome da tabela a ser populada
	 * @return		boolean
	 */
	public function setPopulaTabela($arq='',$tabela='')
	{
		// mandando bala se o csv existe
		if (file_exists($arq))
		{
			$handle  	= fopen($arq,"r");
			$l 			= 0;
			$campos 	= '';
			$cmps	 	= array();
			$valores 	= '';

			// executando linha a linha
			while ($linha = fgetcsv($handle, 2048, ";"))
			{
				if (!$l)
				{
					$i = 0;
					$t = count($linha);
					foreach($linha as $campo)
					{
						$campos .= $campo;
						$i++;
						if ($i!=$t) $campos .= ',';
					}
					// montand os campos da tabela
					$arr_campos = explode(',',$campos);
				} else
				{
					$valores  = '';
					$i = 0;
					$t = count($linha);
					foreach($linha as $valor)
					{
						if ($arr_campos[$i]=='criado' || $arr_campos[$i]=='modificado') $valor = date("Y-m-d H:i:s");
						$valores .= "'".str_replace("'","\'",$valor)."'";
						$i++;
						if ($i!=$t) $valores .= ',';
					}
					$sql = 'INSERT INTO '.$tabela.' ('.$campos.') VALUES ('.$valores.')';
					try
					{
						$this->query($sql);
					} catch (exception $ex)
					{
						die('erro ao executar: '.$sql.'<br />'.$ex->getMessage());
					}
				}
				$l++;
			}
			fclose($handle);
			return true;
		} else return false;
	}
}
