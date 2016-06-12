<?php
/**
 * Class WPEL_Excluded_Link_Fields
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Excluded_Link_Fields extends WPEL_Link_Fields_Base
{

    /**
     * Initialize
     */
    protected function init()
    {
        $option_name = 'wpel-excluded-link-settings';
        $fields = $this->get_general_fields( $option_name );

        // change some specific field labels
        $fields[ 'apply_settings' ][ 'label' ] = __( 'Settings for excluded links:', 'wp-external-links' );
        $fields[ 'target' ][ 'label' ] = __( 'Open excluded links:', 'wp-external-links' );

        $this->set_settings( array(
            'section_id'    => 'wpel-excluded-link-fields',
            'page_id'       => 'wpel-excluded-link-fields',
            'option_name'   => $option_name,
            'option_group'  => $option_name,
            'title'         => __( 'Excluded Links', 'wp-external-links' ),
            'fields'        => $fields,
        ) );

        parent::init();
    }

}

/*?>*/
