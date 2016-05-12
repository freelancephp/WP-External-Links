<?php
/**
 * Class WP_Form_Fields_0x7x0
 *
 * @package  DWP_Base
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class WP_HTML_Fields_0x7x0
{

    /**
     * @var array
     */
    private $values = array();

    /**
     * @var string
     */
    private $field_id_format = '%s';

    /**
     * @var string
     */
    private $field_name_format = '%s';

    /**
     * Constructor
     */
    public function __construct( array $values = array(), $field_id_format = null, $field_name_format = null )
    {
        $this->values = $values;
        $this->field_id_format = $field_id_format;
        $this->field_name_format = $field_name_format;
    }

    /**
     * Get form field id
     * @param string $key
     * @return string
     */
    public function get_field_id( $key )
    {
        return sprintf( $this->field_id_format, $key );
    }

    /**
     * Get form field name
     * @param string $key
     * @return string
     */
    public function get_field_name( $key )
    {
        return sprintf( $this->field_name_format, $key );
    }

    /**
     * Get value
     * @param string $key
     * @param mixed $default_value Optional
     * @return mixed|null
     */
    public function get_value( $key, $default_value = null )
    {
        $values = $this->values;

        if ( !isset( $values[ $key ] ) ) {
            return $default_value;
        }

        return $values[ $key ];
    }

    /**
     * Show html label
     * @param string $key
     * @param string $labelText
     */
    public function label( $key, $labelText )
    {
        echo '<label for="' . $this->get_field_id( $key ) . '">
                     ' . $labelText . '
               </label>';
    }

    /**
     * Show text input field
     * @param string $key
     * @param string $class
     */
    public function text( $key, $class = 'regular-text' )
    {
        echo '<input type="text"
                    class="' . $class . '"
                    id="' . $this->get_field_id( $key ) . '"
                    name="' . $this->get_field_name( $key ) . '"
                    value="' . esc_attr( $this->get_value( $key ) ) . '"
                >';
    }

    /**
     * Show text input field
     * @param string $key
     * @param string $class
     */
    public function text_area( $key, $class = 'large-text' )
    {
        echo '<textarea class="' . $class . '"
                    id="' . $this->get_field_id( $key ) . '"
                    name="' . $this->get_field_name( $key ) . '"
                >'. esc_textarea( $this->get_value( $key ) ) .'</textarea>';
    }

    /**
     * Show a check field
     * @param string $key
     * @param mixed $checked_value
     * @param mixed $unchecked_value
     * @param string $class
     */
    public function check( $key, $checked_value, $unchecked_value = null, $class = '' )
    {
        // workaround for also posting a value when checkbox is unchecked
        if ( null !== $unchecked_value ) {
            echo '<input type="hidden"
                        name="' . $this->get_field_name( $key ) . '"
                        value="' . esc_attr( $unchecked_value ) . '"
                    >';
        }

        echo '<input type="checkbox"
                    class="' . $class . '"
                    id="' . $this->get_field_id( $key ) . '"
                    name="' . $this->get_field_name( $key ) . '"
                    value="' . esc_attr( $checked_value ) . '"
                    ' . $this->get_checked_attr( $key, $checked_value ) . '
                >';
    }

    /**
     * Show a radio field
     * @param string $key
     * @param mixed $checked_value
     * @param string $class
     */
    public function radio( $key, $checked_value, $class = '' )
    {
        $id = $this->get_field_id( $key ) . '-' . sanitize_key( $checked_value );

        echo '<input type="radio"
                    class="' . $class . '"
                    id="' . $id . '"
                    name="' . $this->get_field_name( $key ) . '"
                    value="' . esc_attr( $checked_value ) . '"
                    ' . $this->get_checked_attr( $key, $checked_value ) . '
                >';
    }

    /**
     * Show select field with or without options
     * @param string $key
     * @param mixed $checked_value
     * @param array $options
     * @param string $class
     */
    public function select( $key, $checked_value, array $options = array(), $class = '' )
    {
        echo '<select class="' . $class . '"
                    class="' . $class . '"
                    id="' . $this->get_field_id( $key ) . '"
                    name="' . $this->get_field_name( $key ) . '"
                >';

        foreach ( $options as $value => $text ) {
            $this->select_option( $text, $value, ( $checked_value == $value ) );
        }

        echo '</select>';
    }

    /**
     * Show a select option
     * @param string $text
     * @param string $value
     * @param boolean $selected
     */
    public function select_option( $text, $value, $selected = false )
    {
        echo '<option value="' . esc_attr( $value ) . '"' . ( $selected ? ' selected' : '' ) . '>
                    ' . $text  . '
               </option>';
    }

    /**
     * Get the checked attribute
     * @param string $key
     * @param mixed $checked_value
     * @return string
     */
    private function get_checked_attr( $key, $checked_value )
    {
        return ( $this->get_value( $key ) == $checked_value ) ? ' checked' : '';
    }

}

/*?>*/
