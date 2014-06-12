<?php 
if (isset($Site->sqls) && !empty($Site->sqls)) : ?>

<table border='1px' align='center'>
<tr>
	<th><center>#</center></th>
	<th><center>Model</center></th>
	<th><center>sql</center></th>
	<th width="90px"><center>tempo</center></th>
	<th width="60px"><center>linhas</center></th>
</tr>
	<?php 
		foreach($Site->sqls as $_mod => $_arrL) :
		foreach($_arrL as $_l => $_arrProp) :
		echo "<tr>";

			echo "<td align='center'";
			if ($_l%2>0) echo " class='ativa' ";
			echo ">".($_l+1)."</td>";

			echo "<td align='center'";
			if ($_l%2>0) echo " class='ativa' ";
			echo ">".$_mod."</td>";

			echo "<td align='left'";
			if ($_l%2>0) echo " class='ativa' ";
			echo ">".$_arrProp['sql']."</td>";

			echo "<td align='center'";
			if ($_l%2>0) echo " class='ativa' ";
			echo ">".str_replace('.', ',', $_arrProp['ts'])." segs.</td>";

			echo "<td align='center'";
			if ($_l%2==1) echo " class='ativa' ";
			echo ">".$_arrProp['li']."</td>";

		echo "</tr>";
		endforeach; 
		endforeach;
	?>
</table>

<?php endif; ?>
