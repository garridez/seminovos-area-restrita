
function parseValueOfObject(maskOptions) {
    if (typeof maskOptions !== 'object') {
        return maskOptions;
    }
    $.each(maskOptions, function (i, obj) {
        if (typeof obj === 'string') {
            if (obj.indexOf('RegExp') !== -1) {
                obj = eval(obj);
                maskOptions[i] = obj;
            }
        } else if (typeof obj === 'object') {
            obj = parseValueOfObject(obj);
        }
    });

    return maskOptions;
}
function setMask($) {
    $('[data-mask]').each(function () {
        var $this = $(this);
        if ($this.data('mask-configured')) {
            return;
        }
        $this.data('mask-configured', true);
        var mask = $this.attr('data-mask');
        var maskOptions = $this.attr('data-mask-options');
        if (maskOptions) {
            maskOptions = JSON.parse(maskOptions.trim());
            maskOptions = parseValueOfObject(maskOptions);
        }
        $this.mask(mask, maskOptions);
        $this.keyup(function () {
            var $this = $(this);
            var name = $this.attr('name');
            console.log(name);
            if (name.indexOf('telefone') !== -1 || name.indexOf('celular') !== -1) {
                console.log("to aq");
                if ($this.val().length === 15) {
                    $this.mask('(00) 90000-0000', maskOptions);
                } else {
                    $this.mask('(00) 0000-00009', maskOptions);
                }
            }
        }).trigger('keyup');
    });
}
module.exports = function () {
    var $ = require('jquery');
    $.jMaskGlobals =  $.jMaskGlobals || {}
    $.jMaskGlobals.dataMask = false;

    require('jquery-mask-plugin');

    setMask($);
    $(document).ajaxComplete(function () {
        setTimeout(function () {
            setMask($);
        }, 100);
    });


};