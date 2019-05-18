
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
    });
}
module.exports = function () {
    var $ = require('jquery');
    require('jquery-mask-plugin');

    setMask($);
    $(document).ajaxComplete(function () {
        setTimeout(function () {
            setMask($);
        }, 100);
    });


};