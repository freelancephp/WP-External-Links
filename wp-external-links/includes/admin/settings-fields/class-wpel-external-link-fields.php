<?php
/**
 * Class WPEL_External_Link_Fields
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
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
//delete_option($option_name);

        // set field labels
        $fields[ 'apply_settings' ][ 'label' ] = __( 'Settings for external links:', 'wpel' );
        $fields[ 'target' ][ 'label' ] = __( 'Open external links:', 'wpel' );

        $this->set_settings( array(
            'section_id'        => 'wpel-external-link-fields',
            'page_id'           => 'wpel-external-link-fields',
            'option_name'       => $option_name,
            'option_group'      => $option_name,
            'title'             => __( 'External Links', 'wpel' ),
            'description'       => __( 'Lorem ipsum...', 'wpel' ),
            'fields' => $fields,
        ) );
    }

}

/*?>*/
