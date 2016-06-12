<?php
/**
 * Class WPEL_Link_Fields_Base
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
abstract class WPEL_Link_Fields_Base extends FWP_Settings_Section_Fields_1x0x0
{

    /**
     * Get general fields
     * @return array
     */
    final protected function get_general_fields()
    {
        return array(
            'apply_settings' => array(
                'label'             => __( 'Settings for links:', 'wp-external-links' ),
                'class'             => 'js-apply-settings',
            ),
            'target' => array(
                'label'             => __( 'Open links:', 'wp-external-links' ),
                'class'             => 'wpel-hidden',
                'default_value'     => '',
            ),
            'target_overwrite' => array(
                'label'             => '',
                'class'             => 'wpel-no-label wpel-hidden',
            ),
            'rel_follow' => array(
                'label'             => __( 'Set <code>follow</code> or <code>nofollow</code>:', 'wp-external-links' ),
                'class'             => 'wpel-hidden',
                'default_value'     => '',
            ),
            'rel_follow_overwrite' => array(
                'label'             => '',
                'class'             => 'wpel-no-label wpel-hidden',
            ),
            'rel_noopener' => array(
                'label'             => __( 'Also add to <code>rel</code> attribute:', 'wp-external-links' ),
                'class'             => 'wpel-hidden',
                'default_value'     => '1',
            ),
            'rel_noreferrer' => array(
                'label'             => '',
                'class'             => 'wpel-no-label wpel-hidden',
                'default_value'     => '1',
            ),
            'title' => array(
                'label'             => __( 'Set <code>title</code>:', 'wp-external-links' ),
                'class'             => 'wpel-hidden',
                'default_value'     => '{title}',
            ),
            'class' => array(
                'label'             => __( 'Add CSS class(es):', 'wp-external-links' ),
                'class'             => 'wpel-hidden',
            ),
            'icon_type' => array(
                'label'             => __( 'Choose icon type:', 'wp-external-links' ),
                'class'             => 'js-icon-type wpel-hidden',
            ),
            'icon_image' => array(
                'label'             => __( 'Choose icon image:', 'wp-external-links' ),
                'class'             => 'js-icon-type-child js-icon-type-image wpel-hidden',
                'default_value'     => '1',
            ),
            'icon_dashicon' => array(
                'label'             => __( 'Choose dashicon:', 'wp-external-links' ),
                'class'             => 'js-icon-type-child js-icon-type-dashicon wpel-hidden',
            ),
            'icon_fontawesome' => array(
                'label'             => __( 'Choose FA icon:', 'wp-external-links' ),
                'class'             => 'js-icon-type-child js-icon-type-fontawesome wpel-hidden',
            ),
            'icon_position' => array(
                'label'             => __( 'Icon position:', 'wp-external-links' ),
                'class'             => 'js-icon-type-depend wpel-hidden',
                'default_value'     => 'right',
            ),
            'no_icon_for_img' => array(
                'label'             => __( 'Skip icon with <code>&lt;img&gt;</code>:', 'wp-external-links' ),
                'class'             => 'js-icon-type-depend wpel-hidden',
                'default_value'     => '1',
            ),
        );

        parent::init();
    }

    /**
     * Show field methods
     */

    protected function show_apply_settings( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Apply these settings', 'wp-external-links' )
            , '1'
            , ''
        );
    }

    protected function show_target( array $args )
    {
        $this->get_html_fields()->select(
            $args[ 'key' ]
            , array(
                ''          => __( '- keep as is -', 'wp-external-links' ),
                '_self'     => __( 'in the same window, tab or frame', 'wp-external-links' ),
                '_blank'    => __( 'each in a separate new window or tab', 'wp-external-links' ),
                '_new'      => __( 'all in the same new window or tab', 'wp-external-links' ),
                '_top'      => __( 'in the topmost frame', 'wp-external-links' ),
            )
        );
    }

    protected function show_target_overwrite( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Overwrite existing values.', 'wp-external-links' )
            , '1'
            , ''
        );
    }

    protected function show_rel_follow( array $args )
    {
        $this->get_html_fields()->select(
            $args[ 'key' ]
            , array(
                ''          => __( '- keep as is -', 'wp-external-links' ),
                'follow'    => __( 'follow', 'wp-external-links' ),
                'nofollow'  => __( 'nofollow', 'wp-external-links' ),
            )
        );
    }

    protected function show_rel_follow_overwrite( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Overwrite existing values.', 'wp-external-links' )
            , '1'
            , ''
        );
    }

    protected function show_rel_noopener( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Add <code>"noopener"</code>', 'wp-external-links' )
            , '1'
            , ''
        );
    }

    protected function show_rel_noreferrer( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Add <code>"noreferrer"</code>', 'wp-external-links' )
            , '1'
            , ''
        );
    }

    protected function show_title( array $args )
    {
        $this->get_html_fields()->text( $args[ 'key' ], array(
            'class' => 'regular-text',
        ) );

        echo '<p class="description">'
                . __( 'Use this <code>{title}</code> for the original title value '
                .'and <code>{text}</code> for the link text as shown on the page', 'wp-external-links' )
                .'</p>';
    }

    protected function show_class( array $args )
    {
        $this->get_html_fields()->text( $args[ 'key' ], array(
            'class' => 'regular-text',
        ) );
    }

    protected function show_icon_type( array $args )
    {
        $this->get_html_fields()->select(
            $args[ 'key' ]
            , array(
                ''              => __( '- no icon -', 'wp-external-links' ),
                'image'         => __( 'Image', 'wp-external-links' ),
                'dashicon'      => __( 'Dashicon', 'wp-external-links' ),
                'fontawesome'   => __( 'Font Awesome', 'wp-external-links' ),
            )
        );
    }

    protected function show_icon_image( array $args )
    {
        echo '<fieldset>';
        echo '<div class="wpel-icon-type-image-column">';

        for ( $x = 1; $x <= 20; $x++ ) {
            echo '<label>';
            echo $this->get_html_fields()->radio( $args[ 'key' ], strval( $x ) );
            echo '<img src="'. plugins_url( '/public/images/wpel-icons/icon-'. esc_attr( $x ) .'.png', WPEL_Plugin::get_plugin_file() ) .'">';
            echo '</label>';
            echo '<br>';

            if ( $x % 5 === 0 ) {
                echo '</div>';
				echo '<div class="wpel-icon-type-image-column">';
            }
        }

        echo '</div>';
        echo '</fieldset>';
    }

    protected function show_icon_dashicon( array $args )
    {
        $dashicons_str = file_get_contents( WPEL_Plugin::get_plugin_dir( '/data/json/dashicons.json' ) );
        $dashicons_json = json_decode( $dashicons_str, true );
        $dashicons = $dashicons_json[ 'icons' ];

        $options = array();
        foreach ( $dashicons as $icon ) {
            $options[ $icon[ 'className' ] ] = '&#x'. $icon[ 'unicode' ];
        }

        $this->get_html_fields()->select( $args[ 'key' ], $options, array(
            'style' => 'font-family:dashicons',
        ) );
    }

    protected function show_icon_fontawesome( array $args )
    {
        $fa_icons_str = file_get_contents( WPEL_Plugin::get_plugin_dir( '/data/json/fontawesome.json' ) );
        $fa_icons_json = json_decode( $fa_icons_str, true );
        $fa_icons = $fa_icons_json[ 'icons' ];

        $options = array();
        foreach ( $fa_icons as $icon ) {
            $options[ $icon[ 'className' ] ] = '&#x'. $icon[ 'unicode' ];
        }

        $this->get_html_fields()->select( $args[ 'key' ], $options, array(
            'style' => 'font-family:FontAwesome',
        ) );
    }

    protected function show_icon_position( array $args )
    {
        $this->get_html_fields()->select(
            $args[ 'key' ]
            , array(
                'left'  => __( 'Left side of the link', 'wp-external-links' ),
                'right' => __( 'Right side of the link', 'wp-external-links' ),
            )
        );
    }

    protected function show_no_icon_for_img( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'No icon for links already containing an <code>&lt;img&gt;</code>-tag.', 'wp-external-links' )
            , '1'
            , ''
        );
    }

    /**
     * Validate and sanitize user input before saving to databse
     * @param array $new_values
     * @param array $old_values
     * @return array
     */
    protected function before_update( array $new_values, array $old_values )
    {
        $update_values = $new_values;
        $is_valid = true;

        $is_valid = $is_valid && in_array( $new_values[ 'apply_settings' ], array( '', '1' ) );
        $is_valid = $is_valid && in_array( $new_values[ 'target_overwrite' ], array( '', '1' ) );
        $is_valid = $is_valid && in_array( $new_values[ 'rel_follow_overwrite' ], array( '', '1' ) );
        $is_valid = $is_valid && in_array( $new_values[ 'rel_noopener' ], array( '', '1' ) );
        $is_valid = $is_valid && in_array( $new_values[ 'rel_noreferrer' ], array( '', '1' ) );
        $is_valid = $is_valid && in_array( $new_values[ 'no_icon_for_img' ], array( '', '1' ) );

        if ( false === $is_valid ) {
            // error when user input is not valid conform the UI, probably tried to "hack"
            $this->add_error( __( 'Something went wrong. One or more values were invalid.', 'wp-external-links' ) );
            return $old_values;
        }

        $update_values[ 'target' ]          = sanitize_text_field( $new_values[ 'target' ] );
        $update_values[ 'rel_follow' ]      = sanitize_text_field( $new_values[ 'rel_follow' ] );
        $update_values[ 'title' ]           = sanitize_text_field( $new_values[ 'title' ] );
        $update_values[ 'class' ]           = sanitize_text_field( $new_values[ 'class' ] );
        $update_values[ 'icon_type' ]       = sanitize_text_field( $new_values[ 'icon_type' ] );
        $update_values[ 'icon_image' ]      = sanitize_text_field( $new_values[ 'icon_image' ] );
        $update_values[ 'icon_dashicon' ]   = sanitize_text_field( $new_values[ 'icon_dashicon' ] );
        $update_values[ 'icon_fontawesome' ] = sanitize_text_field( $new_values[ 'icon_fontawesome' ] );
        $update_values[ 'icon_position' ]   = sanitize_text_field( $new_values[ 'icon_position' ] );

        return $update_values;
    }

}

/*?>*/
