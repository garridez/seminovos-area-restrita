/**
 * @todo Fazer uma pequena documentação de como se usa esse plugin
 */
var $ = require('jquery');
module.exports = Plugin;


var pluginName = 'stepPlugin';
var defaults = {
    root: '.step-container',
    stepSeletor: '> [class*="step-"]',
    activeSeletor: '> .active',
    activeClass: 'active',
    scrollOffset: 60,
    nestingPropagation: true,
    debug: false
};

function Plugin(element, options) {
    this.$ctx = $(element);
    this.element = element;
    this.opts = $.extend({}, defaults, options);
    this._defaults = defaults;
    this._name = pluginName;
}

$.extend(Plugin.prototype, {

    inLastStep: function () {
        var index = this.getCurrentStepIndex() + 1;
        return index >= this.getSteps().length;
    },
    inFirstStep: function () {
        return this.getCurrentStepIndex() >= 0;
    },
    getSteps: function (indexOrSelector) {
        var steps = this.$ctx.find(this.opts.stepSeletor);

        if (indexOrSelector != undefined) {
            if (typeof indexOrSelector === 'number') {
                return steps.eq(indexOrSelector);
            }
            return steps.filter(indexOrSelector);
        }
        return steps;
    },
    getStepIndex: function (indexOrSelector) {
        var step = this.getSteps(indexOrSelector);
        return this.getSteps()
                .index(step);
    },
    getCurrentStepIndex: function () {
        var index = false;
        var activeClass = this.opts.activeClass;
        this.getSteps()
                .each(function (i) {
                    if ($(this).hasClass(activeClass)) {
                        index = i;
                        return false;
                    }
                });
        return index;
    },
    /**
     *
     * @param {int|string} index Seletor or index of step
     * @returns {boolean}
     */
    goToIndex: function (index, withEvents) {
        withEvents = withEvents === undefined ? true : withEvents
        if (typeof index !== 'number') {
            this._log(index, "!== 'number'");
            index = this.getStepIndex(index);
        }
        var initialIndex = this.getCurrentStepIndex();
        var activeClass = this.opts.activeClass;
        if (index >= this.getSteps().length || index < 0 || index === this.getCurrentStepIndex()) {
            this._log(index, 'out of interval');
            this._log('Current step:', this.getCurrentStepIndex());
            this._log('Max steps:', this.getSteps().length);
            // Mesmo que não tenha um próximo step, dispara o evento
            if (withEvents && !this._triggerEvent('pre-exit', initialIndex)) {
                return false;
            }
            if (!this.inLastStep()) {
                this.getSteps()
                        .removeClass(activeClass);
            }
            return true;
        }

        if (withEvents && !this._triggerEvent('pre-exit', initialIndex, index)) {
            return false;
        }
        if (withEvents && !this._triggerEvent('pre-change', index)) {
            return false;
        }

        var scrollOffset = this.opts.scrollOffset;
        this.getSteps()
                .removeClass(activeClass)
                .eq(index)
                .addClass(activeClass)
                .each(function () {
                    $("html, body").animate({
                        scrollTop: $(this).offset().top - scrollOffset
                    }, 400);
                });
        if (withEvents) {
            this._triggerEvent('exit', initialIndex);
            this._triggerEvent('change', this.getCurrentStepIndex());
        }

        return true;
    },
    goTo: function (index) {
        return this.goToIndex(index);
    },
    next: function (withEvents) {
        if (this.opts.nestingPropagation && this.inLastStep()) {
            var currentIndex = this.getCurrentStepIndex() + 1;
            if (this.goToIndex(currentIndex) === false) {
                return false;
            }
            return this.$ctx
                    .parent()
                    .closest(this.opts.root)[pluginName]('next');
        }

        this.goToIndex(this.getCurrentStepIndex() + 1, withEvents);
    },
    prev: function (withEvents) {
        this.goToIndex(this.getCurrentStepIndex() - 1, withEvents);
    },
    _log: function () {
        if (this.opts.debug) {
            arguments.unshift && arguments.unshift(pluginName + ' Debug:');
            console.log.apply(this, arguments);
        }
    },
    _triggerEvent: function (event, index, nextIndex) { 
        var eventRes = {};// Event Result
        var stepElementTarget = this.getSteps().eq(index);
        var stepElementDeep = null;

        if (stepElementTarget.find('.active').length) {
            stepElementDeep = stepElementTarget.find('.active');
        }
        var extraParams = {
            'stepIndex': index,
            'stepElementTarget': stepElementTarget,
            'stepElementDeep': stepElementDeep,
            'stepChangeTo': nextIndex
        };
        this._log('Event triggered:', 'step:' + event);

        eventRes.a = this.$ctx.triggerHandler('step:' + event, extraParams);

        var eventName = 'step:' + event + ':index-' + index;
        eventRes.b = this.$ctx.triggerHandler(eventName, extraParams);
        this._log('Event triggered:', eventName);

        var stepLabel = stepElementTarget.data('step-label');
        if (stepLabel) {
            eventName = 'step:' + event + ':' + stepLabel;
            eventRes.c = this.$ctx.triggerHandler(eventName, extraParams);
            this._log('Event triggered:', eventName);
        }
        // Se pelo menos 1 for falso, então é retornado falso
        return !(eventRes.a === false || eventRes.b === false || eventRes.c === false);
    }
});


$.fn[ pluginName ] = function () {
    var __arguments = arguments;
    var options = __arguments[0];

    var returnValue = false;
    var value = null;
    var chain = this.each(function () {
        var instance;

        if (!(instance = $.data(this, 'plugin_' + pluginName))) {
            var opts = typeof options === 'object' ? options : {};
            instance = $.data(this, 'plugin_' + pluginName, new Plugin(this, opts));
        }

        if (typeof options === 'string') {
            var args = Array.prototype.slice.call(__arguments, 1);
            returnValue = true;

            value = instance[options].apply(instance, args);
            return false;
        }

    });
    return returnValue ? value : chain;
};
