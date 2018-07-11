var $ = require('jquery');
module.exports = Plugin;


var pluginName = 'stepPlugin';
var defaults = {
    root: '.step-container',
    stepSeletor: '> [class*="step-"]',
    activeSeletor: '> .active',
    activeClass: 'active',
    scrollOffset: 60
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

        if (indexOrSelector) {
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
        var index = 0;
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
     * @param int|string index Seletor or index of step
     */
    goToIndex: function (index) {
        if (typeof index !== 'number') {
            index = this.getStepIndex(index);
        }
        if (index >= this.getSteps().length || index < 0 || index === this.getCurrentStepIndex()) {
            return;
        }

        var activeClass = this.opts.activeClass;
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
    },
    goTo: function (index) {
        this.goToIndex(index);
    },
    next: function () {
        this.goToIndex(this.getCurrentStepIndex() + 1);
    },
    prev: function () {
        this.goToIndex(this.getCurrentStepIndex() - 1);
    },
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
