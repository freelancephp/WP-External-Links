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
     * @param string $tag_name
     * @param string $content  Optional
     */
    public function __construct( $tag_name, $content = null )
    {
        $this->tag_name = $tag_name;
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
     * Set attributes
     * @param array|string $atts
     */
    public function set_atts( $atts ) {
        if ( is_string( $atts ) ) {
            $this->atts = $this->parse_atts( $atts );
        } else if ( is_array( $atts ) ) {
            $this->atts = $atts;
        }
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
    public function get_html( $escape_content = true )
    {
        $link = '<'. esc_attr( $this->tag_name );

		foreach ( $this->atts AS $key => $value ) {
            if ( null === $value ) {
                $link .= ' '. $key;
            } else {
                $link .= ' '. esc_attr( $key ) .'="'. esc_attr( $value ) .'"';
            }
        }

        $link .= '>';

        if ( null !== $this->content ) {
            if ( true === $escape_content ) {
        		$link .= esc_html( $this->content );
            } else {
        		$link .= $this->content;
            }

            $link .= '</'. esc_attr( $this->tag_name ) .'>';
        }

        return $link;
    }

    /**
     * Parse an attributes string into an array. If the string starts with a tag,
     * then the attributes on the first tag are parsed. This parses via a manual
     * loop and is designed to be safer than using DOMDocument.
     *
     * @param    string   $atts
     * @return   array
     *
     * @example  parse_attrs( 'src="example.jpg" alt="example"' )
     * @example  parse_attrs( '<img src="example.jpg" alt="example">' )
     * @example  parse_attrs( '<a href="example"></a>' )
     *
     * @link http://dev.airve.com/demo/speed_tests/php/parse_attrs.php
     */
    final protected function parse_atts( $atts ) {
        $atts = str_split( trim( $atts ) );

        if ( '<' === $atts[0] ) { // looks like a tag so strip the tagname
            while ( $atts && ! ctype_space( $atts[0] ) && $atts[0] !== '>' ) {
                array_shift($atts);
            }
        }

        $arr = array(); // output
        $name = '';     // for the current attr being parsed
        $value = '';    // for the current attr being parsed
        $mode = 0;      // whether current char is part of the name (-), the value (+), or neither (0)
        $stop = false;  // delimiter for the current $value being parsed
        $space = ' ';   // a single space

        foreach ( $atts as $j => $curr ) {
            if ( $mode < 0 ) { // name
                if ( '=' === $curr ) {
                    $mode = 1;
                    $stop = false;
                } elseif ( '>' === $curr ) {
                    '' === $name or $arr[ $name ] = $value;
                    break;
                } elseif ( ! ctype_space( $curr ) ) {
                    if ( ctype_space( $atts[ $j - 1 ] ) ) {     // previous char
                        '' === $name or $arr[ $name ] = '';     // previous name
                        $name = $curr;                          // initiate new
                    } else {
                        $name .= $curr;
                    }
                }
            } elseif ( $mode > 0 ) { // value
                if ( $stop === false ) {
                    if ( ! ctype_space( $curr ) ) {
                        if ( '"' === $curr || "'" === $curr ) {
                            $value = '';
                            $stop = $curr;
                        } else {
                            $value = $curr;
                            $stop = $space;
                        }
                    }
                } elseif ( $stop === $space ? ctype_space( $curr ) : $curr === $stop ) {
                    $arr[ $name ] = $value;
                    $mode = 0;
                    $name = $value = '';
                } else {
                    $value .= $curr;
                }
            } else { // neither
                if ( '>' === $curr )
                    break;
                if ( ! ctype_space( $curr ) ) {
                    // initiate
                    $name = $curr;
                    $mode = -1;
                }
            }
        }

        // incl the final pair if it was quoteless
        '' === $name or $arr[ $name ] = $value;

        return $arr;
    }

}

/*?>*/
