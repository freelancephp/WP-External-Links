<?php
/**
 * Class WPEL_Plugin
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.2.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Plugin extends FWP_Plugin_Base_1x0x0
{

    /**
     * Initialize plugin
     * @param string $plugin_file
     * @param string $plugin_dir
     */
    protected function init( $plugin_file, $plugin_dir )
    {
        parent::init( $plugin_file, $plugin_dir );

        $this->create_components();
    }

    /**
     * Create components
     */
    protected function create_components()
    {
        WPEL_Register_Scripts::create();

        // network admin page
        $network_page = WPEL_Network_Page::create( array(
            'network-settings'          => WPEL_Network_Fields::create(),
            'network-admin-settings'    => WPEL_Network_Admin_Fields::create(),
        ) );

        // admin settings page
        $settings_page = WPEL_Settings_Page::create( $network_page, array(
            'external-links'    => WPEL_External_Link_Fields::create(),
            'internal-links'    => WPEL_Internal_Link_Fields::create(),
            'excluded-links'    => WPEL_Excluded_Link_Fields::create(),
            'admin'             => WPEL_Admin_Fields::create(),
            'exceptions'        => WPEL_Exceptions_Fields::create(),
        ) );

        // front site
        if ( ! is_admin() ) {
            WPEL_Front::create( $settings_page );
        }

        // update procedures
        WPEL_Update::create();
    }

}

/*?>*/
