<?php
/**
 * Class WPEL_Admin_Fields
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Admin_Fields extends WP_Settings_Section_Fields_0x7x0
{

    /**
     * Initialize
     */
    protected function init()
    {
        $this->set_settings( array(
            'section_id'        => 'wpel-admin-fields',
            'page_id'           => 'wpel-admin-fields',
            'option_name'       => 'wpel-admin-settings',
            'option_group'      => 'wpel-admin-settings',
            'title'             => __( 'Admin Settings', 'wpel' ),
            'description'       => __( 'Lorem ipsum...', 'wpel' ),
            'fields'            => array(
                'own_admin_menu' => array(
                    'label'         => __( 'Main Admin Menu:', 'wpel' ),
                    'default_value' => '1',
                ),
            ),
        ) );
    }

    /**
     * Show field methods
     */

    protected function show_own_admin_menu( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Create own admin menu for this plugin', 'wpel' )
            , '1'
            , ''
        );

        echo ' <p class="description">'
                . __( 'Or else it will be added to the "Settings" menu', 'wpel' )
                .'</p>';
    }

    /**
     * Validate and sanitize user input before saving to databse
     * @param array $new_values
     * @param array $old_values
     * @return array
     */
    protected function before_update( array $new_values, array $old_values )
    {
        $update_values = $new_values;

        $is_valid_check = function ( $value ) {
            $valid_vals = array( '', '1' );
            return in_array( $value, $valid_vals );
        };

        if ( ! $is_valid_check( $new_values[ 'own_admin_menu' ] ) ) {
            $update_values = $old_values;
            $this->add_error( __( 'Wrong values!', 'wpel' ) );
        }

        return $update_values;
    }

}

/*?>*/
