<?php
/**
 * Class WPEL_Icon_Fields
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Icon_Fields extends WP_Settings_Section_Fields_0x7x0
{

    /**
     * Initialize
     */
    protected function init()
    {
        $this->set_settings( array(
            'section_id'        => 'wpel-icon-fields',
            'page_id'           => 'wpel-icon-fields',
            'option_name'       => 'wpel-icon-settings',
            'option_group'      => 'wpel-icon-settings',
            'title'             => __( 'Icon Settings', 'wpel' ),
            'description'       => __( 'Lorem ipsum...', 'wpel' ),
            'fields'            => array(
                'add_icon' => array(
                    'label'             => __( 'Add icon to external links:', 'wpel' ),
                    'label_for'         => 'wpel-link-settings-add_icon',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'icon_type' => array(
                    'label'             => __( 'Choose link icon:', 'wpel' ),
                    'label_for'         => 'wpel-icon-settings-icon_type',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'icon_images' => array(
                    'class'             => 'hide-all',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'icon_dashicons' => array(
                    'class'             => 'hide-all',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'icon_fontawesome' => array(
                    'class'             => 'hide-all',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'icon_left_side' => array(
                    'label'             => __( 'Show icon left:', 'wpel' ),
                    'label_for'         => 'wpel-icon-settings-icon_left_side',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'no_icon_for_img' => array(
                    'label'             => __( 'Skip icon containing <code>&lt;img&gt;</code>:', 'wpel' ),
                    'label_for'         => 'wpel-icon-settings-no_icon_for_img',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'no_icon_class' => array(
                    'label'             => __( 'No icon class:', 'wpel' ),
                    'label_for'         => 'wpel-icon-settings-no_icon_class',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
            ),
        ) );
    }

    /**
     * Show field
     * @param array $args
     */
    protected function show_field( array $args )
    {
        $html_fields = new WP_HTML_Fields_0x7x0(
            $args[ 'values' ]
            , 'wpel-icon-settings-%s'
            , 'wpel-icon-settings[%s]'
        );

        switch ( $args[ 'key' ] ) {
            case 'add_icon':
                echo '<label>';
                $html_fields->check( $args[ 'key' ], '' );
                echo __( ' Add icon to all external links.', 'wpel' );
                echo '</label>';
            break;

            case 'icon_type':
                $html_fields->select(
                    $args[ 'key' ]
                    , null
                    , array(
                        'dashicons'     => __( 'Dashicons', 'wpel' ),
                        'fontawesome'   => __( 'Font Awesome', 'wpel' ),
                        'image'         => __( 'Image icon', 'wpel' ),
                    )
                );

                // icon image
                echo '<div class="wrap-icon-images inside">';
                echo '<div style="width:12%;float:left">';
                for ( $x = 1; $x <= 20; $x++ ) {
                    echo '<label>';
                    $html_fields->radio( 'icon_images', $x );
                    echo '<img src="'. plugins_url( '/public/images/ext-icons/ext-icon-'. $x .'.png', WPEL_Base::get_plugin_file() ) .'">';
                    echo '</label>';
                    echo '<br>';

                    if ($x % 5 == 0) {
                        echo '</div><div style="width:12%;float:left">';
                    }
                }
                echo '</div>';
                echo '<br class="clear">';
                echo '</div>';

                // dashicons
                echo '<div class="wrap-icon-dashicons inside" style="font-family:dashicons">';
                echo '<label>';
                $html_fields->select( 'dashicons', null, array(), 'select-dashicons' );
                echo '</label>';
                echo '</div>';

                // font awesome
                echo '<div class="wrap-icon-fontawesome inside" style="font-family:FontAwesome">';
                echo '<label>';
                $html_fields->select( 'fontawesome', null, array(), 'select-fontawesome' );
                echo '</label>';
                echo '</div>';
            break;

            case 'icon_left_side':
                echo '<label>';
                $html_fields->check( $args[ 'key' ], '', '' );
                echo __( ' Show icon in front of the text. '
                        . '<span class="description">(on the left side)</span>', 'wpel' );
                echo '</label>';
            break;

            case 'no_icon_for_img':
                echo '<label>';
                $html_fields->check( $args[ 'key' ], '', '' );
                echo __( ' No icon for links already containing an <code>&lt;img&gt;</code>-tag.', 'wpel' );
                echo '</label>';
            break;

            case 'no_icon_class':
                $html_fields->text( $args[ 'key' ] );
            break;

        }
    }

    /**
     * Validate and sanitize user input before saving to databse
     * @param array $new_values
     * @param array $old_values
     * @return array
     */
    protected function update( array $new_values, array $old_values )
    {
        $values = $new_values;
        
        if ( '' === trim ( $new_values[ 'some-key' ] ) ) {
            $values = $old_values;
            $this->add_error( __( 'Some key is required.', 'wprun-demo' ) );
        }

        return $values;
    }

}

/*?>*/
