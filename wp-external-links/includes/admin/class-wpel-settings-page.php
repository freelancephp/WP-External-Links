<?php
/**
 * Class WPEL_Tabs_Admin_Page
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Tabs_Admin_Page extends WPEL_Base
{

    /**
     * Action for "admin_menu"
     */
    protected function action_admin_menu()
    {
        $this->page_hook = add_menu_page(
            __( 'WPRun Tabs' , 'demo-wprun' )           // page title
            , __( 'WPRun Tabs' , 'demo-wprun' )         // menu title
            , 'manage_options'                          // capability
            , 'wprun-admin-tabs-page'                   // id
            , $this->get_callback( 'show_admin_page' )  // callback
            , 'dashicons-smiley'                        // icon
        );
    }

    /**
     * Show Admin Page
     */
    protected function show_admin_page()
    {
        $action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );

        $admin_page_template_file = self::get_plugin_dir( '/templates/admin-pages/tabs/page.php' );
        $this->show_template( $admin_page_template_file, array(
            'action'        => $action ? $action : '1',
        ) );
    }

}

/*?>*/
