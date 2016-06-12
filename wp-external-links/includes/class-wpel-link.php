<?php
/**
 * Class WPEL_Link
 *
 * This class extends DOMElement which uses the camelCase naming style.
 * Therefore this class also contains camelCase names.
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class WPEL_Link extends FWP_HTML_Element_1x0x0
{

    /**
     * Mark as external link (by setting data attribute)
     */
    public function set_external()
    {
        $this->set_attr( 'data-wpel-link', 'external' );
    }

    /**
     * Is marked as external link
     * @return boolean
     */
    public function is_external()
    {
        return 'external' === $this->get_attr( 'data-wpel-link' ) || $this->has_attr_value( 'rel', 'external' );
    }

    /**
     * Mark as internal link (by setting data attribute)
     */
    public function set_internal()
    {
        $this->set_attr( 'data-wpel-link', 'internal' );
    }

    /**
     * Is marked as internal link
     * @return boolean
     */
    public function is_internal()
    {
        return 'internal' === $this->get_attr( 'data-wpel-link' );
    }

    /**
     * Mark as excluded link (by setting data attribute)
     */
    public function set_exclude()
    {
        $this->set_attr( 'data-wpel-link', 'exclude' );
    }

    /**
     * Is marked as excluded link
     * @return boolean
     */
    public function is_exclude()
    {
        return 'exclude' === $this->get_attr( 'data-wpel-link' );
    }

    /**
     * Mark as ignored link (by setting data attribute)
     */
    public function set_ignore()
    {
        $this->set_attr( 'data-wpel-link', 'ignore' );
    }

    /**
     * Is marked as ignored link
     * @return boolean
     */
    public function is_ignore()
    {
        return 'ignore' === $this->get_attr( 'data-wpel-link' );
    }

    /**
     * Check url is mailto link
     * @return boolean
     */
    public function is_mailto()
    {
        $url = trim( $this->get_attr( 'href' ) );

        if ( substr( $url, 0, 7 ) === 'mailto:' ) {
            return true;
        }

        return false;
    }

}

/*?>*/
