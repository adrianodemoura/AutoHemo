$(document).ready(function()
{
	$("#0RetiradadataDia").focus();
	setCheckData();
	$(".divReg select").change(function()
	{
		setCheckData();
	});
});

function setCheckData()
{
	var dia = $("#0RetiradadataDia").val();
	var mes = $("#0RetiradadataMes").val();
	var ano = $("#0RetiradadataAno").val();
	var url = base+'checa/data:'+dia+'-'+mes+'-'+ano;
	var urlControle = base+'controle/data:'+dia+'-'+mes+'-'+ano;
	var msgAdd = 'Esta data ainda não possui aplicação, <a href="'+urlControle+'">aqui</a> aqui para criá-la.';
	var msgEdi = 'Esta data já possui aplicação, clique <a href="'+urlControle+'">aqui</a> para editá-la.';

	$('#ajaxResp').html(' Aguarde ... ');
	$('#ajaxResp').load(url, function(resposta, status, xhr)
	{
		if (status=='success')
		{
			$('#ajaxResp').html('');
			if (resposta.length>10)
			{
				$('#btNovo').fadeOut(function() { $('#btEditar').fadeIn(1000); });
				$('#btEditar').attr('onclick','document.location.href='+"'"+urlControle+"'");
			} else
			{
				$('#btEditar').fadeOut(function() { $('#btNovo').fadeIn(1000); });
				$('#btNovo').attr('onclick','document.location.href='+"'"+urlControle+"'");
			}
		}
	});
}
