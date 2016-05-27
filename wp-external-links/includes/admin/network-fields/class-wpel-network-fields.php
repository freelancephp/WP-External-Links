<?php
/**
 * Class WPEL_Network_Fields
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Network_Fields extends WP_Settings_Section_Fields_0x7x0
{

    /**
     * Initialize
     */
    protected function init()
    {
        $this->set_settings( array(
            'section_id'        => 'wpel-network-fields',
            'page_id'           => 'wpel-network-fields',
            'option_name'       => 'wpel-network-settings',
            'option_group'      => 'wpel-network-settings',
            'network_site'      => true,
            'title'             => __( 'Multi Site Settings', 'wpel' ),
            'description'       => __( 'Lorem ipsum...', 'wpel' ),
            'fields'            => array(
                'capability' => array(
                    'label'         => __( 'Capability for individual sites:', 'wpel' ),
                    'default_value' => 'manage_options',
                ),
                'default_settings_site' => array(
                    'label'         => __( 'Use the settings of this site as default for new sites:', 'wpel' ),
                    'default_value' => '',
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
                'page' => $this->get_setting( 'option_group' ) .'-page',
                'updated' => true
            )
            , $redirect_url
        ) );

        exit;
    }

    /**
     * Show field methods
     */

    protected function show_capability( array $args )
    {
        $this->get_html_fields()->select(
            $args[ 'key' ]
            , array(
                'manage_options'    => __( 'Site Admins and Super Admins', 'wpel' ),
                'manage_network'    => __( 'Only Super Admins', 'wpel' ),
            )
        );
    }

    protected function show_default_settings_site( array $args )
    {
        $sites = wp_get_sites();

        $values = array();
        $values[ '' ] = __( '- none -', 'wpel' );

        foreach ( $sites as $site ) {
            $values[ $site[ 'blog_id' ] ] = 'blog_id: '. $site[ 'blog_id' ] .' - '. $site[ 'path' ];
        }

        $this->get_html_fields()->select(
            $args[ 'key' ]
            , $values
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
