export const seletor = '.c-repasse.a-meus-anuncios';
export const callback = async ($: JQueryStatic) => {
    $('.deletar-repasse').on('click', async function (e) {
        e.preventDefault();
        if (confirm('Desejar realmente apagar?')) {
            await $.get('/repasse/deletar/' + $(this).data('idrepasse'));
            $(this).closest('.repasse-row').remove();
        }
    });
};
