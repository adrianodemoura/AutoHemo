$(document).ready(function()
{
	$("#formFiltro select").change(function()
	{
		this.form.submit();
	});
});