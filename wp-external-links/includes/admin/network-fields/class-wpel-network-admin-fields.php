<?php
/**
 * Class WPEL_Network_Admin_Fields
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Network_Admin_Fields extends WP_Settings_Section_Fields_0x7x0
{

    /**
     * Initialize
     */
    protected function init()
    {
        $this->set_settings( array(
            'section_id'        => 'wpel-network-admin-fields',
            'page_id'           => 'wpel-network-admin-fields',
            'option_name'       => 'wpel-network-admin-settings',
            'option_group'      => 'wpel-network-admin-settings',
            'network_site'      => true,
            'title'             => __( 'Network Admin Settings', 'wpel' ),
            'description'       => __( 'Lorem ipsum...', 'wpel' ),
            'fields'            => array(
                'own_admin_menu' => array(
                    'label'         => __( 'Main Network Admin Menu:', 'wpel' ),
                    'default_value' => '1',
                ),
            ),
        ) );

        if ( $this->get_setting( 'network_site' ) ) {
            add_action( 'network_admin_edit_'. $this->get_setting( 'option_group' ) , $this->get_callback( 'save_network_settings' ) );
        }
    }

    protected function save_network_settings()
    {
        // when calling 'settings_fields' but we must add the '-options' postfix
        check_admin_referer( $this->get_setting( 'option_group' ) .'-options' );

        global $new_whitelist_options;
        $option_names = $new_whitelist_options[ $this->get_setting( 'option_group' ) ];

        foreach ( $option_names as $option_name ) {
            if ( isset( $_POST[ $option_name ] ) ) {
                $post_values = $_POST[ $option_name ];
                $sanitized_values = $this->sanitize( $post_values );

                update_site_option( $option_name, $sanitized_values );
            } else {
                delete_site_option( $option_name );
            }
        }

        $redirect_url = filter_input( INPUT_POST, '_wp_http_referer', FILTER_SANITIZE_STRING );;

        wp_redirect( add_query_arg(
            array(
                'page' => 'wpel-network-settings-page',
                'updated' => true
            )
            , $redirect_url
        ) );

        exit;
    }

    /**
     * Show field methods
     */

    protected function show_own_admin_menu( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Create own network admin menu for this plugin', 'wpel' )
            , '1'
            , ''
        );

        echo ' <p class="description">'
                . __( 'Or else it will be added to the "Settings" menu', 'wpel' )
                .'</p>';
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

        $is_valid_check = function ( $value ) {
            $valid_vals = array( '', '1' );
            return in_array( $value, $valid_vals );
        };

        if ( ! $is_valid_check( $new_values[ 'own_admin_menu' ] ) ) {
            $update_values = $old_values;
            $this->add_error( __( 'Wrong values!', 'wpel' ) );
        }

        return $update_values;
    }

}

/*?>*/