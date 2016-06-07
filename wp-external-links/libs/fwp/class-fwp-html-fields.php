<?php
/**
 * Class FWP_HTML_Fields_1x0x0
 *
 * @package  FWP
 * @category WordPress Library
 * @version  1.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WPRun-WordPress-Development
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class FWP_HTML_Fields_1x0x0
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
     * @param string $label_text
     * @param array $atts  Optional
     */
    public function label( $key, $label_text, array $atts = array() )
    {
        echo '<label for="' . $this->get_field_id( $key ) . '"
                    ' . $this->get_html_atts( $atts ) . '
                >' . $label_text . '
                </label>';
    }

    /**
     * Show text input field
     * @param string $key
     * @param array  $atts  Optional
     */
    public function text( $key, array $atts = array() )
    {
        echo '<input type="text"
                    id="' . $this->get_field_id( $key ) . '"
                    name="' . $this->get_field_name( $key ) . '"
                    value="' . esc_attr( $this->get_value( $key ) ) . '"
                    ' . $this->get_html_atts( $atts ) . '
                >';
    }

    /**
     * Show text input field
     * @param string $key
     * @param array  $atts  Optional
     */
    public function text_area( $key, array $atts = array() )
    {
        echo '<textarea id="' . $this->get_field_id( $key ) . '"
                    name="' . $this->get_field_name( $key ) . '"
                    ' . $this->get_html_atts( $atts ) . '
                >'. esc_textarea( $this->get_value( $key ) ) .'</textarea>';
    }

    /**
     * Show a check field
     * @param string $key
     * @param mixed $checked_value
     * @param mixed $unchecked_value
     * @param array $atts  Optional
     */
    public function check( $key, $checked_value = '1', $unchecked_value = '', array $atts = array() )
    {
        // workaround for also posting a value when checkbox is unchecked
        if ( null !== $unchecked_value ) {
            echo '<input type="hidden"
                        name="' . $this->get_field_name( $key ) . '"
                        value="' . esc_attr( $unchecked_value ) . '"
                    >';
        }

        echo '<input type="checkbox"
                    id="' . $this->get_field_id( $key ) . '"
                    name="' . $this->get_field_name( $key ) . '"
                    value="' . esc_attr( $checked_value ) . '"
                    ' . $this->get_checked_attr( $key, $checked_value ) . '
                    ' . $this->get_html_atts( $atts ) . '
                >';
    }

    /**
     * Show a check field with label
     * @param string $key
     * @param string $label_text
     * @param mixed $checked_value
     * @param mixed $unchecked_value
     * @param array $atts  Optional
     */
    public function check_with_label( $key, $label_text, $checked_value, $unchecked_value = null, array $atts = array() )
    {
        echo '<label>';
        $this->check( $key, $checked_value, $unchecked_value, $atts );
        echo $label_text;
        echo '</label>';
    }

    /**
     * Show a radio field
     * @param string $key
     * @param mixed $checked_value
     * @param array $atts  Optional
     */
    public function radio( $key, $checked_value, array $atts = array() )
    {
        $id = $this->get_field_id( $key ) . '-' . sanitize_key( $checked_value );

        echo '<input type="radio"
                    id="' . $id . '"
                    name="' . $this->get_field_name( $key ) . '"
                    value="' . esc_attr( $checked_value ) . '"
                    ' . $this->get_checked_attr( $key, $checked_value ) . '
                    ' . $this->get_html_atts( $atts ) . '
                >';
    }

    /**
     * Show a check field with label
     * @param string $key
     * @param string $label_text
     * @param mixed $checked_value
     * @param array $atts  Optional
     */
    public function radio_with_label( $key, $label_text, $checked_value, array $atts = array() )
    {
        echo '<label>';
        $this->radio( $key, $checked_value, $atts );
        echo $label_text;
        echo '</label>';
    }

    /**
     * Show select field with or without options
     * @param string $key
     * @param mixed $checked_value
     * @param array $options
     * @param array $atts  Optional
     */
    public function select( $key, array $options = array(), array $atts = array() )
    {
        $value = $this->get_value( $key );

        echo '<select id="' . $this->get_field_id( $key ) . '"
                    name="' . $this->get_field_name( $key ) . '"
                    ' . $this->get_html_atts( $atts ) . '
                >';

        foreach ( $options as $option_value => $option_text ) {
            $this->select_option( $option_text, $option_value, ( $value == $option_value ) );
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
     * @param array $atts
     * @return string
     */
    private function get_html_atts( array $atts )
    {
        $html_atts = '';

		foreach ( $atts as $key => $value ) {
            if ( null === $value ) {
    			$html_atts .= ' '. $key;
            } else {
    			$html_atts .= ' '. $key .'="'. esc_attr( $value ) .'"';
            }
        }

        return $html_atts;
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
