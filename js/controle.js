$(document).ready(function()
{
	$(" .divData select").change(function()
	{
		var dia = $("#0RetiradadataDia").val();
		var mes = $("#0RetiradadataMes").val();
		var ano = $("#0RetiradadataAno").val();
		var url = base+'controle/data:'+dia+'-'+mes+'-'+ano;
		document.location.href = url;	
	});
});