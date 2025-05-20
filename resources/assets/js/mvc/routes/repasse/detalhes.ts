import Gallery from 'snbh-site/resources/dist/js/components/Gallery';

export const seletor = '.c-repasse.a-detalhes';
export const callback = async ($: JQueryStatic) => {

    new Gallery($('div.gallery-main'));
};
