import HandleApiError from '../../../components/HandleApiError';
import stopEvent from '../../../helpers/StopEvent';

export const seletor = '.c-criar-anuncio.a-index';
export const callback = ($) => {
  const stepsContainer = $('.step-container');
  let urlSaved = 'none';

  // Flags para direção do passo
  let goingNext = false;
  let goingPrev = false;

  // Detecta cliques explícitos nos botões do seu stepper
  // ajuste os seletores conforme seu HTML
  $(document)
    .on('click', '[data-step-action="next"], .btn-next', () => { goingNext = true; goingPrev = false; })
    .on('click', '[data-step-action="prev"], .btn-prev', () => { goingPrev = true; goingNext = false; });

  $('.anuncio-steps').on('steps-loaded', function () {
    const $input = $('form[name="form_videoVeiculo"]').find('input[name="video"]');

    const updatePreview = () => {
      const v = $input.val().trim();
      if (v === '') {
        $('#remove-link-youtube').hide();
        $('.preview-video').addClass('d-flex').removeClass('d-none');
        $('#videoWindow').addClass('d-none').attr('src', '');
        return;
      }
      $('#remove-link-youtube').show();

      const result = parseVideo(v);
      if (result.type === 'youtube') {
        $('.preview-video').removeClass('d-flex').addClass('d-none');
        $('#videoWindow').removeClass('d-none').attr('src', 'https://www.youtube.com/embed/' + result.id);
      } else {
        // URL não suportada → esconde player
        $('#videoWindow').addClass('d-none').attr('src', '');
        $('.preview-video').addClass('d-flex').removeClass('d-none');
      }
    };

    // Debounce leve evita repinturas excessivas
    let t;
    $input.on('keyup', function () {
      clearTimeout(t);
      t = setTimeout(updatePreview, 120);
    }).trigger('keyup');
  });

  stepsContainer.on('step:pre-exit:video', function (e) {
    // Se for voltar, nunca bloqueie
    if (goingPrev) {
      goingPrev = false;
      goingNext = false;
      return true;
    }

    const stepVideo = $('.step-video');
    const $videoField = stepVideo.find('form [name="video"]');
    const url = ($videoField.val() || '').trim();

    // Nada mudou ou vazio → deixa avançar sem AJAX
    if (url === urlSaved || url === '') {
      goingNext = false;
      return true;
    }

    // Validar URL antes de salvar
    const parsed = parseVideo(url);
    if (!parsed.type) {
      console.log('Link inválido');
      goingNext = false;
      return stopEvent(e);
    }

    // Só aqui bloqueia a saída, salva e navega manualmente
    const $ctx = $('#dados-basicos, .step-video');
    const data = $ctx.find('form').serialize();

    $.ajax({
      url: '/carro/video',
      data,
      type: 'POST',
      dataType: 'json',
      success: function (resp) {
        if (!HandleApiError(resp)) return;
        urlSaved = url;
        // Avança explicitamente após salvar
        stepVideo.closest('.step-container').stepPlugin('next');
      },
      error: function (err) {
        if (err.responseJSON) HandleApiError(err.responseJSON);
        else HandleApiError(false);
      },
      complete: function () {
        goingNext = false;
      },
    });

    // Bloqueia apenas este caso de avançar com salvamento
    return stopEvent(e);
  });
};

/**
 * Parser seguro de YouTube/Vimeo.
 * @param {string} url
 * @returns {{type?: 'youtube'|'vimeo', id?: string}}
 */
function parseVideo(url) {
  const re = /(http:|https:|)\/\/(player\.|www\.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/|shorts\/)?([A-Za-z0-9._%-]+)(\&\S+)?/;
  const m = url.match(re);
  if (!m) return {};

  const host = m[3] || '';
  const id = m[6] || '';

  if (host.includes('youtu')) return { type: 'youtube', id };
  if (host.includes('vimeo')) return { type: 'vimeo', id };
  return {};
}