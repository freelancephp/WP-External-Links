<?php
/**
 * Class WPEL_Plugin
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Plugin extends WPRun_Base_1x0x0
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

        WPEL_Register_Hooks::create();
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
            // filter hooks
            FWP_Final_Output_1x0x0::create();
            FWP_Widget_Output_1x0x0::create();

            // front site
            WPEL_Front::create( $settings_page );
            WPEL_Front_Ignore::create( $settings_page );

            WPEL_Template_Tags::create();
        }

        // update procedures
        WPEL_Update::create();
    }

    /**
     * Action for "plugins_loaded"
     */
    protected function action_plugins_loaded()
    {
        load_plugin_textdomain( 'wp-external-links', false, WPEL_Plugin::get_plugin_dir( '/languages' )  );
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
