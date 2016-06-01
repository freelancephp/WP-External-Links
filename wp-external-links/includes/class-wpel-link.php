<?php
/**
 * Class WPEL_Link
 *
 * This class extends DOMElement which uses the camelCase naming style.
 * Therefore this class also contains camelCase names.
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class WPEL_Link extends FWD_DOM_Element_0x7x0
{

    /**
     * Mark as external link (by setting data attribute)
     */
    public function setExternal()
    {
        $this->setAttribute( 'data-wpel-link', 'external' );
    }

    /**
     * Is marked as external link
     * @return boolean
     */
    public function isExternal()
    {
        return 'external' === $this->getAttribute( 'data-wpel-link' );
    }

    /**
     * Mark as internal link (by setting data attribute)
     */
    public function setInternal()
    {
        $this->setAttribute( 'data-wpel-link', 'internal' );
    }

    /**
     * Is marked as internal link
     * @return boolean
     */
    public function isInternal()
    {
        return 'internal' === $this->getAttribute( 'data-wpel-link' );
    }

    /**
     * Mark as excluded link (by setting data attribute)
     */
    public function setExcluded()
    {
        $this->setAttribute( 'data-wpel-link', 'excluded' );
    }

    /**
     * Is marked as excluded link
     * @return boolean
     */
    public function isExcluded()
    {
        return 'excluded' === $this->getAttribute( 'data-wpel-link' );
    }

    /**
     * Is marked as ignored link
     * @return boolean
     */
    public function isIgnore()
    {
        return 'ignore' === $this->getAttribute( 'data-wpel-link' );
    }

}

/*?>*/