<?php
/**
 * WP External Links Plugin
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
 * @author   Victor Villaverde Laan
 * @link     https://wordpress.org/plugins/wp-external-links/
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 *
 * @wordpress-plugin
 * Plugin Name:    WP External Links
 * Version:        2.1.1
 * Plugin URI:     https://wordpress.org/plugins/wp-external-links/
 * Description:    Open external links in a new tab or window, adding "nofollow" and "noopener", set font icon, SEO friendly options and more.
 * Author:         Victor Villaverde Laan
 * Author URI:     http://www.finewebdev.com
 * License:        Dual licensed under the MIT and GPLv2+ licenses
 * Text Domain:    wp-external-links
 * Domain Path:    /languages
 */
if ( ! function_exists( 'wpel_init' ) ):
    function wpel_init()
    {
        // only load in WP environment
        if ( ! defined( 'ABSPATH' ) ) {
            die();
        }

        // check requirements
        $wp_version = get_bloginfo( 'version' );
        $php_version = phpversion();

        if ( version_compare( $wp_version, '3.6', '<' ) || version_compare( $php_version, '5.3', '<' ) ) {
            if ( ! function_exists( 'wpel_requirements_notice' ) ) {
                function wpel_requirements_notice()
                {
                    // php 5.2 doesn't yet support __DIR__
                    include dirname( __FILE__ ) .'/templates/requirements-notice.php';
                }

                add_action( 'admin_notices', 'wpel_requirements_notice' );
            }

            return;
        }

        /**
         * Autoloader
         */
        require_once __DIR__ . '/libs/wprun/class-wprun-autoloader.php';

        $autoloader = new WPRun_Autoloader_1x0x0();
        $autoloader->add_path( __DIR__ . '/libs/', true );
        $autoloader->add_path( __DIR__ . '/includes/', true );

        /**
         * Load debugger
         */
        if ( true === constant( 'WP_DEBUG' ) ) {
            FWP_Debug_1x0x0::create( array(
                'log_hooks'  => false,
            ) );
        }

        /**
         * Set plugin vars
         */
        WPEL_Plugin::create(
            defined( 'TEST_WPEL_PLUGIN_FILE' ) ? TEST_WPEL_PLUGIN_FILE : __FILE__
            , __DIR__
        );
    }

    wpel_init();
endif;


/*?>*/
