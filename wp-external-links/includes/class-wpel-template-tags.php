<?php
/**
 * Class WPEL_Template_Tags
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Template_Tags extends WPRun_Base_1x0x0
{

    /**
     * Create template tag(s)
     */
    protected function init()
    {
        $this->create_templatetag();
    }

    /**
     * Create template tag
     * @return void
     */
    protected function create_templatetag()
    {
        if ( function_exists( 'wpel_filter' ) ) {
            return;
        }

        /**
         * Template tag to apply plugin settings on given content
         * @return string
         */
        function wpel_filter( $content ) {
            // hidden dependency to WPEL_Front::scan()
            return WPEL_Front::get_instance()->scan( $content );
        }
    }

}

/*?>*/
