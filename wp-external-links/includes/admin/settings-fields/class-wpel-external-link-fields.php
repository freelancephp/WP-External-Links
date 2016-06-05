<?php
/**
 * Class WPEL_External_Link_Fields
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.0.3
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
        $fields[ 'apply_settings' ][ 'label' ] = __( 'Settings for external links:', 'wpel' );
        $fields[ 'apply_settings' ][ 'default_value' ] = '1';
        $fields[ 'target' ][ 'label' ] = __( 'Open external links:', 'wpel' );

        $this->set_settings( array(
            'section_id'        => 'wpel-external-link-fields',
            'page_id'           => 'wpel-external-link-fields',
            'option_name'       => $option_name,
            'option_group'      => $option_name,
            'title'             => __( 'External Links', 'wpel' ),
            'fields' => $fields,
        ) );
    }

}

/*?>*/
