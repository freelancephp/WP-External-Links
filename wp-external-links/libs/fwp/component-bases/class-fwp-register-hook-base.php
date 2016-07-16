<?php
/**
 * Class FWP_Register_Hook_Base_1x0x0
 *
 * @package  FWP
 * @category WordPress Library
 * @version  1.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WPRun-WordPress-Development
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
abstract class FWP_Register_Hook_Base_1x0x0 extends WPRun_Base_1x0x0
{

    /**
     * @var string
     */
    protected $hook_type = null;

    /**
     * @var wpdb
     */
    private $wpdb = null;

    /**
     * Initialize
     * @triggers E_USER_NOTICE  Hook function does not exist
     */
    protected function init( $plugin_file, wpdb $wpdb )
    {
        $this->wpdb = $wpdb;

        $wp_hook_function = 'register_'. $this->hook_type .'_hook';

        if ( ! function_exists( $wp_hook_function ) ) {
            trigger_error( 'Register hook function "'. $wp_hook_function .'" does not exist.' );
        }

        $wp_hook_function(
            $plugin_file
            , $this->get_callback( 'procedure' )
        );
    }

    /**
     * Plugin activation procedure
     */
    protected function procedure( $networkwide = null )
    {
        if ( is_multisite() && $networkwide ) {
            // network activation
            $sites = wp_get_sites();
            $active_blog = $this->wpdb->blogid;

            foreach ( $sites as $site ) {
                switch_to_blog( $site[ 'blog_id' ] );
                $this->site_procedure();
            }

            // switch back to active blog
            switch_to_blog( $active_blog );

            $this->network_procedure();
        } else {
            // single site activation
            $this->site_procedure();
        }
    }

    /**
     * Network hook procedure
     * @return void
     */
    protected function network_procedure()
    {
    }

    /**
     * Site hook procedure
     * @return void
     */
    abstract protected function site_procedure();

}

/*?>*/
