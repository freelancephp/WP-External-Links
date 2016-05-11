<?php
/**
 * Class WP_Link
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class WP_Link
{

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
    public function __construct( array $atts, $content = null )
    {
        $this->atts = $atts;
        $this->content = $content;
    }

    /**
     * @param string $name
     * @return string
     */
    public function get_attr( $name )
    {
        return $this->atts[ $name ];
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function set_attr( $name, $value )
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
     * @param string $value
     * @return boolean
     */
    public function has_attr_value( $name, $value )
    {
        if ( !$this->has_attr( $name ) ) {
            return false;
        }

        $attr_values = explode( ' ', $this->get_attr( $name ) );
        return in_array( $value, $attr_values );
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
     * @return string
     */
    public function get_html()
    {
		$link = '<a';

		foreach ( $this->atts AS $key => $value ) {
			$link .= ' '. $key .'="'. esc_attr( $value ) .'"';
        }

		$link .= '>'. $this->content .'</a>';

        return $link;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->get_html();
    }

}


/*?>*/
