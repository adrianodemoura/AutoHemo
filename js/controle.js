$(document).ready(function()
{
	$(".divData select").change(function()
	{
		var dia = $("#0RetiradadataDia").val();
		var mes = $("#0RetiradadataMes").val();
		var ano = $("#0RetiradadataAno").val();
		var url = base+'controle/data:'+dia+'-'+mes+'-'+ano;
		document.location.href = url;	
	});

	$(".rdRetirada").click(function()
	{
		var id 		= $(this).attr('id');
		var vlr		= $(this).val();
		var checked = $(this).is(':checked');
		$(this).toggleAttr('checked');
	});

	$(".rdAplicacao").click(function()
	{
		var id 		= $(this).attr('id');
		var vlr		= $(this).val();
		var checked = $(this).is(':checked');
		$(this).toggleAttr('checked');
	});
});