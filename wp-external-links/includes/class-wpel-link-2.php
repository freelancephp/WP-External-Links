<?php
/**
 * Class WPEL_Link_2
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
class WPEL_Link_2 extends WP_HTML_Element_0x7x0
{
    /**
     * Mark as external link (by setting data attribute)
     */
    public function set_external()
    {
        $this->set_attr( 'data-wpel-link', 'external' );
    }
    /**
     * @return boolean
     */
    public function is_external()
    {
        return 'external' === $this->get_attr( 'data-wpel-link' );
    }
    /**
     * Mark as internal link (by setting data attribute)
     */
    public function set_internal()
    {
        $this->set_attr( 'data-wpel-link', 'internal' );
    }
    /**
     * @return boolean
     */
    public function is_internal()
    {
        return 'internal' === $this->get_attr( 'data-wpel-link' );
    }
    /**
     * Mark as excluded link (by setting data attribute)
     */
    public function set_excluded()
    {
        $this->set_attr( 'data-wpel-link', 'excluded' );
    }
    /**
     * @return boolean
     */
    public function is_excluded()
    {
        return 'excluded' === $this->get_attr( 'data-wpel-link' );
    }
    /**
     * @return boolean
     */
    public function is_ignore()
    {
        return 'ignore' === $this->get_attr( 'data-wpel-link' );
    }
}



/*?>*/
