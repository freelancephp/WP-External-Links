<?php
/**
 * Class WPEL_Template_Tags
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.2.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Template_Tags extends FWP_Template_Tag_Base_1x0x0
{

    /**
     * @var WPEL_Front
     */
    private $front = null;

    /**
     * Initialize
     * @param WPEL_Front $front
     */
    protected function init( WPEL_Front $front )
    {
        $this->front = $front;
    }

    /**
     * Template tag funtion
     * @param string $content
     * @return string
     */
    public function wpel_filter( $content )
    {
        return $this->front->scan( $content );
    }

}

/*?>*/
