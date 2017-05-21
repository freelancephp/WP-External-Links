/**
 * WP External Links Plugin
 * Admin
 */
/*global jQuery, window*/
jQuery(function ($) {
    'use strict';
    
    // add custom jQuery show/hide function
    $.extend($.fn, {
        wpelShow: function () {
            var self = this;
            this.stop({ clearQueue: true, jumpToEnd: true });
            this.fadeIn({ duration: 500, queue: false, complete: function () {
                self.removeClass('wpel-hidden'); 
            }});
        },
        wpelHide: function () {
            var self = this;
            this.stop({ clearQueue: true, jumpToEnd: true });
            this.fadeOut({ duration: 500, queue: false, complete: function () { 
                self.addClass('wpel-hidden'); 
            }});
        }
    });

    var $wrapper = $('.wpel-settings-page');

    /**
     * Apply Sections Settings
     */
    $wrapper.on('change', '.js-wpel-apply input', function () {
        var applyAll = $(this).is(':checked');
        var $items = $wrapper.find('.js-wpel-apply-child');

        if (applyAll) {
            $items.wpelHide();
        } else {
            $items.wpelShow();
        }
    });

    // trigger immediatly
    $wrapper.find('.js-wpel-apply input[type="checkbox"]').change();

    /**
     * Link Settings
     */
    $wrapper.on('change', '.js-icon-type select', function () {
        var iconType = $(this).val();
        var $itemsChild = $wrapper.find('.js-icon-type-child');
        var $itemsDepend = $wrapper.find('.js-icon-type-depend');

        $itemsChild.hide();

        if (iconType === 'image') {
            $itemsDepend.wpelShow();
            $itemsChild.filter('.js-icon-type-image').wpelShow();
        } else if (iconType === 'dashicon') {
            $itemsDepend.wpelShow();
            $itemsChild.filter('.js-icon-type-dashicon').wpelShow();
        } else if (iconType === 'fontawesome') {
            $itemsDepend.wpelShow();
            $itemsChild.filter('.js-icon-type-fontawesome').wpelShow();
        } else {
            $itemsDepend.wpelHide();
        }
    });

    $wrapper.on('change', '.js-apply-settings input[type="checkbox"]', function () {
        var $items = $wrapper.find('.form-table tr').not('.js-apply-settings');

        if ($(this).prop('checked')) {
            $items.wpelShow();
            $wrapper.find('.js-icon-type select').change();
        } else {
            $items.wpelHide();
        }
    });

    // trigger immediatly
    $wrapper.find('.js-apply-settings input[type="checkbox"]').change();


    /**
     * Support
     * Copy to clipboard
     */
    $wrapper.on('click', '.js-wpel-copy', function (e) {
        e.preventDefault();

        var node = $wrapper.find('.js-wpel-copy-target').get(0);
        node.select();

        var range = document.createRange();
        range.selectNode(node);
        window.getSelection().addRange(range);

        try {
            document.execCommand('copy');
        } catch(err) {
            console.log('Unable to copy');
        }
    });

    /**
     * Help documentation links/buttons
     */
    $wrapper.on('click', '[data-wpel-help]', function () {
        var helpKey = $(this).data('wpel-help');

        if (helpKey) {
            // activate given tab
            $('#tab-link-'+ helpKey +' a').click();
        } else {
            // activate first tab
            $('.contextual-help-tabs li a').first().click();
        }

        $('#contextual-help-link[aria-expanded="false"]').click();
    });

    // show current tab
    $wrapper.find('form').wpelShow();
    // for network pages
    $('.wpel-network-page').find('form').wpelShow();
    
});
