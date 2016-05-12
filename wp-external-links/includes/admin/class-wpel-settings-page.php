<?php
/**
 * Class WPEL_Settings_Page
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Settings_Page extends WPEL_Base
{

    /**
     * @var string
     */
    private $menu_slug = 'wpel-settings-page';

    /**
     * @var string
     */
    private $current_tab = null;

    /**
     * @var WP_Settings_Section_Fields_0x7x0
     */
    private $current_settings_fields = null;

    /**
     * Initialize
     */
    protected function init()
    {
        $this->current_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );

        if ( empty( $this->current_tab )) {
            $this->current_tab = '1';
        }

        if ( '1' === $this->current_tab ) {
            $this->current_settings_fields = WPEL_Link_Fields::create();
        } elseif ( '2' === $this->current_tab ) {
            $this->current_settings_fields = WPEL_Icon_Fields::create();
        } elseif ( '3' === $this->current_tab ) {
            $this->current_settings_fields = WPEL_Exclusions_Fields::create();
        } elseif ( '4' === $this->current_tab ) {
            $this->current_settings_fields = WPEL_Internal_Link_Fields::create();
        }
    }

    /**
     * Action for "admin_menu"
     */
    protected function action_admin_menu()
    {
        $this->page_hook = add_menu_page(
            __( 'WP External Links' , 'wpel' )          // page title
            , __( 'External Links' , 'wpel' )           // menu title
            , 'manage_options'                          // capability
            , $this->menu_slug                          // id
            , $this->get_callback( 'show_admin_page' )  // callback
            , 'dashicons-external'                      // icon
            , null                                      // position
        );
    }

    protected function action_admin_enqueue_scripts()
    {
        // set wpel admin script
        wp_enqueue_script(
            'wpel-admin-settings'
            , plugins_url( '/public/js/wpel-admin.js', WPEL_Base::get_plugin_file() )
            , array('jquery')
            , false
            , true
        );

        if ( '2' === $this->current_tab ) {
            wp_localize_script('wpel-admin-settings', 'wpelSettings', array(
                'pluginUrl' => plugins_url( '', WPEL_Base::get_plugin_file() ),
                'dashiconsValue' => 1,
                'fontawesomeValue' => 1,
            ));

            // set style
            wp_enqueue_style(
                'font-awesome'
                , 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css'
                , array()
                , null
            );
        }
    }

    /**
     * Show Admin Page
     */
    protected function show_admin_page()
    {
        $admin_page_template_file = self::get_plugin_dir( '/templates/settings-page.php' );
        $this->show_template( $admin_page_template_file, array(
            'current_tab'   => $this->current_tab,
            'menu_slug'     => $this->menu_slug,
            'option_group'  => $this->current_settings_fields->get_setting( 'option_group' ),
            'settings_page' => $this->current_settings_fields->get_setting( 'page_id' ),
        ) );
    }

}

/*?>*/
