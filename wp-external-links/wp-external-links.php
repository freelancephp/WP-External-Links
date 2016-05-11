<?php
/**
 * WP External Links Plugin
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  1.81
 * @author   Victor Villaverde Laan
 * @link     https://wordpress.org/plugins/wp-external-links/
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 *
 * @wordpress-plugin
 * Plugin Name:    WP External Links
 * Version:        1.81
 * Plugin URI:     https://wordpress.org/plugins/wp-external-links/
 * Description:    Open external links in a new window/tab, add "external" / "nofollow" to rel-attribute, set icon, XHTML strict, SEO friendly...
 * Author:         Victor Villaverde Laan
 * Author URI:     http://www.finewebdev.com
 * License:        Dual licensed under the MIT and GPLv2+ licenses
 * Text Domain:    wpel
 * Domain Path:    /languages
 */
call_user_func( function () {

    // only load in WP environment
    if ( !defined( 'ABSPATH' ) ) {
        die();
    }

    /**
     * Autoloader
     */
    require_once __DIR__ . '/includes/libs/wprun/class-wprun-autoloader.php';

    $autoloader = new WPRun_Autoloader_0x7x0();
    $autoloader->add_path( __DIR__ . '/includes/', true );

    /**
     * Load debugger
     */
    if ( true === constant( 'WP_DEBUG' ) ) {
        WP_Debug_0x7x0::create( array(
            'log_hooks'  => false,
        ) );
    }

    /**
     * Set plugin vars
     */
    WPEL_Base::set_plugin_file( defined( 'TEST_WPEL_PLUGIN_FILE' ) ? TEST_WPEL_PLUGIN_FILE : __FILE__ );
    WPEL_Base::set_plugin_dir( __DIR__ );

    /**
     * Create plugin components
     */

    WPEL_Registerhooks::create();
    WPEL_Textdomain::create();

    if ( is_admin() ) {

    } else {
        // Filter hooks
        WP_Final_Output_0x7x0::create();
        WP_Widget_Output_0x7x0::create();

        WPEL_Front::create();
    }

} );


/*?>*/
