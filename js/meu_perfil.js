
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
	$("#UsuarioCidade").keyup(function()
	{
		var texto = $(this).val();
		var url = base+'lista_ajax/model:Cidade/campos:id,nome,uf/ordem:Cidade.nome/filtro:Cidade.nome='+texto;
		var pag = 1;
		var url = url + '/pag:'+pag;
console.log(url);
		$('#ajaxResp').load(url, function(resposta, status, xhr)
		{
			if (status=='success')
			{
				/*$("#ajaxResp").html("");
				//console.log(resposta);
				var jArrResposta 	= resposta.split('*');
				var table			= '<table border="1px" id="ajaxTab">'+"\n";
				$.each(jArrResposta, function(i, linha)
				{
					var jArrLinha = linha.split(';');
					if (jArrLinha[0].length>0)
					{
						table += "<tr class='ajaxTr' id='"+i+"ajaxTr'>\n";
						var tds = [];
						$.each(jArrLinha, function(o, vlr)
						{
							if (vlr)
							{
								table += "\t<td class='ajaxTd' ";
								if (o==0) table += "style='display: none;' ";
								table += "onclick='setItemAjax("+i+"); showLista();'>"+vlr+"</td>\n";
								tem = 1;
							}
						});
						table += "</tr>\n";
					}
				});
				table += "</table>\n";
				$("#ajaxResp").html(table);*/
			}
		});
	});
});
