/* WP External Links Plugin - Admin */
/*global jQuery, window*/
jQuery(function ($) {
    'use strict';

    /* Tipsy Plugin */
    (function () {
        $.fn.tipsy = function (options) {
            options = $.extend({}, $.fn.tipsy.defaults, options);

            return this.each(function () {
                var opts = $.fn.tipsy.elementOptions(this, options);

                $(this).hover(function () {
                    $.data(this, 'cancel.tipsy', true);

                    var tip = $.data(this, 'active.tipsy');
                    if (!tip) {
                        tip = $('<div class="tipsy"><div class="tipsy-inner"/></div>');
                        tip.css({position: 'absolute', zIndex: 100000});
                        $.data(this, 'active.tipsy', tip);
                    }

                    if ($(this).attr('title') || typeof $(this).attr('original-title') !== 'string') {
                        $(this).attr('original-title', $(this).attr('title') || '').removeAttr('title');
                    }

                    var title;
                    if (typeof opts.title === 'string') {
                        title = $(this).attr(opts.title === 'title' ? 'original-title' : opts.title);
                    } else if (typeof opts.title === 'function') {
                        title = opts.title.call(this);
                    }

                    tip.find('.tipsy-inner')[opts.html ? 'html' : 'text'](title || opts.fallback);

                    var pos = $.extend({}, $(this).offset(), {width: this.offsetWidth, height: this.offsetHeight});
                    tip.get(0).className = 'tipsy'; // reset classname in case of dynamic gravity
                    tip.remove().css({top: 0, left: 0, visibility: 'hidden', display: 'block'}).appendTo(document.body);
                    var actualWidth = tip[0].offsetWidth, actualHeight = tip[0].offsetHeight;
                    var gravity = (typeof opts.gravity == 'function') ? opts.gravity.call(this) : opts.gravity;

                    switch (gravity.charAt(0)) {
                        case 'n':
                            tip.css({top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2}).addClass('tipsy-north');
                            break;
                        case 's':
                            tip.css({top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2}).addClass('tipsy-south');
                            break;
                        case 'e':
                            tip.css({top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth}).addClass('tipsy-east');
                            break;
                        case 'w':
                            tip.css({top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width}).addClass('tipsy-west');
                            break;
                    }

                    if (opts.fade) {
                        tip.css({opacity: 0, display: 'block', visibility: 'visible'}).animate({opacity: 0.9});
                    } else {
                        tip.css({visibility: 'visible'});
                    }

                }, function () {
                    $.data(this, 'cancel.tipsy', false);
                    var self = this;
                    setTimeout(function () {
                        if ($.data(this, 'cancel.tipsy')) return;
                        var tip = $.data(self, 'active.tipsy');
                        if (opts.fade) {
                            tip.stop().fadeOut(function () { $(this).remove(); });
                        } else {
                            tip.remove();
                        }
                    }, 100);
                });
            });
        };

        // Overwrite this method to provide options on a per-element basis.
        // For example, you could store the gravity in a 'tipsy-gravity' attribute:
        // return $.extend({}, options, {gravity: $(ele).attr('tipsy-gravity') || 'n' });
        // (remember - do not modify 'options' in place!)
        $.fn.tipsy.elementOptions = function (ele, options) {
            return $.metadata ? $.extend({}, options, $(ele).metadata()) : options;
        };

        $.fn.tipsy.defaults = {
            fade: false,
            fallback: '',
            gravity: 'w',
            html: false,
            title: 'title'
        };

        $.fn.tipsy.autoNS = function () {
            return $(this).offset().top > ($(document).scrollTop() + $(window).height() / 2) ? 's' : 'n';
        };

        $.fn.tipsy.autoWE = function () {
            return $(this).offset().left > ($(document).scrollLeft() + $(window).width() / 2) ? 'e' : 'w';
        };

    })(); // End Tipsy Plugin


    $('#setting-error-settings_updated').click(function () {
        $(this).hide();
    });

    // option filter page
    $('input#filter_page')
        .change(function () {
            var $i = $('input#filter_posts, input#filter_comments, input#filter_widgets');

            if ($(this).attr('checked')) {
                $i.attr('disabled', true)
                    .attr('checked', true);
            } else {
                $i.attr('disabled', false);
            }
        })
        .change();

    // option use js
    $('input#use_js')
        .change(function () {
            var $i = $('input#load_in_footer');

            if ($(this).attr('checked')) {
                $i.attr('disabled', false);
            } else {
                $i.attr('disabled', true)
                    .attr('checked', false);
            }
        })
        .change();

    // option filter_excl_sel
    $('input#phpquery')
        .change(function () {
            if ($(this).attr('checked')) {
                $('.filter_excl_sel').fadeIn();
            } else {
                $('.filter_excl_sel').fadeOut();
            }
        })
        .change();

    // refresh page when updated menu position
    $('#menu_position').parents('form.ajax-form').on('ajax_saved_options', function () {
        var s = $(this).val() || '';
        window.location.href = s + (s.indexOf('?') > -1 ? '&' : '?') + 'page=wp_external_links&settings-updated=true';
    });

    // set tooltips
    $('.tooltip-help').css('margin', '0 5px').tipsy({ fade: true, live: true, gravity: 'w', fallback: 'No help text.' });

    // remove class to fix button background
    $('*[type="submit"]').removeClass('submit');

    // slide postbox
    $('.postbox').find('.handlediv, .hndle').click(function () {
        var $inside = $(this).parent().find('.inside');

        if ($inside.css('display') === 'block') {
            $inside.css({ display: 'none' });
        } else {
            $inside.css({ display: 'block' });
        }
    });

});