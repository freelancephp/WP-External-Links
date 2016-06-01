<?php
/**
 * Class WPEL_Front_Ignore
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Front_Ignore extends WPRun_Base_0x7x0
{

    /**
     * @var array
     */
    private $content_placeholders = array();

    /**
     * Filter for "wpel_before_filter"
     * @param string $content
     * @return string
     */
    protected function filter_wpel_before_filter_10000000000( $content )
    {
        $content = preg_replace_callback(
            '/<script([^>]*)>(.*?)<\/script[^>]*>/is'
            , $this->get_callback( 'skip_script_tags' )
            , $content
        );

        return $content;
    }

    /**
     * Filter for "wpel_after_filter"
     * @param string $content
     * @return string
     */
    protected function filter_wpel_after_filter_10000000000( $content )
    {
       return $this->restore_content_placeholders( $content );
    }

//    protected function action_wpel_link( $link_object )
//    {
//        $url = $link_object->getAttribute( 'href' );
//        $link_object->setAttribute( 'href', '//somedom.com?url='. urlencode( $url ) );
//
////        global $post;
////        debug( $post->ID );
////        add_filter( 'wpel_apply_settings', '__return_false' );
////        add_filter( 'wpel_apply_settings', function () use ( $post ) {
////            if ( $post->ID == '285' ) {
////                return false;
////            }
////
////            return true;
////        } );
//    }

    /**
     * Pregmatch callback
     * @param array $matches
     * @return string
     */
    protected function skip_script_tags( $matches )
    {
        $script_content = $matches[ 0 ];
        return $this->get_placeholder( $script_content );
    }

    /**
     * Return placeholder text for given content
     * @param string $placeholding_content
     * @return string
     */
    protected function get_placeholder( $placeholding_content )
    {
        $placeholder = '<!--- WPEL PLACEHOLDER '. count( $this->content_placeholders ) .' --->';
        $this->content_placeholders[ $placeholder ] = $placeholding_content;
        return $placeholder;
    }

    /**
     * Restore placeholders with original content
     * @param string $content
     * @return string
     */
    protected function restore_content_placeholders( $content )
    {
        foreach ( $this->content_placeholders as $placeholder => $placeholding_content ) {
            $content = str_replace( $placeholder, $placeholding_content, $content );
        }

        return $content;
    }

}

/*?>*/
