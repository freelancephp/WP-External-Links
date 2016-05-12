<?php
/**
 * Class WP_Settings_0x7x0
 *
 * @package  WPEL_Base
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
abstract class WP_Settings_Section_Fields_0x7x0 extends WPRun_Base_0x7x0
{

    /**
     * @var array
     */
    protected $default_settings = array(
        'section_id'        => '',
        'title'             => '',
        'description'       => '',
        'page_id'           => '',
        'option_name'       => '',
        'option_group'      => '',
        'fields'            => array(
            //'name' => array(
            //    'label'             => '',
            //    'label_for'         => '',
            //    'input_callback'    => '',
            //),
        ),
    );

    /**
     * @var array
     */
    private $field_errors = array();

    /**
     * Get sanitize callback
     * F.e. when using multiple sections on one form / page
     */
    final public function get_sanitize_callback()
    {
        return $this->get_callback( 'sanitize' );
    }

    /**
     * Action for "admin_init"
     */
    protected function action_admin_init()
    {
        $description = $this->get_setting( 'description' );
        
        add_settings_section(
            $this->get_setting( 'section_id' )      // id
            , $this->get_setting( 'title' )         // title
            , function () use ( $description ) {    // callback
                echo $description;
            }
            , $this->get_setting( 'page_id' )       // page id
        );

        register_setting(
            $this->get_setting( 'option_group' )
            , $this->get_setting( 'option_name' )
            , $this->get_callback( 'sanitize' )
        );

        $this->add_fields();
    }

    /**
     * Sanitize settings callback
     * @param array $values
     * @return array
     */
    protected function sanitize( $values )
    {
        $old_values = get_option( $this->get_setting( 'option_name' ));

        $this->field_errors = array();

        $new_values = $this->update( $values, $old_values );

        if ( count ( $this->field_errors ) > 0 ) {
            add_settings_error(
                $this->get_setting( 'option_group' )
                , 'settings_updated'
                , implode( '<br>', $this->field_errors )
                , 'error'
            );
        }

        return $new_values;
    }

    /**
     * Validate and sanitize user input before saving to databse
     * @param array $new_values
     * @param array $old_values
     * @return array
     */
    abstract protected function update( array $new_values, array $old_values );

    /**
     * Add fields
     */
    protected function add_fields()
    {
        $fields = $this->get_setting( 'fields' );
        $option_name = $this->get_setting( 'option_name' );

        $option = get_option( $option_name );
        $values = is_array( $option ) ? $option : array();

        foreach ( $fields as $key => $field_settings ) {
            $input_callback = isset( $field_settings[ 'input_callback' ] ) ? $field_settings[ 'input_callback' ] : null;
            $class = isset( $field_settings[ 'class' ] ) ? $field_settings[ 'class' ] : '';
            $label = isset( $field_settings[ 'label' ] ) ? $field_settings[ 'label' ] : '';
            $label_for = isset( $field_settings[ 'label_for' ] ) ? $field_settings[ 'label_for' ] : '';

            $field_name = $option_name .'['. $key . ']';
            $field_value = isset( $values[ $key ] ) ? $values[ $key ] : '';

            add_settings_field(
                $key
                , $label
                , $input_callback
                , $this->get_setting( 'page_id' )
                , $this->get_setting( 'section_id' )
                , array(
                    'key'           => $key,
                    'field_name'    => $field_name,
                    'field_value'   => $field_value,
                    'label_for'     => $label_for,
                    'option_name'   => $option_name,
                    'values'        => $values,
                    'class'         => $class,
                )
            );
        }
    }

    /**
     * @param string $message
     */
    final protected function add_error( $message )
    {
        $this->field_errors[] = $message;
    }

}

/*?>*/
