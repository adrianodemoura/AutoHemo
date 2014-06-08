$(document).ready(function()
{
	setTotalRetiradas();
	setTotalAplicacoes();
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
		setTotalRetiradas();
		checkSoma();
	});

	$(".rdAplicacao").click(function()
	{
		var id 		= $(this).attr('id');
		var vlr		= $(this).val();
		var checked = $(this).is(':checked');
		$(this).toggleAttr('checked');
		setTotalAplicacoes();
		if (!checkSoma())
		{
			$(this).toggleAttr('checked');
			setTotalAplicacoes();
		}
	});
});

function setTotalRetiradas()
{
	var tot = 0;
	$(".rdRetirada").each(function()
	{
		var checked = $(this).is(':checked');
		if (checked)
		{
			tot += parseFloat($(this).val());
		}
	});
	$("#totRetirada").html(tot);
}

function setTotalAplicacoes()
{
	var tot = 0;
	$(".rdAplicacao").each(function()
	{
		var checked = $(this).is(':checked');
		if (checked)
		{
			tot += parseFloat($(this).val());
		}
	});
	$("#totAplicado").html(tot);
}

function checkSoma()
{
	var totRetirada = $("#totRetirada").text();
	var totAplicado = $("#totAplicado").text();
	if (totAplicado>totRetirada)
	{
		alert("O total aplicado não pode ser maior do que o retirado !!!");
		//$("#btSalvar").attr("disabled","disabled");
		return false;
	}
	//$("#btSalvar").removeAttr("disabled");
	return true;
}