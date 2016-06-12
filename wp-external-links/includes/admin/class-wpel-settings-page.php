<?php
/**
 * Class WPEL_Settings_Page
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Settings_Page extends WPRun_Base_1x0x0
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
     * @var array
     */
    private $tabs = array();

    /**
     * @var WPEL_Network_Page
     */
    private $network_page = null;

    /**
     * Initialize
     */
    protected function init( $network_page, array $fields_objects )
    {
        $this->network_page = $network_page;

        $this->tabs = array(
            'external-links' => array(
                'title'     => __( 'External Links', 'wp-external-links' ),
                'icon'      => '<i class="fa fa-external-link-square" aria-hidden="true"></i>',
                'fields'    => $fields_objects[ 'external-links' ],
            ),
            'internal-links' => array(
                'title'     => __( 'Internal Links', 'wp-external-links' ),
                'icon'      => '<i class="fa fa-square-o" aria-hidden="true"></i>',
                'fields'    => $fields_objects[ 'internal-links' ],
            ),
            'excluded-links' => array(
                'title'     => __( 'Excluded Links', 'wp-external-links' ),
                'icon'      => '<i class="fa fa-share-square" aria-hidden="true"></i>',
                'fields'    => $fields_objects[ 'excluded-links' ],
            ),
            'exceptions' => array(
                'title'     => __( 'Exceptions', 'wp-external-links' ),
                'icon'      => '<i class="fa fa-th-large" aria-hidden="true"></i>',
                'fields'    => $fields_objects[ 'exceptions' ],
            ),
            'admin' => array(
                'title'     => __( 'Admin Settings', 'wp-external-links' ),
                'icon'      => '<i class="fa fa-cogs" aria-hidden="true"></i>',
                'fields'    => $fields_objects[ 'admin' ],
            ),
            'support' => array(
                'title'     => __( 'Support', 'wp-external-links' ),
                'icon'      => '<i class="fa fa-question" aria-hidden="true"></i>',
            ),
        );

        // check excluded links tab available
        if ( $this->get_option_value( 'excludes_as_internal_links', 'exceptions' ) ) {
            unset( $this->tabs[ 'excluded-links' ] );
        }

        // get current tab
        $this->current_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );

        // set default tab
        if ( ! key_exists( $this->current_tab, $this->tabs ) ) {
            reset( $this->tabs );
            $this->current_tab = key( $this->tabs );
        }
    }

    /**
     * Get option value
     * @param string $key
     * @param string $type
     * @return string
     * @triggers E_USER_NOTICE Option value cannot be found
     */
    public function get_option_value( $key, $type = null )
    {
        if ( null === $type ) {
            foreach ( $this->tabs as $tab_key => $values ) {
                if ( ! isset( $values[ 'fields' ] ) ) {
                    continue;
                }

                $option_values = $values[ 'fields' ]->get_option_values();

                if ( ! isset( $option_values[ $key ] ) ) {
                    continue;
                }

                return $option_values[ $key ];
            }
        } else if ( isset( $this->tabs[ $type ][ 'fields' ] ) ) {
            $option_values = $this->tabs[ $type ][ 'fields' ]->get_option_values();
            return $option_values[ $key ];
        }

        trigger_error( 'Option value "'. $key .'" cannot be found.' );
    }

    /**
     * Action for "admin_menu"
     */
    protected function action_admin_menu()
    {
        $capability = $this->network_page->get_option_value( 'capability' );

        $own_admin_menu = $this->get_option_value( 'own_admin_menu', 'admin' );

        if ( '1' === $own_admin_menu ) {
            $this->page_hook = add_menu_page(
                __( 'WP External Links' , 'wp-external-links' )          // page title
                , __( 'External Links' , 'wp-external-links' )           // menu title
                , $capability                               // capability
                , $this->menu_slug                          // id
                , $this->get_callback( 'show_admin_page' )  // callback
                , 'none'                                    // icon
                , null                                      // position
            );
        } else {
            $this->page_hook = add_options_page(
                __( 'WP External Links' , 'wp-external-links' )          // page title
                , __( 'External Links' , 'wp-external-links' )           // menu title
                , $capability                               // capability
                , $this->menu_slug                          // id
                , $this->get_callback( 'show_admin_page' )  // callback
            );
        }

        add_action( 'load-'. $this->page_hook, $this->get_callback( 'add_help_tabs' ) );
    }

    /**
     * Set default option values for new created sites
     * @param integer $blog_id
     */
    protected function action_wpmu_new_blog( $blog_id )
    {
        $default_site_id = $this->network_page->get_option_value( 'default_settings_site' );

        foreach ( $this->tabs as $tab_key => $values ) {
            if ( ! isset( $values[ 'fields' ] ) ) {
                continue;
            }

            $option_name = $values[ 'fields' ]->get_setting( 'option_name' );

            $default_option_values = get_blog_option( $default_site_id, $option_name, array() );
            update_blog_option( $blog_id, $option_name, $default_option_values );
        }
    }

    /**
     * Action for "admin_enqueue_scripts"
     */
    protected function action_admin_enqueue_scripts()
    {
        wp_enqueue_style( 'font-awesome' );
        wp_enqueue_style( 'wpel-admin-style' );
        wp_enqueue_script( 'wpel-admin-script' );
    }

    /**
     * Show Admin Page
     */
    protected function show_admin_page()
    {
        $template_file = WPEL_Plugin::get_plugin_dir( '/templates/settings-page/main.php' );
        $page = $this->get_option_value( 'own_admin_menu' ) ? 'admin.php' : 'options-general.php';
        $page_url = admin_url() . $page .'?page='. $this->menu_slug;

        $template_vars = array(
            'tabs'              => $this->tabs,
            'current_tab'       => $this->current_tab,
            'page_url'          => $page_url,
            'menu_slug'         => $this->menu_slug,
            'own_admin_menu'    => $this->get_option_value( 'own_admin_menu', 'admin' ),
        );

        $this->show_template( $template_file, $template_vars );
    }

    /**
     * Add help tabs
     */
    protected function add_help_tabs()
    {
        $screen = get_current_screen();

        $screen->add_help_tab( array(
            'id'        => 'under-construction',
            'title'     => __( 'Under Construction', 'wp-external-links' ),
            'callback'  => $this->get_callback( 'show_help_tab' ),
        ) );
        $screen->add_help_tab( array(
            'id'        => 'data-attributes',
            'title'     => __( 'Data Attributes', 'wp-external-links' ),
            'callback'  => $this->get_callback( 'show_help_tab' ),
        ) );
    }

    /**
     * @param WP_Screen $screen
     * @param array     $args
     */
    protected function show_help_tab( $screen, array $args )
    {
        $template_file = WPEL_Plugin::get_plugin_dir( '/templates/settings-page/help-tabs/'. $args[ 'id' ] .'.php' );
        $this->show_template( $template_file );
    }

}

/*?>*/
