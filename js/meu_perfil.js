
/**
 * Valida o formuĺário meu perfil
 */
function validaForm()
{
	var nome 	= $("#UsuarioNome").val();
	var email 	= $("#UsuarioEmail").val();
	var senha1  = $("#UsuarioSenha").val();
	var senha2  = $("#UsuarioSenha2").val();

	// verificando o nome
	if (nome.length==0)
	{
		alert('O Campo nome é de preechimento obrigatório !!!');
		$("#UsuarioNome").focus();
		return false;
	}

	// verificando o email
	if (email.length==0)
	{
		alert('O Campo e-mail é de preechimento obrigatório !!!');
		$("#UsuarioEmail").focus();
		return false;
	}

	// verificando a senha
	if (senha1.length>0 || senha2.length>0)
	{
		console.log(senha1+' '+senha2);
		if (senha1!=senha2)
		{
			alert('As senhans não coincidem !!!');
			$("#UsuarioSenha").focus();
			return false;
		}
	}
	return true;
}

$(document).ready(function()
{
	$("#UsuarioCidade").keypress(function()
	{
		var texto = $(this).val();
		
	});
});