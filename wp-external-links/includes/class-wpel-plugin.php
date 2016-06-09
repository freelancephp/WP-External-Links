<?php
/**
 * Class WPEL_Plugin
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.0
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

        WPEL_Registerhooks::create();

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

    /**
     * Action for "wp_enqueue_scripts"
     */
    protected function action_wp_enqueue_scripts()
    {
        $this->register_scripts();
    }

    /**
     * Action for "admin_enqueue_scripts"
     */
    protected function action_admin_enqueue_scripts()
    {
        $this->register_scripts();
    }

    /**
     * Register styles and scripts
     */
    protected function register_scripts()
    {
        $plugin_version = get_option( 'wpel-version' );

        // set style font awesome icons
        wp_register_style(
            'font-awesome'
            , 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css'
            , array()
            , $plugin_version
        );

        // front style
        wp_register_style(
            'wpel-style'
            , plugins_url( '/public/css/wpel.css', WPEL_Plugin::get_plugin_file() )
            , array()
            , $plugin_version
        );

        // set admin style
        wp_register_style(
            'wpel-admin-style'
            , plugins_url( '/public/css/wpel-admin.css', WPEL_Plugin::get_plugin_file() )
            , array()
            , $plugin_version
        );

        // set wpel admin script
        wp_register_script(
            'wpel-admin-script'
            , plugins_url( '/public/js/wpel-admin.js', WPEL_Plugin::get_plugin_file() )
            , array( 'jquery' )
            , $plugin_version
            , true
        );
    }

}

/*?>*/
