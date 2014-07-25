$(document).ready(function()
{
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

	$('#ajaxResp').load(url, function(resposta, status, xhr)
	{
		if (status=='success')
		{
			if (resposta.length>10)
			{
				$('#ajaxResp').html(msgEdi);
			} else
			{
				$('#ajaxResp').html(msgAdd);
			}
			console.log(resposta.length);
		}
	});
}