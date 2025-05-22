import '../../../components/JsBsModal';

import Gallery from 'snbh-site/resources/dist/js/components/Gallery';

export const seletor = '.c-repasse.a-index';
export const callback = async ($: JQueryStatic) => {
    $<HTMLAnchorElement>('.veiculo-detalhes')
        .on('click', async function (e) {
            e.preventDefault();
            const url = String($(this).attr('href'));

            const res = await $.ajax(url);

            const modal = $.jsBsModal({
                autoShow: true,
                contents: {
                    'modal-body': res,
                },
            });
            modal.find('.modal-dialog').css({
                width: '90%',
                maxWidth: '100%',
                minWidth: '300px',
            });
            new Gallery(modal.find('div.gallery-main'));

            modal.on('hidden.bs.modal', function () {
                this.remove();
            });
        });
};
