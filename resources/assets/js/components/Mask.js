

function setMask($) {
    $('[data-mask]').each(function () {
        var $this = $(this);
        if ($this.data('mask-configured')) {
            return;
        }
        var mask = $this.data('mask');
        var maskOptions = $this.data('mask-options');
        if (typeof maskOptions === 'string') {
            maskOptions = JSON.parse(maskOptions.trim());

        }
        $this.mask(mask, maskOptions);
        console.log(maskOptions);
                $this.data('mask-configured', true);
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