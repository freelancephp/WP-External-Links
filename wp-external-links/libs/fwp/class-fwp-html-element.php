<?php
/**
 * Class FWP_HTML_Element_1x0x0
 *
 * @package  FWP
 * @category WordPress Library
 * @version  1.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WPRun-WordPress-Development
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class FWP_HTML_Element_1x0x0
{

    /**
     * @var string
     */
    private $tag_name = null;

    /**
     * @var string
     */
    private $content = null;

    /**
     * @var array
     */
    private $atts = array();

    /**
     * @param array $atts
     * @param string $content
     */
    public function __construct( $tag_name, array $atts, $content = null )
    {
        $this->tag_name = $tag_name;
        $this->atts = $atts;
        $this->content = $content;
    }

    /**
     * @param string $content
     */
    public function set_content( $content )
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function get_tag_name()
    {
        return $this->tag_name;
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function get_attr( $name )
    {
        if ( ! isset( $this->atts[ $name ] ) ) {
            return null;
        }

        return $this->atts[ $name ];
    }

    /**
     * @param string $name
     * @param string $value Optional
     */
    public function set_attr( $name, $value = null )
    {
        $this->atts[ $name ] = $value;
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function has_attr( $name )
    {
        return isset( $this->atts[ $name ] );
    }

    /**
     * @param string $name
     */
    public function remove_attr( $name )
    {
        unset( $this->atts[ $name ] );
    }

    /**
     * @param string $name
     * @param string $value
     * @return boolean
     */
    public function has_attr_value( $name, $value )
    {
        if ( ! $this->has_attr( $name ) ) {
            return false;
        }

        $attr_values = explode( ' ', $this->get_attr( $name ) );
        return in_array( $value, $attr_values );
    }

    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    public function add_to_attr( $name, $value )
    {
        if ( empty( $this->atts[ $name ] ) ) {
            $this->set_attr( $name, $value );
            return;
        }

        if ( $this->has_attr_value( $name, $value ) ) {
            return;
        }

        $this->atts[ $name ] .= ' '. $value;
    }

    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    public function remove_from_attr( $name, $value )
    {
        if ( ! $this->has_attr_value( $name, $value ) ) {
            return;
        }

        $attr_values = explode( ' ', $this->atts[ $name ] );
        $new_attr_values = array_diff( $attr_values , array( $value ) );

        $this->atts[ $name ] = implode( ' ', $new_attr_values );
    }

    /**
     * @return string
     */
    public function get_html()
    {
		$link = '<'. $this->tag_name;

		foreach ( $this->atts AS $key => $value ) {
            if ( null === $value ) {
    			$link .= ' '. $key;
            } else {
    			$link .= ' '. $key .'="'. esc_attr( $value ) .'"';
            }
        }

        if ( null === $this->content ) {
    		$link .= '>';
        } else {
    		$link .= '>'. $this->content .'</'. $this->tag_name .'>';
        }

        return $link;
    }

}


/*?>*/
