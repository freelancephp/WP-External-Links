<?php
/**
 * Class WPEL_Activation
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.2.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Activation extends FWP_Register_Hook_Base_1x0x0
{

    /**
     * @var string
     */
    protected $hook_type = 'activation';

    /**
     * Activate network
     * @return void
     */
    protected function network_procedure()
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
    protected function site_procedure()
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

}

/*?>*/
