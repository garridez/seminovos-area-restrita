/* !
 * @author: Felipe Amaral
 * @date: 04/11/2015
 * @version: 1.0.0
 *
 */

import 'bootstrap/js/dist/modal.js';

import $ from 'jquery';
declare global {
    interface JQueryStatic {
        jsBsModal(options: Partial<OptionsType>): $HTMLElement;
    }
}
type $HTMLElement = JQuery<HTMLElement>;
type HTMLSKeysType =
    | 'modal'
    | 'modal-dialog'
    | 'modal-content'
    | 'modal-header'
    | 'close'
    | 'modal-title'
    | 'modal-body'
    | 'modal-footer';

type StructureHTMLType = {
    name: HTMLSKeysType;
    childs?: StructureHTMLType | StructureHTMLType[];
};

type OptionsType = {
    autoShow: boolean;
    structureHTML: StructureHTMLType;
    contents: {
        [key in HTMLSKeysType]?: JQuery.htmlString | JQuery.Node | false;
        //modal: JQuery.htmlString | JQuery.Node;

        //'modal-dialog': JQuery.htmlString | JQuery.Node;
        //'modal-content': JQuery.htmlString | JQuery.Node;
        //'modal-header': JQuery.htmlString | JQuery.Node;
        //close: JQuery.htmlString | JQuery.Node;
        //'modal-title': JQuery.htmlString | JQuery.Node | false;
        //'modal-body': JQuery.htmlString | JQuery.Node | false;
        //'modal-footer': JQuery.htmlString | JQuery.Node | false;
    };
};

(function ($: JQueryStatic): void {
    'use strict';
    const pluginName = 'jsBsModal';
    const htmls: {
        [key in HTMLSKeysType]: string;
    } = {
        modal: '<div class="modal fade" tabindex="-1" role="dialog">',
        'modal-dialog': '<div class="modal-dialog" role="document">',
        'modal-content': '<div class="modal-content">',
        'modal-header': '<div class="modal-header">',
        close: `<button
                        type="button"
                        class="btn-close
                        btn btn-light d-flex
                        justify-content-center"
                        data-dismiss="modal"
                    >
                        &times;
                    </button>`,
        'modal-title': '<h4 class="modal-title">',
        'modal-body': '<div class="modal-body">',
        'modal-footer': '<div class="modal-footer">',
    };

    const structureHTML: StructureHTMLType = {
        name: 'modal',
        childs: {
            name: 'modal-dialog',
            childs: {
                name: 'modal-content',
                childs: [
                    {
                        name: 'modal-header',
                        childs: [{ name: 'close' }, { name: 'modal-title' }],
                    },
                    { name: 'modal-body' },
                    { name: 'modal-footer' },
                ],
            },
        },
    };
    const optionsDefault: OptionsType = {
        autoShow: true,
        structureHTML: structureHTML,
        contents: {
            modal: '', // HTML to preprend. Accepts: jQuery Obj, element and string
            'modal-dialog': '', // false or undefined to exclude element
            'modal-content': '',
            'modal-header': '',
            close: '',
            'modal-title': false,
            'modal-body': false,
            'modal-footer': false,
        },
    };

    function makeHtml(structureHTML: StructureHTMLType[], options: OptionsType): $HTMLElement[];
    function makeHtml(structureHTML: StructureHTMLType, options: OptionsType): $HTMLElement | false;
    function makeHtml(
        structureHTML: StructureHTMLType | StructureHTMLType[],
        options: OptionsType,
    ): $HTMLElement | $HTMLElement[] | false {
        console.log(structureHTML);
        if (Array.isArray(structureHTML)) {
            console.log(structureHTML);
            const elements: $HTMLElement[] = [];
            $.each<StructureHTMLType>(structureHTML, function (_i, v) {
                const html = makeHtml(v, options);
                if (html) {
                    elements.push(html);
                }
            });
            return elements;
        }

        if (htmls[structureHTML.name] === undefined) {
            throw new Error(
                `"${structureHTML.name}" is not valid.\nValids: ` + Object.keys(htmls).join(', '),
            );
        }

        const content = options.contents[structureHTML.name];

        if (content === false || content === undefined) {
            return false;
        }

        const html = $(htmls[structureHTML.name]);

        if (content !== '') {
            html.append(content);
        }
        const childs = structureHTML.childs;
        if (childs) {
            $.each(makeHtml(Array.isArray(childs) ? childs : [childs], options), function (_i, el) {
                html.append(el);
            });
        }

        return html;
    }

    function jsBsModalInit(options: OptionsType) {
        const modal = makeHtml(options.structureHTML, options);

        if (modal) {
            $('body').append(modal);
        }

        if (options.autoShow && modal) {
            modal.modal('show');
        }

        return modal;
    }

    $[pluginName] = function (options) {
        options = $.extend({}, optionsDefault, options);

        options.contents = $.extend({}, optionsDefault.contents, options.contents);

        return jsBsModalInit(options as OptionsType) || $();
    };
    //$[pluginName].htmls = htmls;
})($);
