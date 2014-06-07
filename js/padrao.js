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