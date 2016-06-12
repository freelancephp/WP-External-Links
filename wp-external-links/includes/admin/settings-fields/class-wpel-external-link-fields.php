<?php
/**
 * Class WPEL_External_Link_Fields
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_External_Link_Fields extends WPEL_Link_Fields_Base
{

    /**
     * Initialize
     */
    protected function init()
    {
        $option_name = 'wpel-external-link-settings';
        $fields = $this->get_general_fields( $option_name );

        // specific field settings
        $fields[ 'apply_settings' ][ 'label' ] = __( 'Settings for external links:', 'wp-external-links' );
        $fields[ 'apply_settings' ][ 'default_value' ] = '1';
        $fields[ 'target' ][ 'label' ] = __( 'Open external links:', 'wp-external-links' );

        //
        $index_prev = array_search( 'rel_noreferrer', array_keys( $fields ) );
        $index_insert = $index_prev + 1;

        $additional_fields = array(
            'rel_external' => array(
                'label'         => '',
                'class'         => 'wpel-no-label wpel-hidden',
                'default_value' => '1',
            ),
        );

        $fields = array_merge(
            array_slice( $fields, 0, $index_insert )
            , $additional_fields
            , array_slice( $fields, $index_insert )
        );

        $this->set_settings( array(
            'section_id'        => 'wpel-external-link-fields',
            'page_id'           => 'wpel-external-link-fields',
            'option_name'       => $option_name,
            'option_group'      => $option_name,
            'title'             => __( 'External Links', 'wp-external-links' ),
            'fields'            => $fields,
        ) );

        parent::init();
    }

    /**
     * Show field methods
     */

    protected function show_rel_external( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Add <code>"external"</code>', 'wp-external-links' )
            , '1'
            , ''
        );
    }

    /**
     * Validate and sanitize user input before saving to databse
     * @param array $new_values
     * @param array $old_values
     * @return array
     */
    protected function before_update( array $new_values, array $old_values )
    {
        $is_valid = true;

        $is_valid = $is_valid && in_array( $new_values[ 'rel_external' ], array( '', '1' ) );

        if ( false === $is_valid ) {
            // error when user input is not valid conform the UI, probably tried to "hack"
            $this->add_error( __( 'Something went wrong. One or more values were invalid.', 'wp-external-links' ) );
            return $old_values;
        }

        $update_values = parent::before_update( $new_values, $old_values );
        return $update_values;
    }

}

/*?>*/
