<?php
/**
 * Class FWD_DOM_Element_0x7x0
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class FWD_DOM_Element_0x7x0 extends DOMElement
{

    /**
     * @var DOMDocument
     */
    private static $doc = null;

    /**
     * @var string
     */
    private $content = null;

    /**
     * Factory method
     * @param string $tagName
     * @param string $content
     * @param array $attributes
     * @return WPEL_Link
     */
    public static function create( $tagName, $content = null, array $attributes = array() )
    {
        if ( null === self::$doc ) {
            self::$doc = self::createDocument();
        }

        $element = self::$doc->createElement( $tagName );
//        $element->setContent( $content );
        $element->nodeValue = $content;
        $element->setAttributes( $attributes );

        return $element;
    }

    /**
     * Factory method
     * @param string $tagName
     * @param string $label
     * @return WPEL_Link
     */
    public static function createDocument()
    {
        $doc = new DOMDocument();
        $doc->registerNodeClass( 'DOMElement', get_called_class() );
        return $doc;
    }

    /**
     * Get all attributes
     * @return array
     */
    public function getAttributes()
    {
        $attributes = array();

        foreach ( $this->attributes as $name => $node ) {
            $attributes[ $name ] = $node->nodeValue;
        }

        return $attributes;
    }

    /**
     * Set multiple attributes
     * @param array $attributes
     */
    public function setAttributes( array $attributes )
    {
        foreach ( $attributes as $name => $value ) {
            $this->setAttribute( $name, $value );
        }
    }

    /**
     * Has given attribute value
     * Used for attributes with multiple values like "class"
     * @param string $name
     * @param string $value
     * @return boolean
     */
    public function hasAttributeValue( $name, $value )
    {
        if ( ! $this->hasAttribute( $name ) ) {
            return false;
        }

        $attrValue = $this->getAttribute( $name );
        $attrValues = explode( ' ', $attrValue );
        return in_array( $value, $attrValues );
    }

    /**
     * Add value to attribute
     * Used for attributes with multiple values like "class"
     * @param string $name
     * @param string $value
     * @return boolean
     */
    public function addValueToAttribute( $name, $value )
    {
        $attrValue = $this->getAttribute( $name );

        if ( empty( $attrValue ) ) {
            $this->setAttribute( $name, $value );
            return;
        }

        if ( $this->hasAttributeValue( $name, $value ) ) {
            return;
        }

        $this->setAttribute( $name, $attrValue .' '. $value );
    }

    /**
     * Remove value from attribute
     * Used for attributes with multiple values like "class"
     * @param string $name
     * @param string $value
     * @return boolean
     */
    public function removeValueFromAttribute( $name, $value )
    {
        if ( ! $this->hasAttributeValue( $name, $value ) ) {
            return;
        }

        $attrValue = $this->getAttribute( $name );
        $attrValues = explode( ' ', $attrValue );

        $newAttrValues = array_diff( $attrValues , array( $value ) );

        $newAttrValue = implode( ' ', $newAttrValues );
        $this->setAttribute( $name, $newAttrValue );
    }

    /**
     * Set element content
     * @param string $value
     * @param boolean $asHTML
     */
    public function setContent( $content )
    {
        $this->content = $content;
    }

    /**
     * Get element content
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Return valid HTML5
     * @return string
     */
    public function getHTML()
    {
        $attributes = $this->getAttributes();

		$link = '<'. $this->tagName;

		foreach ( $attributes AS $name => $value ) {
            if ( null === $value ) {
    			$link .= ' '. $name;
            } else {
    			$link .= ' '. $name .'="'. esc_attr( $value ) .'"';
            }
        }

        if ( null === $this->content ) {
    		$link .= '>';
        } else {
    		$link .= '>'. $this->content .'</'. $this->tagName .'>';
        }

        return $link;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getHTML();
    }

//    /**
//     * @return string
//     */
//    public function getHTML()
//    {
//        return self::$doc->saveXML( $this );
//    }

    /**
     * Append given html
     * @param string $html
     */
    public function appendHTML( $html )
    {
//        if ( version_compare( phpversion(), '5.3.6', '<' ) ) {
            $fragment = self::$doc->createDocumentFragment();
            $fragment->appendXML( $html );
            $this->appendChild( $fragment );
//        } else {
//            $doc = self::createDocument();
//            $doc->loadHTML( $html );

//            foreach ( $doc->getElementsByTagName('body')->item(0)->childNodes as $childNode ) {
//                $node = $doc->importNode( $childNode );
//                $this->appendChild( $node );
//            }
//        }
    }

    public function prependHTML()
    {

//        $value = $this->nodeValue;
//        $this->nodeValue = '';
//
//        $newDoc = self::createDocument();
//        $newDoc->loadHTML( $html );
//
//        $textElement = self::create( 'span' );
//        $textElement->setAttribute( 'class', 'wpel-text' );
////        $textElement->setValue( $value );
//        $textElement->nodeValue = $value;
//
//        $this->appendChild( $textElement );
//
//        foreach ( $newDoc->getElementsByTagName('body')->item(0)->childNodes as $childNode ) {
//            $node = self::$doc->importNode( $childNode );
//            $this->appendChild( $node );
//        }
    }

}


/*?>*/
