<?php
/**
 * Class FWP_DOM_Element_1x0x0
 *
 * @package  FWP
 * @category WordPress Library
 * @version  1.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WPRun-WordPress-Development
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class FWP_DOM_Element_1x0x0 extends DOMElement
{

    /**
     * @var DOMDocument
     */
    private static $doc = null;

    /**
     * Factory method
     * @param string $tagName
     * @param string $content
     * @param array $attributes
     * @return FWP_DOM_Element_1x0x0
     */
    public static function create( $tagName, $content = null, array $attributes = array() )
    {
        if ( null === self::$doc ) {
            self::$doc = self::createDocument();
        }

        $element = self::$doc->createElement( $tagName );
        $element->setContent( $content );
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
     * Set element content
     * @param string $content
     */
    public function setContent( $content )
    {
        $clean_content = str_replace( '&', '&amp;', $content );
        $this->nodeValue = $clean_content;
    }

    /**
     * Get element content
     * @return string
     */
    public function getContent()
    {
        return $this->nodeValue;
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
    public function addToAttribute( $name, $value )
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
    public function removeFromAttribute( $name, $value )
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
     * Remove all childs
     */
    public function removeAllChilds()
    {
        foreach ( $this->childNodes as $childNode ) {
            $this->removeChild( $childNode );
        }
    }

    /**
     * Prepend child element
     * @param FWP_DOM_Element_1x0x0 $element
     */
    public function prependChild( $element )
    {
        if ( count( $this->childNodes ) > 0 ) {
            $this->insertBefore( $element, $this->childNodes->item( 0 ) );
        } else {
            $this->appendChild( $element );
        }
    }

    /**
     * Append HTML content
     * @param string $html
     */
    public function appendHTML( $html )
    {
        $tmpDoc = self::createDocument();
        $tmpDoc->loadHTML( $html );

        foreach ( $tmpDoc->getElementsByTagName( 'body' )->item( 0 )->childNodes as $node ) {
            $node = $this->ownerDocument->importNode( $node );
            $this->appendChild( $node );
        }

        // method 2:
        //$fragment = self::$doc->createDocumentFragment();
        //$fragment->appendXML( $html );
        //$this->appendChild( $fragment );
    }

    /**
     * Return valid HTML5
     * Instead of:
     * <code>
     *      self::$doc->saveXML( $this );
     * </code>
     * 
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

        if ( null === $this->nodeValue ) {
    		$link .= '>';
        } else {
    		$link .= '>';

            foreach ( $this->childNodes as $childNode ) {
                if ( $childNode instanceof DOMText ) {
                    $link .= $childNode->wholeText;
                } elseif ( $childNode instanceof FWP_DOM_Element_1x0x0 ) {
                    $link .= $childNode->getHTML();
                }
            }

    		$link .= '</'. $this->tagName .'>';
        }

        return $link;
    }

}


/*?>*/
