<?php
/**
 * Class WPEL_Update
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Update extends WPRun_Base_1x0x0
{

    /**
     * Initialize
     */
    protected function init()
    {
        $this->update_to_v2();
    }

    /**
     * Action for "admin_init"
     */
    protected function action_admin_init()
    {
        $this->update_version();
    }

    /**
     * Update version
     * @return void
     */
    private function update_version()
    {
        $plugin_data = get_plugin_data( WPEL_Plugin::get_plugin_file() );

        $current_version = $plugin_data[ 'Version' ];
        $saved_version = get_option( 'wpel-version' );

        if ( $current_version !== $saved_version ) {
            update_option( 'wpel-version', $current_version );
        }
    }

    /**
     * Update procedure to v2.x
     * @return void
     */
    private function update_to_v2()
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

        // Upgrade to version 2
        // check for old option values version < 2.0.0
        $old_main = get_option( 'wp_external_links-main' );
        $old_seo = get_option( 'wp_external_links-seo' );
        $old_style = get_option( 'wp_external_links-style' );
        $old_extra = get_option( 'wp_external_links-extra' );
        $old_screen = get_option( 'wp_external_links-screen' );

        // convert old to new db option values
        if ( ! empty( $old_main ) || ! empty( $old_seo ) || ! empty( $old_style ) || ! empty( $old_extra ) || ! empty( $old_screen ) ) {
            // helper function
            $val = function ( $arr, $key, $default = '' ) {
                if ( ! isset( $arr[ $key ] ) ) {
                    return $default;
                }

                return (string) $arr[ $key ];
            };

            // mapping
            if ( ! empty( $old_main ) ) {
                $target = $val( $old_main, 'target' );
                $external_link_values[ 'target' ] = str_replace( '_none', '_self', $target );

                $exceptions_link_values[ 'apply_all' ] = $val( $old_main, 'filter_page' );
                $exceptions_link_values[ 'apply_post_content' ] = $val( $old_main, 'filter_posts' );
                $exceptions_link_values[ 'apply_comments' ] = $val( $old_main, 'filter_comments' );
                $exceptions_link_values[ 'apply_widgets' ] = $val( $old_main, 'filter_widgets' );
                $exceptions_link_values[ 'exclude_urls' ] = $val( $old_main, 'ignore' );
                $exceptions_link_values[ 'subdomains_as_internal_links' ] = $val( $old_main, 'ignore_subdomains' );
            }
            if ( ! empty( $old_seo ) ) {
                $external_link_values[ 'rel_follow' ] = ( '1' ==  $val( $old_seo, 'nofollow' ) ) ? 'nofollow' : 'follow';
                $external_link_values[ 'rel_follow_overwrite' ] = $val( $old_seo, 'overwrite_follow' );
                $external_link_values[ 'rel_external' ] = $val( $old_seo, 'external' );

                $title = $val( $old_seo, 'title' );
                $external_link_values[ 'title' ] = str_replace( '%title%', '{title}', $title );
            }
            if ( ! empty( $old_style ) ) {
                if ( $old_style[ 'icon' ] ) {
                    $external_link_values[ 'icon_type' ] = 'image';
                    $external_link_values[ 'icon_image' ] = $val( $old_style, 'icon', '1' );
                }
                $external_link_values[ 'class' ] = $val( $old_style, 'class_name' );
                $external_link_values[ 'no_icon_for_img' ] = $val( $old_style, 'image_no_icon' );
            }
            if ( ! empty( $old_extra ) ) {
                // nothing
            }
            if ( ! empty( $old_screen ) ) {
                $admin_link_values[ 'own_admin_menu' ] = ( 'admin.php' == $val( $old_screen, 'menu_position' ) ) ? '1' : '';
            }

            // delete old values
            delete_option( 'wp_external_links-meta' );
            delete_option( 'wp_external_links-main' );
            delete_option( 'wp_external_links-seo' );
            delete_option( 'wp_external_links-style' );
            delete_option( 'wp_external_links-extra' );
            delete_option( 'wp_external_links-screen' );
        }

        // update new values
        update_option( 'wpel-external-link-settings', $external_link_values );
        update_option( 'wpel-internal-link-settings', $internal_link_values );
        update_option( 'wpel-excluded-link-settings', $excluded_link_values );
        update_option( 'wpel-exceptions-settings', $exceptions_link_values );
        update_option( 'wpel-admin-settings', $admin_link_values );
    }

}

/*?>*/
