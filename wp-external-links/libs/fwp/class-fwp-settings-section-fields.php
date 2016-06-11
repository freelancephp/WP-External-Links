<?php
/**
 * Class FWP_Settings_Section_Fields_1x0x0
 *
 * @package  FWP
 * @category WordPress Library
 * @version  1.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WPRun-WordPress-Development
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
abstract class FWP_Settings_Section_Fields_1x0x0 extends WPRun_Base_1x0x0
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
        'html_fields_class' => 'FWP_HTML_Fields_1x0x0',
        'fields'            => array(
            //'key' => array(
            //    'label'             => '',
            //    'class'             => '',
            //    'default_value'     => '',
            //),
        ),
    );

    /**
     * @var array
     */
    private $field_errors = array();

    /**
     * @var FWP_HTML_Fields_1x0x0
     */
    private $html_fields = null;

    /**
     * @var array
     */
    private $option_values = array();

    /**
     * Init
     */
    protected function init()
    {
        $this->set_option_values();
        $this->set_html_fields();
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
     * Set option values
     */
    private function set_option_values()
    {
        $saved_values = $this->get_saved_values();
        $default_values = $this->get_default_values();

        $values = wp_parse_args( $saved_values, $default_values );
        $this->option_values = $values;
    }

    /**
     * Get option values
     * @return array
     */
    final public function get_option_values()
    {
        return $this->option_values;
    }

    /**
     * Get the default option values
     * @return array
     */
    final public function get_default_values()
    {
        $fields = $this->get_setting( 'fields' );

        $default_values = array_map( function ( $arr ) {
            if ( ! isset( $arr[ 'default_value' ] ) ) {
                return '';
            }

            return $arr[ 'default_value' ];
        }, $fields );

        return $default_values;
    }

    /**
     * Get saved option values from database
     * @return type
     */
    final public function get_saved_values()
    {
        if ( is_network_admin() ) {
            $option = get_site_option( $this->get_setting( 'option_name' ) );
        } else {
            $option = get_option( $this->get_setting( 'option_name' ) );
        }

        $saved_values = is_array( $option ) ? $option : array();
        return $saved_values;
    }

    /**
     * Create html fields
     */
    private function set_html_fields()
    {
        $option_name = $this->get_setting( 'option_name' );

        $html_fields_class = $this->get_setting( 'html_fields_class' );
        $this->html_fields = new $html_fields_class(
            $this->option_values
            , $option_name .'-%s'
            , $option_name .'[%s]'
        );
    }

    /**
     * @return FWP_HTML_Fields_1x0x0
     */
    final protected function get_html_fields()
    {
        return $this->html_fields;
    }

    /**
     * Sanitize settings callback
     * @param array $values
     * @return array
     */
    protected function sanitize( $values )
    {
        $old_values = $this->option_values;

        $this->field_errors = array();

        $new_values = $this->before_update( $values, $old_values );

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
    protected function before_update( array $new_values, array $old_values )
    {
        return $new_values;
    }

    /**
     * Add fields
     */
    protected function add_fields()
    {
        $fields = $this->get_setting( 'fields' );

        foreach ( $fields as $key => $field_settings ) {
            $label = isset( $field_settings[ 'label' ] ) ? $field_settings[ 'label' ] : '';
            $class = isset( $field_settings[ 'class' ] ) ? $field_settings[ 'class' ] : '';

            add_settings_field(
                $key
                , $label
                , $this->get_callback( 'field_callback' )
                , $this->get_setting( 'page_id' )
                , $this->get_setting( 'section_id' )
                , array(
                    'key'           => $key,
                    'label_for'     => $this->html_fields->get_field_id( $key ),
                    'class'         => $class,
                )
            );
        }
    }

    /**
     * Show field callback
     * @param array $args
     */
    final protected function field_callback( array $args )
    {
        $field_method = 'show_'. $args[ 'key' ];

        if ( is_callable( array( $this, $field_method ) ) ) {
            $this->{ $field_method }( $args );
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
