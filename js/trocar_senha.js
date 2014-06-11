function validaForm()
{
	var senha1 = $("#senha").val();
	var senha2 = $("#senha2").val();

	if (senha1!=senha2)
	{
		alert('As senhas não coincidem !!!');
		$("#senha").focus();
		return false;
	}
	if (senha1.length==0 || senha1.length<6)
	{
		alert('Senha inválida !!!');
		$("#senha").focus();
		return false;
	}

	return true;
}