/*global define*/
define(['jquery', 'router'], function ($, Router) {
    "use strict";
    var SerloModals,
        Modal,
        modals = {},
        modalTemplate = '#modalTemplate';

    Modal = function (options, successCallback) {
        this.$el = $(modalTemplate).clone();

        this.type = options.type || false;
        this.title = options.title || false;
        this.content = options.content;
        this.href = (options.href && options.href !== '#') ? options.href : false;
        this.cancel = options.cancel === undefined ? true : options.cancel;
        this.okayLabel = options.okayLabel || false;

        this.render().show(successCallback);
    };

    Modal.prototype.render = function () {
        var self = this,
            $btn = $('.btn-primary', self.$el);

        $('.modal-body', self.$el).html(self.content);
        $('body').append(self.$el);

        $btn.click(function () {
            if (self.successCallback) {
                self.successCallback();
                self.successCallback = null;

                self.hide();
            } else if (self.href) {
                Router.navigate(self.href);
            } else {
                self.hide();
            }
        });

        if (!self.cancel) {
            $('.btn-default, .close', self.$el).remove();
        }

        if (self.type) {
            $btn.removeClass('btn-primary').addClass('btn-' + this.type);
        }

        if (self.title) {
            $('.modal-title', self.$el).text(self.title);
        }

        if (self.label) {
            $btn.text(self.label);
        }

        return self;
    };

    Modal.prototype.show = function (cb) {
        this.successCallback = cb;
        this.$el.modal('show');
        return this;
    };

    Modal.prototype.hide = function () {
        this.$el.modal('hide');
        return this;
    };

    SerloModals = function () {
        return $(this).each(function () {
            var $self = $(this),
                options = {
                    type: $self.attr('data-type'),
                    title: $self.attr('data-title'),
                    content: $self.attr('data-content'),
                    href: $self.attr('href'),
                    cancel: $self.attr('data-cancel') === "false" ? false : true,
                    label: $self.attr('data-label')
                };

            $self.click(function (e) {
                e.preventDefault();
                new Modal(options);
                return;
            });
        });
    };

    $.fn.SerloModals = SerloModals;

    return {
        show: function (options, uid, cb) {
            if (uid) {
                return modals[uid] ? modals[uid].show(cb) : (modals[uid] = new Modal(options, cb));
            }
            return new Modal(options, cb);
        }
    };
});