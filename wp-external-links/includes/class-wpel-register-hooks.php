<?php
/**
 * Class WPEL_Register_Hooks
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Register_Hooks extends WPRun_Base_1x0x0
{

    /**
     * Initialize
     */
    protected function init()
    {
        register_activation_hook(
            WPEL_Plugin::get_plugin_file()
            , $this->get_callback( 'activate' )
        );

        register_uninstall_hook(
            WPEL_Plugin::get_plugin_file()
            , $this->get_callback( 'uninstall' )
        );
    }

    /**
     * Plugin activation procedure
     */
    protected function activate( $networkwide )
    {
        global $wpdb;

        if ( is_multisite() && $networkwide ) {
            // network activation
            $sites = wp_get_sites();
            $active_blog = $wpdb->blogid;

            foreach ( $sites as $site ) {
                switch_to_blog( $site[ 'blog_id' ] );
                $this->activate_site();
            }

            // switch back to active blog
            switch_to_blog( $active_blog );

            $this->activate_network();
        } else {
            // single site activation
            $this->activate_site();
        }
    }

    /**
     * Activate network
     * @return void
     */
    private function activate_network()
    {
        $network_already_set = get_site_option( 'wpel-network-settings' );

        if ( $network_already_set ) {
            return;
        }

        // network default settings
        $network_values = WPEL_Network_Fields::get_instance()->get_default_values();
        $network_admin_values = WPEL_Network_Admin_Fields::get_instance()->get_default_values();

        update_site_option( 'wpel-network-settings', $network_values );
        update_site_option( 'wpel-network-admin-settings', $network_admin_values );
    }

    /**
     * Activate site
     * @return void
     */
    private function activate_site()
    {
        $site_already_set = get_option( 'wpel-external-link-settings' );

        if ( $site_already_set ) {
            return;
        }
        
        // get default values
        $external_link_values = WPEL_External_Link_Fields::get_instance()->get_default_values();
        $internal_link_values = WPEL_Internal_Link_Fields::get_instance()->get_default_values();
        $excluded_link_values = WPEL_Excluded_Link_Fields::get_instance()->get_default_values();
        $exceptions_link_values = WPEL_Exceptions_Fields::get_instance()->get_default_values();
        $admin_link_values = WPEL_Admin_Fields::get_instance()->get_default_values();

        // update new values
        update_option( 'wpel-external-link-settings', $external_link_values );
        update_option( 'wpel-internal-link-settings', $internal_link_values );
        update_option( 'wpel-excluded-link-settings', $excluded_link_values );
        update_option( 'wpel-exceptions-settings', $exceptions_link_values );
        update_option( 'wpel-admin-settings', $admin_link_values );

        // update meta data
        $plugin_data = get_plugin_data( WPEL_Plugin::get_plugin_file() );
        update_option( 'wpel-version', $plugin_data[ 'Version' ] );
    }

    /**
     * Uninstall site
     */
    protected function uninstall()
    {
        global $wpdb;

        if ( is_multisite() ) {
            // network activation
            $sites = wp_get_sites();
            $active_blog = $wpdb->blogid;
            foreach ( $sites as $site ) {
                switch_to_blog( $site[ 'blog_id' ] );
                $this->uninstall_site();
            }

            // switch back to active blog
            switch_to_blog( $active_blog );

            // network settings
            delete_site_option( 'wpel-network-settings' );
            delete_site_option( 'wpel-network-admin-settings' );
        } else {
            // single site activation
            $this->uninstall_site();
        }
    }

    /**
     * Plugin uninstall procedure
     */
    protected function uninstall_site()
    {
        // delete options
        delete_option( 'wpel-external-link-settings' );
        delete_option( 'wpel-internal-link-settings' );
        delete_option( 'wpel-excluded-link-settings' );
        delete_option( 'wpel-exceptions-settings' );
        delete_option( 'wpel-admin-settings' );

        delete_option( 'wpel-version' );
        delete_option( 'wpel-show-notice' );
    }

}

/*?>*/
