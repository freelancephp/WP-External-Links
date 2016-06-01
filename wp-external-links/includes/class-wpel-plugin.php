<?php
/**
 * Class WPEL_Plugin
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Plugin extends WPRun_Base_0x7x0
{

    /**
     * @var string
     */
    private static $plugin_file = null;

    /**
     * @var string
     */
    private static $plugin_dir = null;

    /**
     * Initialize plugin
     * @param string $plugin_file
     * @param string $plugin_dir
     */
    protected function init( $plugin_file, $plugin_dir )
    {
        self::$plugin_file = $plugin_file;
        self::$plugin_dir = untrailingslashit( $plugin_dir );

        WPEL_Registerhooks::create();
        WPEL_Textdomain::create();

        // network admin page
        $network_page = WPEL_Network_Page::create( array(
            'network-settings'          => WPEL_Network_Fields::create(),
            'network-admin-settings'    => WPEL_Network_Admin_Fields::create(),
        ) );

        // admin settings page
        $settings_page = WPEL_Settings_Page::create( $network_page, array(
            'external-links'    => WPEL_External_Link_Fields::create(),
            'internal-links'    => WPEL_Internal_Link_Fields::create(),
            'admin'             => WPEL_Admin_Fields::create(),
            'exceptions'        => WPEL_Exceptions_Fields::create(),
        ) );

        // front site
        if ( ! is_admin() ) {
            // filter hooks
            WP_Final_Output_0x7x0::create();
            WP_Widget_Output_0x7x0::create();

            // front site
            WPEL_Front::create( $settings_page );
            WPEL_Front_Ignore::create();
        }
    }

    /**
     * @return string
     */
    public static function get_plugin_file()
    {
        return self::$plugin_file;
    }

    /**
     * @param string $path Optional
     * @return string
     */
    public static function get_plugin_dir( $path = '' )
    {
        return self::$plugin_dir . $path;
    }

}

/*?>*/
