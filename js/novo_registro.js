function validaForm()
{
	var nome 	= $("#UsuarioNome").val();
	var email 	= $("#UsuarioEmail").val();
	if (nome.length==0)
	{
		alert('Nome inválido !!!');
		$("#UsuarioNome").focus();
		return false;
	}
	if (email.length==0)
	{
		alert('e-mail inválido !!!');
		$("#UsuarioEmail").focus();
		return false;
	}
	return true;
}