/* WP External Links */
/*global jQuery, window*/
(function (undefined) {
    'use strict';

    var $ = jQuery === undefined ? null : jQuery;

    // add event handler
    function addEvt(el, evt, fn) {
        if (el.attachEvent) {
            // IE method
            el.attachEvent('on' + evt, fn);
        } else if (el.addEventListener) {
            // Standard JS method
            el.addEventListener(evt, fn, false);
        }
    }

    // open external link
    function openExtLink(a, evt) {
        var target = a.getAttribute('data-wpel-target');
        var href = a.getAttribute('href');
        var win;

        if (href && target) {
            // open link in a new window
            win = window.open(href, target);
            win.focus();

            // prevent default event action
            if (evt) {
                if (evt.preventDefault !== undefined) {
                    evt.preventDefault();
                } else if (evt.returnValue !== undefined) {
                    evt.returnValue = false;
                }
            }
        }
    }

    if ($) {
        // jQuery DOMready method
        $(function () {
            $('a').live('click', function (evt) {
                openExtLink(this, evt);
            });
        });
    } else {
        // use onload when jQuery not available
        addEvt(window, 'load', function () {
            var links = window.document.getElementsByTagName('a');
            var eventClick = function (evt) {
                var target = this instanceof Element ? this : evt.target;
                openExtLink(target, evt);
            };
            var a;
            var i;

            // check each <a> element
            for (i = 0; i < links.length; i += 1) {
                a = links[i];

                // click event for opening in a new window
                addEvt(a, 'click', eventClick);
            }
        });
    }

}());
