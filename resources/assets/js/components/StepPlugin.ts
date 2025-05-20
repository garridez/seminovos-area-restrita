/**
 * @todo Fazer uma pequena documentação de como se usa esse plugin
 */
import $ from 'jquery';

const pluginName = 'stepPlugin';

const defaults: PluginOptions = {
    root: '.step-container',
    stepSeletor: '> [class*="step-"]',
    activeSeletor: '> .active',
    activeClass: 'active',
    scrollOffset: 60,
    nestingPropagation: true,
    debug: false,
};

type PluginOptions = {
    root: string;
    stepSeletor: string;
    activeSeletor: string;
    activeClass: string;
    scrollOffset: number;
    nestingPropagation: boolean;
    debug: boolean;
};

type $HTMLElement = JQuery<HTMLElement>;
interface StepPluginCall {
    (): $HTMLElement;
    (options: Partial<PluginOptions>): $HTMLElement;
    //(method: string, ...params: (never)[]): number | string | boolean;
    (method: 'getSteps'): $HTMLElement;
    (method: 'goTo', index: number): $HTMLElement;
    (method: 'goTo', selector: string): $HTMLElement;
    (method: 'goTo', element: JQuery<HTMLElement> | HTMLElement): $HTMLElement;
    (method: 'inLastStep'): boolean;
    (method: 'next', withEvents?: boolean): void;
    (method: 'prev', withEvents?: boolean): void;
}

declare global {
    interface JQuery {
        stepPlugin: StepPluginCall;
    }
}

export default class Plugin {
    $ctx: $HTMLElement;
    element;
    opts: PluginOptions;
    _defaults: PluginOptions;
    _name: string;
    constructor(element: $HTMLElement | HTMLElement, options: Partial<PluginOptions>) {
        this.$ctx = $(element);
        this.element = element;
        this.opts = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
    }

    inLastStep() {
        const index = Number(this.getCurrentStepIndex()) + 1;
        return index >= this.getSteps().length;
    }
    inFirstStep() {
        return Number(this.getCurrentStepIndex()) >= 0;
    }
    getSteps(indexOrSelector: JQuery<HTMLElement> | number | string | undefined = undefined) {
        const steps = this.$ctx.find(this.opts.stepSeletor);

        if (indexOrSelector != undefined) {
            if (typeof indexOrSelector === 'number') {
                return steps.eq(indexOrSelector);
            }
            return steps.filter(indexOrSelector);
        }
        return steps;
    }
    getStepIndex(indexOrSelector: number | string) {
        const step = this.getSteps(indexOrSelector);
        return this.getSteps().index(step);
    }
    getCurrentStepIndex(): number | false {
        let index: false | number = false;
        const activeClass = this.opts.activeClass;
        this.getSteps().each(function (i) {
            if ($(this).hasClass(activeClass)) {
                index = i;
                return false;
            }
        });
        return index;
    }

    goToIndex(index: number | string, withEvents: boolean | undefined = undefined) {
        withEvents = withEvents === undefined ? true : withEvents;
        if (typeof index !== 'number') {
            this._log(index, "!== 'number'");
            index = this.getStepIndex(index);
        }
        const initialIndex = this.getCurrentStepIndex() || 0;
        const activeClass = this.opts.activeClass;
        if (index >= this.getSteps().length || index < 0 || index === this.getCurrentStepIndex()) {
            this._log(index, 'out of interval');
            this._log('Current step:', this.getCurrentStepIndex());
            this._log('Max steps:', this.getSteps().length);
            // Mesmo que não tenha um próximo step, dispara o evento
            if (withEvents && !this._triggerEvent('pre-exit', initialIndex)) {
                return false;
            }
            if (!this.inLastStep()) {
                this.getSteps().removeClass(activeClass);
            }
            return true;
        }

        if (withEvents && !this._triggerEvent('pre-exit', initialIndex, index)) {
            return false;
        }
        if (withEvents && !this._triggerEvent('pre-change', index)) {
            return false;
        }

        const scrollOffset = this.opts.scrollOffset;
        this.getSteps()
            .removeClass(activeClass)
            .eq(index)
            .addClass(activeClass)
            .each(function () {
                $('html, body').animate(
                    {
                        scrollTop: (($(this).offset() || {}).top || 0) - scrollOffset,
                    },
                    400,
                );
            });
        if (withEvents) {
            this._triggerEvent('exit', initialIndex);
            this._triggerEvent('change', this.getCurrentStepIndex() || 0);
        }

        return true;
    }
    goTo(index: number | string) {
        return this.goToIndex(index);
    }
    next(withEvents: boolean) {
        if (this.opts.nestingPropagation && this.inLastStep()) {
            const currentIndex = Number(this.getCurrentStepIndex()) + 1;
            if (this.goToIndex(currentIndex) === false) {
                return false;
            }
            return this.$ctx.parent().closest(this.opts.root).stepPlugin('next');
        }

        this.goToIndex(Number(this.getCurrentStepIndex()) + 1, withEvents);
    }
    prev(withEvents: boolean) {
        this.goToIndex(Number(this.getCurrentStepIndex()) - 1, withEvents);
    }
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    _log(...fargs: any[]) {
        if (this.opts.debug) {
            const args = [...fargs];
            const root =
                '.' +
                (this.$ctx.attr('class') || '')
                    .split(/\s+/)
                    .filter((e) => e.includes('step-') && e !== 'step-0')
                    .join('.');
            args.unshift(root);
            args.unshift(pluginName + ' Debug:');

            console.log.apply(this, args);
        }
    }
    _triggerEvent(
        event: string,
        index: number,
        nextIndex: number | string | undefined = undefined,
    ) {
        const eventRes: {
            a?: boolean;
            b?: boolean;
            c?: boolean;
        } = {}; // Event Result
        const stepElementTarget = this.getSteps().eq(index);
        let stepElementDeep = null;

        if (stepElementTarget.find('.active').length) {
            stepElementDeep = stepElementTarget.find('.active');
        }
        const extraParams = {
            stepIndex: index,
            stepElementTarget: stepElementTarget,
            stepElementDeep: stepElementDeep,
            stepChangeTo: nextIndex,
        };
        this._log('Event triggered:', 'step:' + event);

        eventRes.a = this.$ctx.triggerHandler('step:' + event, extraParams);

        let eventName = 'step:' + event + ':index-' + index;
        eventRes.b = this.$ctx.triggerHandler(eventName, extraParams);
        this._log('Event triggered:', eventName);

        const stepLabel = stepElementTarget.data('step-label');
        if (stepLabel) {
            eventName = 'step:' + event + ':' + stepLabel;
            eventRes.c = this.$ctx.triggerHandler(eventName, extraParams);
            this._log('Event triggered:', eventName);
        }
        // Se pelo menos 1 for falso, então é retornado falso
        return !(eventRes.a === false || eventRes.b === false || eventRes.c === false);
    }
}
// @ts-expect-error Isso funciona de qualquer forma
$.fn.stepPlugin = function (...args) {
    const __arguments = args;
    const options = __arguments[0];

    let returnValue = false;
    let value = null;
    const chain = this.each(function () {
        let instance;

        if (!(instance = $.data(this, 'plugin_' + pluginName))) {
            const opts = typeof options === 'object' ? options : {};
            instance = $.data(
                this,
                'plugin_' + pluginName,
                new Plugin(this, opts as PluginOptions),
            );
        }

        if (typeof options === 'string') {
            const args = Array.prototype.slice.call(__arguments, 1);
            returnValue = true;

            value = instance[options](...args);
            return false;
        }
    });
    return returnValue ? value : chain;
};
