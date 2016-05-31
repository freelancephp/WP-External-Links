<?php
/**
 * Class WPEL_Link_Fields_Base
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
abstract class WPEL_Link_Fields_Base extends WP_Settings_Section_Fields_0x7x0
{

    /**
     * Get general fields
     * @param string $option_name
     * @return array
     */
    final protected function get_general_fields( $option_name )
    {
        return array(
            'apply_settings' => array(
                'label'             => __( 'Settings for links:', 'wpel' ),
                'class'             => 'js-apply-settings',
            ),
            'target' => array(
                'label'             => __( 'Open links:', 'wpel' ),
                'class'             => 'wpel-hidden',
                'default_value'     => '_blank',
            ),
            'target_overwrite' => array(
                'label'             => '',
                'class'             => 'wpel-no-label wpel-hidden',
            ),
            'rel_follow' => array(
                'label'             => __( 'Set <code>follow</code> or <code>nofollow</code>:', 'wpel' ),
                'class'             => 'wpel-hidden',
                'default_value'     => 'nofollow',
            ),
            'rel_follow_overwrite' => array(
                'label'             => '',
                'class'             => 'wpel-no-label wpel-hidden',
            ),
            'rel_external' => array(
                'label'             => __( 'Also add to <code>rel</code> attribute:', 'wpel' ),
                'class'             => 'wpel-hidden',
                'default_value'     => '1',
            ),
            'rel_noopener' => array(
                'label'             => '',
                'class'             => 'wpel-no-label wpel-hidden',
                'default_value'     => '1',
            ),
            'rel_noreferrer' => array(
                'label'             => '',
                'class'             => 'wpel-no-label wpel-hidden',
                'default_value'     => '1',
            ),
            'title' => array(
                'label'             => __( 'Set <code>title</code>:', 'wpel' ),
                'class'             => 'wpel-hidden',
                'default_value'     => '{title}',
            ),
            'class' => array(
                'label'             => __( 'Add CSS class(es):', 'wpel' ),
                'class'             => 'wpel-hidden',
            ),
            'icon_type' => array(
                'label'             => __( 'Choose icon type:', 'wpel' ),
                'class'             => 'js-icon-type wpel-hidden',
            ),
            'icon_image' => array(
                'label'             => __( 'Choose icon image:', 'wpel' ),
                'class'             => 'js-icon-type-child js-icon-type-image wpel-hidden',
            ),
            'icon_dashicon' => array(
                'label'             => __( 'Choose dashicon:', 'wpel' ),
                'class'             => 'js-icon-type-child js-icon-type-dashicon wpel-hidden',
            ),
            'icon_fontawesome' => array(
                'label'             => __( 'Choose FA icon:', 'wpel' ),
                'class'             => 'js-icon-type-child js-icon-type-fontawesome wpel-hidden',
            ),
            'icon_position' => array(
                'label'             => __( 'Icon position:', 'wpel' ),
                'class'             => 'js-icon-type-depend wpel-hidden',
                'default_value'     => 'right',
            ),
            'no_icon_for_img' => array(
                'label'             => __( 'Skip icon with <code>&lt;img&gt;</code>:', 'wpel' ),
                'class'             => 'js-icon-type-depend wpel-hidden',
                'default_value'     => '1',
            ),
        );
    }

    /**
     * Show field methods
     */

    protected function show_apply_settings( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Apply these settings', 'wpel' )
            , '1'
            , ''
        );
    }

    protected function show_target( array $args )
    {
        $this->get_html_fields()->select(
            $args[ 'key' ]
            , array(
                ''          => __( '- keep as is -', 'wpel' ),
                '_self'     => __( 'in the same window, tab or frame', 'wpel' ),
                '_blank'    => __( 'each in a separate new window or tab', 'wpel' ),
                '_new'      => __( 'all in the same new window or tab', 'wpel' ),
                '_top'      => __( 'in the topmost frame', 'wpel' ),
            )
        );
    }

    protected function show_target_overwrite( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Overwrite existing values.', 'wpel' )
            , '1'
            , ''
        );
    }

    protected function show_rel_follow( array $args )
    {
        $this->get_html_fields()->select(
            $args[ 'key' ]
            , array(
                ''          => __( '- keep as is -', 'wpel' ),
                'follow'    => __( 'follow', 'wpel' ),
                'nofollow'  => __( 'nofollow', 'wpel' ),
            )
        );
    }

    protected function show_rel_follow_overwrite( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Overwrite existing values.', 'wpel' )
            , '1'
            , ''
        );
    }

    protected function show_rel_external( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Add <code>"external"</code>', 'wpel' )
            , '1'
            , ''
        );
    }

    protected function show_rel_noopener( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Add <code>"noopener"</code>', 'wpel' )
            , '1'
            , ''
        );
    }

    protected function show_rel_noreferrer( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Add <code>"noreferrer"</code>', 'wpel' )
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
                .'and <code>{text}</code> for the link text as shown on the page'
                .'<br>Leave empty to unchange the title attributes', 'wpel' )
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
                ''              => __( '- no icon -', 'wpel' ),
                'image'         => __( 'Image', 'wpel' ),
                'dashicon'      => __( 'Dashicon', 'wpel' ),
                'fontawesome'   => __( 'Font Awesome', 'wpel' ),
            )
        );
    }

    protected function show_icon_image( array $args )
    {
        echo '<fieldset>';
        echo '<div class="wpel-icon-type-image-column">';

        for ( $x = 1; $x <= 20; $x++ ) {
            echo '<label>';
            echo $this->get_html_fields()->radio( 'image_icon', strval( $x ) );
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
        $dashicons_str = file_get_contents( WPEL_Plugin::get_plugin_dir( '/public/data/json/dashicons.json' ) );
        $dashicons_json = json_decode( $dashicons_str, true );
        $dashicons = $dashicons_json[ 'icons' ];

        $options = array();
        foreach ( $dashicons as $icon ) {
            $options[ $icon[ 'className' ] ] = '&#x'. $icon[ 'unicode' ];
        }

        $this->get_html_fields()->select( 'dashicon', $options, array(
            'style' => 'font-family:dashicons',
        ) );
    }

    protected function show_icon_fontawesome( array $args )
    {
        $fa_icons_str = file_get_contents( WPEL_Plugin::get_plugin_dir( '/public/data/json/fontawesome.json' ) );
        $fa_icons_json = json_decode( $fa_icons_str, true );
        $fa_icons = $fa_icons_json[ 'icons' ];

        $options = array();
        foreach ( $fa_icons as $icon ) {
            $options[ $icon[ 'className' ] ] = '&#x'. $icon[ 'unicode' ];
        }

        $this->get_html_fields()->select( 'fontawesome', $options, array(
            'style' => 'font-family:FontAwesome',
        ) );
    }

    protected function show_icon_position( array $args )
    {
        $this->get_html_fields()->select(
            $args[ 'key' ]
            , array(
                'left'  => __( 'Left side of the link', 'wpel' ),
                'right' => __( 'Right side of the link', 'wpel' ),
            )
        );
    }

    protected function show_no_icon_for_img( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'No icon for links already containing an <code>&lt;img&gt;</code>-tag.', 'wpel' )
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


        return $update_values;
    }

}

/*?>*/
