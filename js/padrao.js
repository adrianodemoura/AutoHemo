// padrao.js

/**
 * toggleAttr Plugin
 */
jQuery.fn.toggleAttr = function(attr) {
    return this.each(function() {
        var $this = $(this);
        $this.attr(attr) ? $this.removeAttr(attr) : $this.attr(attr, attr);
    });
};

String.prototype.capitalize = function() 
{
    return this.charAt(0).toUpperCase() + this.slice(1);
}

// *********************************************
// SOLUÇÃO ELEGANTE
// *********************************************
String.prototype.replaceAll = function(de, para)
{
    var str = this;
    var pos = str.indexOf(de);
    while (pos > -1)
    {
		str = str.replace(de, para);
		pos = str.indexOf(de);
	}
    return (str);
}

function setInputVlr(id,vlr,vlr2)
{
    var proximoId   = $("#"+id).next('input').attr('id');
    $("#"+id).val(vlr);
    if(vlr2!=null) $("#"+proximoId).val(vlr2);
}