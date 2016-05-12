/* WP External Links - Admin */
/*global jQuery, window*/
jQuery(function ($) {
    'use strict';

    $( '#wpbody form' )
        .on( 'change', '[name="wpel-link-settings[use_settings_external_links]"]', function () {
            hide_other_fields( this, $( '.nav-tab' ).eq( 1 ) );
        })
        .on( 'change', '[name="wpel-icon-settings[add_icon]"]', function () {
            hide_other_fields( this );
        })
        .on( 'change', '[name="wpel-internal-link-settings[use_settings_interal_links]"]', function () {
            hide_other_fields( this );
        });

    var hide_other_fields = function ( el, $other ) {
        var $el = $( el );
        var $form = $el.closest( 'form' );
        var $other = $other || $();

        if ( $el.prop( 'checked' ) ) {
            $form.find( 'input, select, textarea' ).not( el ).closest( 'tr' ).fadeIn();
            $other.fadeIn();
        } else {
            $form.find( 'input, select, textarea' ).not( el ).closest( 'tr' ).fadeOut();
            $other.fadeOut();
        }
    };


    // fill dashicons  select options
    $.get(wpelSettings.pluginUrl + '/public/data/json/fontawesome.json', null, function (data) {
        var $select = $('.select-fontawesome');

        // create select options
        fillSelect($select, data.icons, 'unicode', 'className');

        // select saved value
        $select.find('option').each(function () {
            if (this.value === wpelSettings.fontawesomeValue) {
                $(this).prop('selected', true);
            }
        });
    });

    // fill fontawesome select options
    $.get(wpelSettings.pluginUrl + '/public/data/json/dashicons.json', null, function (data) {
        var $select = $('.select-dashicons');

        // create select options
        fillSelect($select, data.icons, 'unicode', 'className');

        // select saved value
        $select.find('option').each(function () {
            if (this.value === wpelSettings.dashiconsValue) {
                $(this).prop('selected', true);
            }
        });
    });

    // fill select helper function
    function fillSelect($select, list, keyText, keyValue) {
        $.each(list, function (index, item) {
            var value = item[keyValue];
            var text = item[keyText];

            $select.append('<option value="'+ value +'">&#x'+ text +'</option>');
        });
    }

    // mail icon
    $('body').on('change', '*[name="wpel-icon-settings[icon_type]"]', function () {
        var value = $(this).val();
        var $images = $('.wrap-icon-images');
        var $selectDashicons = $('.wrap-icon-dashicons');
        var $selectFontAwesome = $('.wrap-icon-fontawesome');

        $images.hide();
        $selectDashicons.hide();
        $selectFontAwesome.hide();

//        var $form = $(this).closest( 'form' );
//         if ( value ) {
//            $form.find( 'input, select, textarea' ).not( this ).closest( 'tr' ).fadeIn();
//        } else {
//            $form.find( 'input, select, textarea' ).not( this ).closest( 'tr' ).fadeOut();
//        }

        if (value === 'image') {
            $images.fadeIn();
        } else if (value === 'dashicons') {
            $selectDashicons.fadeIn();
        } else if (value === 'fontawesome') {
            $selectFontAwesome.fadeIn();
        }
    });
    // trigger immediatly
    $('*[name="wpel-icon-settings[icon_type]"]').change();
});
