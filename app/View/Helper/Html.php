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
		$input 			= '<div class="divData">';
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
}
