<?php 
if (isset($erros['0'])) : ?>

<table border='1px'>
	<tr>
		<th align="center">#</th>
		<th align="center">Código</th>
		<th align="center">Mensagem</th>
	</tr>
	<?php 

		foreach($erros as $_l => $_arrProp) :
		echo "<tr>";
		echo "<td align='center' width='30px'>".($_l+1)."</td>";
		echo "<td>".$_arrProp['codigo']."</td>";
		echo "<td>".$_arrProp['mensagem']."</td>";
		echo "</tr>";
		endforeach 
	?>
</table>

<?php endif ?>
