module.exports = function ($) {
    var MobileView = function (element, options) {
        var self = this;
        self.options = $.extend(true, {}, $.fn.pgMobileViews.defaults, options);
        self.element = $(element);
        self.element.on('click', function (e) {
            e.preventDefault();
            var data = self.element.data();
            var el = $(data.viewPort);
            var toView = data.toggleView;
            if (data.toggleView != null) {
                el.children().last().children('.view').hide();
                $(data.toggleView).show();
            } else {
                toView = el.last();
            }
            el.toggleClass(data.viewAnimation);
            self.options.onNavigate(toView, data.viewAnimation);
            return false;
        });
        return this; // enable chaining
    };
    $.fn.pgMobileViews = function (options) {
        return new MobileView(this, options);
    };

    $.fn.pgMobileViews.defaults = {
        //Returns Target View & Animation Type
        onNavigate: function (_view, _animation) {},
    };
    // MOBILE VIEW DATA API
    //===================

    $(window).on('load', function () {
        $('[data-navigate="view"]').each(function () {
            var $mobileView = $(this);
            $mobileView.pgMobileViews();
        });
    });
};
