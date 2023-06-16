'use strict';
if (typeof jQuery === 'undefined') {
    throw new Error('This plugin need jQuery');
}

var jdialog;
(function ($) {
    $.confirm = function (options) {
        options.type = 'confirm';
        return jdialog(options);
    };
    jdialog = function (options) {
        if (jdialog.defaults) {
            $.extend(jdialog.pluginDefaults, jdialog.defaults);
        }
        var options = $.extend({}, jdialog.pluginDefaults, options);
        return new Jdialog(options);
    };
    var Jdialog = function (options) {
        $.extend(this, options);
        this.animation = this.animation.toLowerCase();
        this._init();
    };
    Jdialog.prototype = {
        _init: function () {
            this._buildHTML();
            this._bindEvents();

            var that = this;
            setTimeout(function () {
                that.open();
            }, 100);
        },
        animations: ['animate-scale'],
        _buildHTML: function () {
            var that = this;
            this.$te = $(this.template).appendTo('body')
                .addClass(this.theme);
            this.$ae = this.$te.find('.jdialog-box')
                .css({
                    '-webkit-transition': 'all ' + this.animationSpeed / 1000 + 's',
                    'transition': 'all ' + this.animationSpeed / 1000 + 's'
                });
            this.animation = this.animation.split('|');
            $.each(this.animation, function (i, a) {
                that.animation[i] = 'anim-' + a;
            });
            this.$ae.addClass(this.animation.join(' '));
            this.$te.find('div.title').html(this.title);
            this.$te.find('div.content').html(this.content);
            var $btns = this.$te.find('.buttons');
            if (this.confirmButton) {
                this.$confirmButton = $('<button class="btn">' + this.confirmButton + '</button>').appendTo($btns);
                this.$confirmButton.addClass(this.confirmButtonClass);
            }
            if (this.cancelButton) {
                this.$cancelButton = $('<button class="btn">' + this.cancelButton + '</button>').appendTo($btns);
                this.$cancelButton.addClass(this.cancelButtonClass);
            }

        },
        _bindEvents: function () {
            var that = this;

            this.$confirmButton.click(function () {
                that.close();
                that.confirm();
            });
            this.$cancelButton.click(function (e) {
                that.close();
                that.cancel();
            });
        },
        close: function () {
            var that = this;
            this.$ae.addClass(this.animation.join(' '));
            setTimeout(function () {
                that.$te.remove();
            }, this.animationSpeed);
        },
        open: function () {
            var that = this;
            this.$ae.removeClass(this.animation.join(' '));
        }
    };

    jdialog.pluginDefaults = {
        type: 'confirm',
        template: '<div class="jdialog"><div class="jdialog-box"><div class="title"></div><div class="content"></div><div class="buttons"></div><div class="jquery-clear"></div></div></div>',
        title: 'Attention',
        content: 'Are you sure to continue?',
        confirmButton: 'Okay',
        cancelButton: 'Cancel',
        confirmButtonClass: 'btn-default',
        cancelButtonClass: 'btn-default',
        theme: 'white',
        animation: 'scale',
        animationSpeed: 400,
        confirm: function () {
            //
        },
        cancel: function () {
            //
        }
    };
})(jQuery);