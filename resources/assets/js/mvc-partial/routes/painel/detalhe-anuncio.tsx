import $ from 'jquery';

export const seletor = '.c-painel.a-detalhe-anuncio';
export default () => {
    const $ctx = $('form.filtro-date');
    const $dateStart = $ctx.find('[name="date-start"]');
    const $dateEnd = $ctx.find('[name="date-end"]');

    $dateStart.on('change', function () {
        const val = $(this).val();
        console.log({ val });
        $dateEnd.attr('min', String(val));
    });
    $dateEnd.on('change', function () {
        const val = $(this).val();
        console.log({ val });
        $dateStart.attr('max', String(val));
    });
};
