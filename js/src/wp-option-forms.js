/* WP Options Form */
/*global jQuery, ajaxurl*/
jQuery(function ($) {
    'use strict';

    // save function
    var saveAjaxForm = function (target) {
        var $this = $(target),
            $form = $this.parents('form'),
            // get ajax post values
            vals = $form.serializeArray();

        // disable button
        $this.attr('disabled', true);

        // show ajax loader
        $form.find('.ajax-feedback').css('visibility', 'visible');

        // save option values
        $.post(ajaxurl, vals, function (result) {
            var $msg = $('<strong>').insertBefore($this);

            if (result === '1') {
                $msg.html('Saved');
            } else {
                // save options, non-ajax fallback
                $form.find('[name="action"]').val('update');
                // normal submit
                $form.submit();
            }

            $msg.css({ margin: '0 5px' })
                .delay(1000)
                .fadeOut(function () {
                    $(this).remove();
                });

            // enable button
            $this.attr('disabled', false);

            // hide ajax loader
            $form.find('.ajax-feedback').css('visibility', 'hidden');

            // trigger ajax_saved_options
            $form.trigger('ajax_saved_options', [result]);
        });
    };

    // add ajax post
    $('form.ajax-form input[type="submit"]').click(function (e) {
        saveAjaxForm(this);
        e.preventDefault();
    });

});
