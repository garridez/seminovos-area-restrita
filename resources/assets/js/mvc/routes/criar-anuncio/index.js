module.exports.seletor = '.c-criar-anuncio.a-index';
function stopEvent(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    return false;
}
module.exports.callback = ($) => {
    require('components/StepPlugin');
    var elementsUrl = $('div.anuncio-steps [data-url]').toArray();
    var loadContentAsync = function () {
        var element = elementsUrl.shift();
        if (element === undefined) {
            return;
        }
        element = $(element);
        $.get(element.data('url'), function (data) {
            element.html(data);
            loadContentAsync();
        });
    };
    loadContentAsync();

    /*
     var rotasAnuncio = window.rotasAnuncio;
     var stepsContainer = $('.anuncio-steps');
     var anuncioSteps = {
     veiculo: stepsContainer.find('.step-veiculo'),
     plano: stepsContainer.find('.step-plano'),
     checkout: stepsContainer.find('.step-checkout'),
     };
     
     $.each(rotasAnuncio.veiculo, function (name, url) {
     $.get(url, function (data) {
     anuncioSteps
     .veiculo
     .find('.step-' + name)
     .html(data);
     });
     });*/

    var stepsContainer = $('.step-container');

    stepsContainer
            .stepPlugin()
            .on('submit', 'form', function (e) {

                $(this).closest('.step-container').stepPlugin('next');
                return stopEvent(e);
            });
//    setTimeout(function () {
//        $('.anuncio-steps').stepPlugin('next');
//    }, 1000);

    $('.btn-voltar').on('click', function () {
        $('.anuncio-steps').stepPlugin('prev');
    });
    $('.btn-continuar').on('click', function () {
//        $('.anuncio-steps').stepPlugin('next');
        var form = stepsContainer.find('[class*="step-"].active > form');
            form.find('[type="submit"]').click();
        if (!form[0].checkValidity()) {
            return;
        }
        
    });
};
