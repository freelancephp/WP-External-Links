<?php
/**
 * Class WPEL_Link_Fields
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Link_Fields extends WP_Settings_Section_Fields_0x7x0
{

    /**
     * Initialize
     */
    protected function init()
    {
        $this->set_settings( array(
            'section_id'        => 'wpel-link-fields',
            'title'             => __( 'Link Settings', 'demo-wprun' ),
            'description'       => __( 'Lorem ipsum...', 'demo-wprun' ),
            'page_id'           => 'wpel-link-fields',
            'option_name'       => 'wprun-admin-options-section-1',
            'option_group'      => 'wprun-admin-options-section-1',
            'fields'            => array(
                'some-key' => array(
                    'label'             => __( 'Some value:', 'demo-wprun' ),
                    'label_for'         => 'wprun-admin-options-section-1-some-key',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
            ),
        ) );
    }

    /**
     * Show field
     * @param array $args
     */
    protected function show_field( array $args )
    {
        switch ( $args[ 'key' ] ) {
            case 'some-key':
                // @todo
//                $this->show_template( WPEL_Base::get_plugin_dir( 'templates/form-fields/text.php' ), array(
//                    'id'    => $args[ 'label_for' ],
//                    'name'  => $args[ 'field_name' ],
//                    'value' => $args[ 'field_value' ],
//                    'class' => 'regular-text',
//                ) );

                echo '<input type="text" class="regular-text"
                            id="'. $args[ 'label_for' ] .'"
                            name="'. $args[ 'field_name' ] .'"
                            value="'. $args[ 'field_value' ] .'">';
                echo '<p class="description">Some extra information about this setting.</p>';
            break;
        }
    }

    /**
     * Validate and sanitize user input before saving to databse
     * @param array $new_values
     * @param array $old_values
     * @return array
     */
    protected function update( array $new_values, array $old_values )
    {
        $values = $new_values;
        
        if ( '' === trim ( $new_values[ 'some-key' ] ) ) {
            $values = $old_values;
            $this->add_error( __( 'Some key is required.', 'wprun-demo' ) );
        }

        return $values;
    }

}

/*?>*/
