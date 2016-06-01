<?php
/**
 * Class WPEL_RegisterHooks
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Registerhooks extends WPRun_Base_0x7x0
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
        error_log( 'WPEL Demo Activate!' );
        $sites = wp_get_sites();
        
        if ( is_multisite() && $networkwide ) {
            // network activation
            $active_blog = $wpdb->blogid;

            foreach ( $sites as $site ) {
                switch_to_blog( $site[ 'blog_id' ] );
//                $this->activate_site();
            }

            // switch back to active blog
            switch_to_blog( $active_blog );
        } else {
            // single site activation
//            $this->activate_site();
        }
    }

    /**
     * Activate site
     * @return void
     */
    private function activate_site()
    {
        // convert old to new db option values
        // check for old option values version < 2.0.0
        $old_meta = get_option( 'wp_external_links-meta' );
        $old_main = get_option( 'wp_external_links-main' );

        // stop when old settings not exist
        if ( empty( $old_meta ) && empty( $old_main ) ) {
            return;
        }

        // get other old values
        $old_seo = get_option( 'wp_external_links-seo' );
        $old_style = get_option( 'wp_external_links-style' );
        $old_extra = get_option( 'wp_external_links-extra' );
        $old_screen = get_option( 'wp_external_links-screen' );

        $external_link_fields = WPEL_External_Link_Fields::get_instance();
        $external_link_values = $external_link_fields->get_option_values();

        $internal_link_fields = WPEL_Internal_Link_Fields::get_instance();
        $internal_link_values = $internal_link_fields->get_option_values();

        $exceptions_fields = WPEL_Internal_Link_Fields::get_instance();
        $exceptions_link_values = $exceptions_fields->get_option_values();

        $admin_fields = WPEL_Admin_Fields::get_instance();
        $admin_link_values = $admin_fields->get_option_values();

        // mapping
        if ( ! empty( $old_main ) ) {
            $external_link_values[ 'target' ] = str_replace( '_none', '_self', $old_main[ 'filter_page' ] );
            $exceptions_link_values[ 'apply_all' ] = $old_main[ 'filter_page' ];
            $exceptions_link_values[ 'apply_post_content' ] = $old_main[ 'filter_posts' ];
            $exceptions_link_values[ 'apply_comments' ] = $old_main[ 'filter_comments' ];
            $exceptions_link_values[ 'apply_widgets' ] = $old_main[ 'filter_widgets' ];
            $exceptions_link_values[ 'exclude_urls' ] = $old_main[ 'ignore' ];
        }
        if ( ! empty( $old_seo ) ) {
            $external_link_values[ 'rel_follow' ] = ( 1 == $old_seo[ 'nofollow' ] ) ? 'nofollow' : 'follow';
            $external_link_values[ 'rel_follow_overwrite' ] = (string) $old_seo[ 'overwrite_follow' ];
            $external_link_values[ 'rel_external' ] = (string) $old_seo[ 'external' ];
            $external_link_values[ 'title' ] = str_replace( '%title%', '{title}', $old_seo[ 'title' ] );
        }
        if ( ! empty( $old_style ) ) {
            if ( $old_style[ 'icon' ] ) {
                $external_link_values[ 'icon_type' ] = 'image';
                $external_link_values[ 'icon_image' ] = $old_style[ 'icon' ];
            }
            $external_link_values[ 'class' ] = $old_style[ 'class_name' ];
            $external_link_values[ 'no_icon_for_img' ] = (string) $old_style[ 'image_no_icon' ];
        }
        if ( ! empty( $old_screen ) ) {
            $admin_link_values[ 'own_admin_menu' ] = ( 'admin.php' == $old_screen[ 'menu_position' ] ) ? '1' : '0';
        }

        // update new values
        $external_link_fields->update_option_values( $external_link_values );
        $internal_link_fields->update_option_values( $internal_link_values );
        $exceptions_fields->update_option_values( $exceptions_link_values );
        $admin_fields->update_option_values( $admin_link_values );

        // update meta data
        $plugin_data = get_plugin_data( WPEL_Plugin::get_plugin_file() );
        update_option( 'wpel-version', $plugin_data[ 'Version' ] );
        update_option( 'wpel-show-notice', true );

        // delete old values
        delete_option( 'wp_external_links-meta' );
        delete_option( 'wp_external_links-main' );
        delete_option( 'wp_external_links-seo' );
        delete_option( 'wp_external_links-style' );
        delete_option( 'wp_external_links-extra' );
        delete_option( 'wp_external_links-screen' );
    }

    /**
     * Plugin uninstall procedure
     */
    protected function uninstall()
    {
        error_log( 'WPEL Demo Uninstall!' );

        // delete options
        $external_link_fields = WPEL_External_Link_Fields::get_instance();
        $external_link_values = $external_link_fields->delete_option_values();

        $internal_link_fields = WPEL_Internal_Link_Fields::get_instance();
        $internal_link_values = $internal_link_fields->delete_option_values();

        $exceptions_fields = WPEL_Internal_Link_Fields::get_instance();
        $exceptions_link_values = $exceptions_fields->delete_option_values();

        $admin_fields = WPEL_Admin_Fields::get_instance();
        $admin_link_values = $admin_fields->delete_option_values();
    }

}

/*?>*/
