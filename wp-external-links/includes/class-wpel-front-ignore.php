<?php
/**
 * Class WPEL_Front_Ignore
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
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
     * Action for "wpel_before_apply_link"
     * @param WPEL_Link $link
     */
    protected function filter_wpel_before_apply_link_10000000000( WPEL_Link $link )
    {
        // ignore mailto links
        if ( $this->opt( 'ignore_mailto_links' ) && $link->is_mailto() ) {
            $link->set_ignore();
        }

        // ignore WP Admin Bar Links
        if ( $link->has_attr_value( 'class', 'ab-item' ) ) {
            $link->set_ignore();
        }
    }

    /**
     * Filter for "_wpel_before_filter"
     * @param string $content
     * @return string
     */
    protected function filter__wpel_before_filter_10000000000( $content )
    {
        $ignore_tags = array( 'head' );

        if ( $this->opt( 'ignore_script_tags' ) ) {
            $ignore_tags[] = 'script';
        }

        foreach ( $ignore_tags as $tag_name ) {
            $content = preg_replace_callback(
                $this->get_tag_regexp( $tag_name )
                , $this->get_callback( 'skip_tag' )
                , $content
            );
        }

        return $content;
    }

    /**
     * Filter for "_wpel_after_filter"
     * @param string $content
     * @return string
     */
    protected function filter__wpel_after_filter_10000000000( $content )
    {
       return $this->restore_content_placeholders( $content );
    }

    /**
     * @param type $tag_name
     * @return type
     */
    protected function get_tag_regexp( $tag_name )
    {
        return '/<'. $tag_name .'[\s.*>|>](.*?)<\/'. $tag_name .'[\s+]*>/is';
    }

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
