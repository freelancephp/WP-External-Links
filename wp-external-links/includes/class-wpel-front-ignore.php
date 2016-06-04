<?php
/**
 * Class WPEL_Front_Ignore
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Front_Ignore extends WPRun_Base_1x0x0
{

    /**
     * @var array
     */
    private $content_placeholders = array();

    /**
     * @var WPEL_Settings_Page
     */
    private $settings_page = null;

    /**
     * Initialize
     * @param WPEL_Settings_Page $settings_page
     */
    protected function init( WPEL_Settings_Page $settings_page )
    {
        $this->settings_page = $settings_page;
    }

    /**
     * Get option value
     * @param string $key
     * @param string|null $type
     * @return string
     * @triggers E_USER_NOTICE Option value cannot be found
     */
    protected function opt( $key, $type = null )
    {
        return $this->settings_page->get_option_value( $key, $type );
    }

    /**
     * Filter for "wpel_before_filter"
     * @param string $content
     * @return string
     */
    protected function filter_wpel_before_filter_10000000000( $content )
    {
        $content = preg_replace_callback(
            $this->get_tag_regexp( 'head' )
            , $this->get_callback( 'skip_tag' )
            , $content
        );

        if ( $this->opt( 'ignore_script_tags' ) ) {
            $content = preg_replace_callback(
                $this->get_tag_regexp( 'script' )
                , $this->get_callback( 'skip_tag' )
                , $content
            );
        }

        return $content;
    }

    /**
     *
     * @param type $tag_name
     * @return type
     */
    protected function get_tag_regexp( $tag_name )
    {
        return '/<'. $tag_name .'([^>]*)>(.*?)<\/'. $tag_name .'[^>]*>/is';
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

//    protected function action_wp()
//    {
//        global $post;
////        debug( gettype( $post->ID ) );
////        add_filter( 'wpel_apply_settings', '__return_false' );
//        add_filter( 'wpel_apply_settings', function () use ( $post ) {
//            $excluded_posts = array( 1, 2, 4 );
//
//            if ( in_array( $post->ID, $excluded_posts ) ) {
//                return false;
//            }
//
//            return true;
//        } );
//    }

//    protected function action_wpel_link( $link_object )
//    {
//        if ( $link_object->isExternal() ) {
//            $url = $link_object->getAttribute( 'href' );
//            $redirect_url = '//somedom.com?url='. urlencode( $url );
//            $link_object->setAttribute( 'href', $redirect_url );
//        }
//    }

    /**
     * Pregmatch callback
     * @param array $matches
     * @return string
     */
    protected function skip_tag( $matches )
    {
        $skip_content = $matches[ 0 ];
        return $this->get_placeholder( $skip_content );
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
