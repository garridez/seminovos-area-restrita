module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    var stopEvent = require('helpers/StopEvent');
    var stepsContainer = $('.step-container');

    stepsContainer.on('step:pre-exit:video', function (e, stepParams) {

        var video = stepParams.stepElement.find('form [name="video"]');
        var url = video.val().trim();
        if (url === '') {
            return;
        }
        var videoParsed = parseVideo(url);
        if (videoParsed.type === undefined) {
            console.log('Link inválido');
            return stopEvent(e);
        }
        var data = $('form', '#dados-basicos,.step-video').serialize();
        $.post('/carro/video', data, function (e) {
            /**
             * Implementar melhor esse metodo
             */
            return;
            if (e.responseJSON) {
                HandleApiError(e.responseJSON);
            } else {
                HandleApiError(false);
            }
        });
        // O evento não espera o ajax terminar, pois não é um dado crítico
        // E melhora a fluidez da criação do anúncio
        // return stopEvent(e);
    });

};


/**
 * @see https://gist.github.com/yangshun/9892961
 */
function parseVideo(url) {
    // - Supported YouTube URL formats:
    //   - http://www.youtube.com/watch?v=My2FRPA3Gf8
    //   - http://youtu.be/My2FRPA3Gf8
    //   - https://youtube.googleapis.com/v/My2FRPA3Gf8
    // - Supported Vimeo URL formats:
    //   - http://vimeo.com/25451551
    //   - http://player.vimeo.com/video/25451551
    // - Also supports relative URLs:
    //   - //player.vimeo.com/video/25451551

    url.match(/(http:|https:|)\/\/(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/);

    if (RegExp.$3.indexOf('youtu') > -1) {
        var type = 'youtube';
    } else if (RegExp.$3.indexOf('vimeo') > -1) {
        var type = 'vimeo';
    }

    return {
        type: type,
        id: RegExp.$6
    };
}
