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

		//var checked = $(this).attr('previousValue');
		//var radioChecked = !checked;
		//$(this).attr('checked', radioChecked);
		/*if (checked==true)
		{
			$(this).attr('checked', false);
			//$(this).removeAttr('checked');
		} else
		{
			//$(this).attr('checked', true);
		}*/
	});
});