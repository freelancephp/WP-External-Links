<?php
/**
 * Class WPEL_Network_Admin_Fields
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Network_Admin_Fields extends FWP_Settings_Section_Fields_1x0x0
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
            'title'             => __( 'Network Admin Settings', 'wp-external-links' ),
            'fields'            => array(
                'own_admin_menu' => array(
                    'label'         => __( 'Main Network Admin Menu:', 'wp-external-links' ),
                    'default_value' => '1',
                ),
            ),
        ) );

        if ( is_network_admin() ) {
            add_action( 'network_admin_edit_'. $this->get_setting( 'option_group' ) , $this->get_callback( 'save_network_settings' ) );
        }

        parent::init();
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

        $redirect_url = filter_input( INPUT_POST, '_wp_http_referer', FILTER_SANITIZE_STRING );

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
            , __( 'Create own network admin menu for this plugin', 'wp-external-links' )
            , '1'
            , ''
        );

        echo ' <p class="description">'
                . __( 'Or else it will be added to the "Settings" menu', 'wp-external-links' )
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
        $is_valid = true;

        $is_valid = $is_valid && in_array( $new_values[ 'own_admin_menu' ], array( '', '1' ) );

        if ( false === $is_valid ) {
            // error when user input is not valid conform the UI, probably tried to "hack"
            $this->add_error( __( 'Something went wrong. One or more values were invalid.', 'wp-external-links' ) );
            return $old_values;
        }

        return $update_values;
    }

}

/*?>*/
