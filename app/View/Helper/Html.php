<?php
/**
 * Class html
 *
 * Helper paa a view
 * 
 * @package 	Html
 * @subpcakge	Autohemo.view.helper
 */
class Html {
	/**
	 * Retorna o input tipo data nofrmato brasileiro, d/m/Y
	 * 
	 * @return 	string
	 */
	public function getInputData($cmp='', $prop=array())
	{
		$input 			= '<div class="divReg divData">';
		$ano 			= isset($prop['ano']) ? $prop['ano'] : date('Y');
		$intervaloAno 	= isset($prop['intervaloAno']) ? $prop['intervaloAno'] : 2;
		$valor 			= isset($prop['value']) ? $prop['value'] : null;

		if (!$valor && isset($prop['default']))
		{
			$valor 		= isset($prop['default']) ? $prop['default'] : null;
		}
		if ($valor)
		{
			$valor = str_replace('-', '/', $valor);
			$arrVlr = explode('/', $valor);
		}
		$c 		= explode('.', $cmp);
		$name 	= $c['0']."][".$c['1']."][".$c['2'];
		$id 	= $c['0'].$c['1'].$c['2'];

		$inDia = "<select name='data[".$name."][dia]' id='".$id."Dia'>";
		for($i=1; $i<32; $i++)
		{
			$i = '00'.$i; $i = substr($i, strlen($i)-2,2);
			$inDia .= "<option ";
			if (isset($arrVlr['0']) && $arrVlr['0']==$i) $inDia .= " selected='selected' ";
			$inDia .= "value='$i'>$i</option>";
		}
		$inDia .= "</select>";
	
		$inMes = "<select name='data[".$name."][mes]' id='".$id."Mes'>";
		for($i=1; $i<13; $i++)
		{
			$i = '00'.$i; $i = substr($i, strlen($i)-2,2);
			$inMes .= "<option ";
			if (isset($arrVlr['1']) && $arrVlr['1']==$i) $inMes .= " selected='selected' ";
			$inMes .= "value='$i'>$i</option>";
		}
		$inMes .= "</select>";

		$inAno = "<select name='data[".$name."][ano]' id='".$id."Ano'>";
		for($i=$ano; $i>($ano-$intervaloAno); $i--)
		{
			$inAno .= "<option ";
			if (isset($arrVlr['2']) && $arrVlr['2']==$i) $inAno .= " selected='selected' ";
			$inAno .= "value='$i'>$i</option>";
		}
		$inAno .= "</select>";

		$input .= $inDia.$inMes.$inAno.'</div>';

		return $input;
	}

	/**
	 * Retorna o input form
	 *
	 * @param 	string 	$cmp 	Nome do campo no formado linha.model.campo
	 * @param 	array 	$prop 	Matriz com as propriedades do campo
	 * @return 	string 	$input
	 */
	public function getInputText($cmp='', $prop=array())
	{
		$valor 			= isset($prop['value']) ? $prop['value'] : null;
		$a 				= explode('.',$cmp);
		$tipo 			= isset($prop['type']) ? $prop['type'] : 'text';
		$tagInput		= isset($prop['input']) ? $prop['input'] : array();

		// valor padrão
		if (!$valor && isset($prop['default']))
		{
			$valor 		= isset($prop['default']) ? $prop['default'] : null;
		}

		// se tem options vira um select
		if (isset($prop['options']))
		{
			$tipo = 'select';

		}

		$input 	= '<div id="div'.$a['0'].$a['1'].$a['2'].'" class="divReg div'.ucfirst($a['1']).ucfirst($a['2']).'">';
		switch ($tipo) 
		{
			case 'select':
				$input .= "<select 
					name='data[".$a['0']."][".$a['1']."][".$a['2']."]' 
					id='".$a['0'].ucfirst($a['1']).ucfirst($a['2'])."'
					class='se".ucfirst($a['1']).ucfirst($a['2'])."' ";
				foreach ($tagInput as $_tag => $_vlr) $input .= "$_tag='$_vlr' ";
				$input .= ">";
				foreach($prop['options'] as $_id => $_vlr)
				{
					$s = "";
					if ($_id==$valor) $s = " selected='selected' ";
					$input .= "<option $s value='".$_id."'>$_vlr</option>";
				}
				$input .= "</select>";
				break;

			default:
				$input .= "<input type='text' 
				name='data[".$a['0']."][".$a['1']."][".$a['2']."]'
				id='".$a['0'].ucfirst($a['1']).ucfirst($a['2'])."'
				value='".$valor."' ";
				foreach ($tagInput as $_tag => $_vlr) $input .= "$_tag='$_vlr' ";
				$input .= "/>";
				break;
		};
		

		$input .= '</div>';

		return $input;
	}


}
