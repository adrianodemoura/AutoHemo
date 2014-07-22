
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
	//$("#UsuarioNome").select();

	$('#ajaxRespCidade').click(function()
	{
		$(this).fadeOut();
	});

	$("#UsuarioCidade").keyup(function()
	{
		var texto 		= $(this).val();
		var anteriorId	= $(this).prev().attr('id');
		var url 		= base+'lista_ajax/model:Cidade/campos:id,nome,uf/ordem:Cidade.nome/inputId:'+anteriorId+'/filtro:Cidade.nome='+texto;
		url 			= url.replaceAll(' ','%20');
		var pag 		= 1;
		var url 		= url + '/pag:'+pag;

		// se tem texto pra pesquisa
		if (texto.length>0)
		{
			$('#ajaxRespCidade').load(url, function(resposta, status, xhr)
			{
				if (status=='success')
				{
					$('#ajaxRespCidade').fadeIn();
				}
			});
		}

	});
});
