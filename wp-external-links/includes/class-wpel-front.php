<?php
/**
 * Class WPEL_Front
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Front extends WPRun_Base_0x7x0
{

    /**
     * @var array
     */
    private $settings_page = null;

    /**
     * @param WPEL_Settings_Page $settings_page
     */
    protected function init( $settings_page )
    {
        $this->settings_page = $settings_page;

        // apply page sections
        if ( $this->opt( 'apply_all' ) ) {
            add_action( 'final_output', $this->get_callback( 'scan' ) );
        } else {
            $filter_hooks = array();

            if ( $this->opt( 'apply_post_content' ) ) {
                array_push( $filter_hooks, 'the_title', 'the_content', 'the_excerpt', 'get_the_excerpt' );
            }

            if ( $this->opt( 'apply_comments' ) ) {
                array_push( $filter_hooks, 'comment_text', 'comment_excerpt' );
            }

            if ( $this->opt( 'apply_widgets' ) ) {
                array_push( $filter_hooks, 'widget_output' );
            }

            foreach ( $filter_hooks as $hook ) {
               add_filter( $hook, $this->get_callback( 'scan' ) );
            }
        }
    }

    /**
     * Get option value
     * @param string $key
     * @param string $type
     * @return string
     * @triggers E_USER_NOTICE Option value cannot be found
     */
    private function opt( $key, $type = null )
    {
        return $this->settings_page->get_option_value( $key, $type );
    }

    /**
     * Enqueue scripts and styles
     */
    protected function action_wp_enqueue_scripts()
    {
        $icon_type_int = $this->opt( 'icon_type', 'internal-links' );
        $icon_type_ext = $this->opt( 'icon_type', 'external-links' );

        if ( 'dashicon' === $icon_type_int || 'dashicon' === $icon_type_ext ) {
            wp_enqueue_style( 'dashicons' );
        }
        if ( 'fontawesome' === $icon_type_int || 'fontawesome' === $icon_type_ext ) {
            wp_enqueue_style(
                'font-awesome'
                , 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css'
                , array()
                , null
            );
        }
    }

    /**
     * @param string $content
     * @return string
     */
    protected function scan( $content )
    {
        /**
         * Filter whether the plugin settings will be applied on links
         * @param boolean $apply_settings
         */
        $apply_settings = apply_filters( 'wpel_apply_settings', true );

        if ( false === $apply_settings ) {
            return $content;
        }

        WP_Debug_0x7x0::start_benchmark( 'scan_links' );

       /**
        * Filters before scanning content
        * @param string $content
        */
        $content = apply_filters( 'wpel_before_filter', $content );

        $regexp_links = '/<a[^A-Za-z](.*?)>(.*?)<\/a[\s+]*>/is';

       /**
        * Filters for changing regular expression for getting html links
        * @param string $regexp_links
        */
        $regexp_links = apply_filters( 'wpel_regexp_link', $regexp_links );

		$content = preg_replace_callback( $regexp_links, $this->get_callback( 'match_link' ), $content );

//        $dom = new DOMDocument();
//
//        try {
//            $dom->loadHTML( $content );
//            $links = $dom->getElementsByTagName( 'a' );
//
//            foreach ( $links as $link ) {
//                $this->link_settings( $link );
//            }
//        } catch ( Exception $exception ) {
//            debug( $exception );
//        }
//
//        $content = $dom->saveHTML();

       /**
        * Filters after scanning content
        * @param string $content
        */
        $content = apply_filters( 'wpel_after_filter', $content );

        WP_Debug_0x7x0::end_benchmark( 'scan_links' );

        return $content;
    }

    /**
     * Pregmatch callback for handling link
     * @param array $matches  [ 0 ] => link, [ 1 ] => atts_string, [ 2 ] => label
     * @return string
     */
    protected function match_link( $matches )
    {
        $original_link = $matches[ 0 ];
        $atts = shortcode_parse_atts( $matches[ 1 ] );
        $label = $matches[ 2 ];

//        $created_link = $this->get_created_link( $label, $atts );
        $created_link = $this->get_created_link_2( $label, $atts );

        if ( false === $created_link ) {
            return $original_link;
        }
//debug($original_link);
//debug($created_link);
//return $original_link;
        return $created_link;
    }

    /**
     * Create html link
     * @param string $label
     * @param array $atts
     * @return string
     */
    protected function get_created_link( $label, array $atts )
    {
        $link = WPEL_Link::create( 'a', $label, $atts );

        if ( $link->isIgnore() ) {
            return false;
        }

        $url = $link->getAttribute( 'href' );

        $excludes_as_internal_links = $this->opt( 'excludes_as_internal_links' );

        // exceptions
        $is_included = $link->isExternal() || $this->is_included_url( $url );
        $is_excluded = $link->isExcluded() || $this->is_excluded_url( $url );

        // is internal or external
        $is_internal = ( $link->isInternal() || $this->is_internal_url( $url ) ) || ( $is_excluded && $excludes_as_internal_links );
        $is_external = ( $link->isExternal() || $is_included ) || ( ! $is_internal && ! $is_excluded );

        if ( $is_external ) {
            $link->setExternal();
            $this->apply_link_settings( $link, 'external-links' );
        } else if ( $is_internal ) {
            $link->setInternal();
            $this->apply_link_settings( $link, 'internal-links' );
        } else {
            $link->setExcluded();
        }

        /**
         * Action for changing link object
         * @param WPEL_Link $link
         * @return void
         */
        do_action( 'wpel_link', $link );

        return $link->getHTML();
    }

    /**
     * @param WPEL_Link $link
     * @param string $type
     */
    protected function apply_link_settings( WPEL_Link $link, $type )
    {
        if ( ! $this->opt( 'apply_settings', $type ) ) {
            return;
        }

        // set target
        $target = $this->opt( 'target', $type );
        $target_overwrite = $this->opt( 'target_overwrite', $type );
        $has_target = $link->hasAttribute( 'target' );

        if ( $target && ( ! $has_target || $target_overwrite ) ) {
            $link->setAttribute( 'target', $target );
        }

        // add "follow" / "nofollow"
        $follow = $this->opt( 'rel_follow', $type );
        $follow_overwrite = $this->opt( 'rel_follow_overwrite', $type );
        $has_follow = $link->hasAttributeValue( 'rel', 'follow' ) || $link->hasAttributeValue( 'rel', 'nofollow' );

        if ( $follow && ( ! $has_follow || $follow_overwrite ) ) {
            if ( $has_follow ) {
                $link->removeValueFromAttribute( 'rel', 'follow' );
                $link->removeValueFromAttribute( 'rel', 'nofollow' );
            }

            $link->addValueToAttribute( 'rel', $follow );
        }

        // add "external"
        if ( $this->opt( 'rel_external', $type ) ) {
            $link->addValueToAttribute( 'rel', 'external' );
        }

        // add "noopener"
        if ( $this->opt( 'rel_noopener', $type ) ) {
            $link->addValueToAttribute( 'rel', 'noopener' );
        }

        // add "noreferrer"
        if ( $this->opt( 'rel_noreferrer', $type ) ) {
            $link->addValueToAttribute( 'rel', 'noreferrer' );
        }

        // set title
        $title_format = $this->opt( 'title', $type );

        if ( $title_format ) {
            $title = $link->getAttribute( 'title' );
            $text = esc_attr( $link->getContent() );
            $new_title = str_replace( array( '{title}', '{text}' ), array( $title, $text ), $title_format );

            if ( $new_title ) {
                $link->setAttribute( 'title', $new_title );
            }
        }

        // add classes
        $class = $this->opt( 'class', $type );

        if ( $class ) {
            $link->addValueToAttribute( 'class', $class );
        }

        // add icon
        $icon_type = $this->opt( 'icon_type', $type );
        $no_icon_for_img = $this->opt( 'no_icon_for_img', $type );
        $has_img = preg_match( '/<img([^>]*)>/is', $link->getContent() );

        if ( $icon_type && ! ( $has_img && $no_icon_for_img ) ) {
            if ( 'dashicon' === $icon_type ) {
                $dashicon = $this->opt( 'dashicon', $type );
                $icon = '<i class="dashicons-before '. $dashicon .'"></i>';
            } else if ( 'fontawesome' === $icon_type ) {
                $fa = $this->opt( 'fontawesome', $type );
                $icon = '<i class="fa '. $fa .'" aria-hidden="true"></i>';
            }

            if ( 'left' === $this->opt( 'icon_position', $type ) ) {
                $new_content = $icon .' '. $link->getContent();
            } else {
                $new_content = $link->getContent() .' '. $icon;
            }

            $link->setContent( $new_content );
        }
    }

    /**
     * Create html link
     * @param string $label
     * @param array $atts
     * @return string
     */
    protected function get_created_link_2( $label, array $atts )
    {
        $link = new WPEL_Link_2( 'a', $atts, $label );

        if ( $link->is_ignore() ) {
            return false;
        }

        $url = $link->get_attr( 'href' );

        $excludes_as_internal_links = $this->opt( 'excludes_as_internal_links' );

        // exceptions
        $is_included = $link->is_external() || $this->is_included_url( $url );
        $is_excluded = $link->is_excluded() || $this->is_excluded_url( $url );

        // is internal or external
        $is_internal = ( $link->is_internal() || $this->is_internal_url( $url ) ) || ( $is_excluded && $excludes_as_internal_links );
        $is_external = ( $link->is_external() || $is_included ) || ( ! $is_internal && ! $is_excluded );

        if ( $is_external ) {
            $link->set_external();
            $this->apply_link_settings_2( $link, 'external-links' );
        } else if ( $is_internal ) {
            $link->set_internal();
            $this->apply_link_settings_2( $link, 'internal-links' );
        } else {
            $link->set_excluded();
        }

        /**
         * Filters for changing link object
         * @param WPEL_Link $link
         * @return string | WPEL_Link | void
         */
        $return_filters = apply_filters( 'wpel_link', $link );

        if ( $return_filters ) {
            if ( $return_filters instanceof WPEL_Link ) {
                $link_html = $return_filters->get_html();
            } else {
                $link_html = $return_filters;
            }
        } else {
            $link_html = $link->get_html();
        }

        return $link_html;
    }

    /**
     * @param WPEL_Link $link
     * @param string $type
     */
    protected function apply_link_settings_2( WPEL_Link_2 $link, $type )
    {
        if ( ! $this->opt( 'apply_settings', $type ) ) {
            return;
        }

        // set target
        $target = $this->opt( 'target', $type );
        $target_overwrite = $this->opt( 'target_overwrite', $type );
        $has_target = $link->has_attr( 'target' );

        if ( $target && ( ! $has_target || $target_overwrite ) ) {
            $link->set_attr( 'target', $target );
        }

        // add "follow" / "nofollow"
        $follow = $this->opt( 'rel_follow', $type );
        $follow_overwrite = $this->opt( 'rel_follow_overwrite', $type );
        $has_follow = $link->has_attr_value( 'rel', 'follow' ) || $link->has_attr_value( 'rel', 'nofollow' );

        if ( $follow && ( ! $has_follow || $follow_overwrite ) ) {
            if ( $has_follow ) {
                $link->remove_from_attr( 'rel', 'follow' );
                $link->remove_from_attr( 'rel', 'nofollow' );
            }

            $link->add_to_attr( 'rel', $follow );
        }

        // add "external"
        if ( $this->opt( 'rel_external', $type ) ) {
            $link->add_to_attr( 'rel', 'external' );
        }

        // add "noopener"
        if ( $this->opt( 'rel_noopener', $type ) ) {
            $link->add_to_attr( 'rel', 'noopener' );
        }

        // add "noreferrer"
        if ( $this->opt( 'rel_noreferrer', $type ) ) {
            $link->add_to_attr( 'rel', 'noreferrer' );
        }

        // set title
        $title_format = $this->opt( 'title', $type );

        if ( $title_format ) {
            $title = $link->get_attr( 'title' );
            $text = $link->get_content();
            $new_title = str_replace( array( '{title}', '{text}' ), array( $title, $text ), $title_format );
    
            if ( $new_title ) {
                $link->set_attr( 'title', $new_title );
            }
        }

        // add classes
        $class = $this->opt( 'class', $type );

        if ( $class ) {
            $link->add_to_attr( 'class', $class );
        }

        // add icon
        $icon_type = $this->opt( 'icon_type', $type );
        $no_icon_for_img = $this->opt( 'no_icon_for_img', $type );
        $has_img = preg_match( '/<img([^>]*)>/is', $link->get_content() );

        if ( $icon_type && ! ( $has_img && $no_icon_for_img ) ) {
            if ( 'dashicon' === $icon_type ) {
                $dashicon = $this->opt( 'dashicon', $type );
                $icon = '<i class="dashicons-before '. $dashicon .'"></i>';
            } else if ( 'fontawesome' === $icon_type ) {
                $fa = $this->opt( 'fontawesome', $type );
                $icon = '<i class="fa '. $fa .'" aria-hidden="true"></i>';
            }

            if ( 'left' === $this->opt( 'icon_position', $type ) ) {
                $new_content = $icon .' '. $link->get_content();
            } else {
                $new_content = $link->get_content() .' '. $icon;
            }

            $link->set_content( $new_content );
        }
    }

    /**
     * Check if url is included as external link
     * @param string $url
     * @return boolean
     */
    protected function is_included_url( $url )
    {
        $include_urls_str = $this->opt( 'include_urls' );
        $include_urls_arr = explode( "\n", $include_urls_str );

        foreach ( $include_urls_arr as $include_url ) {
			if ( false !== strrpos( $url, $include_url ) ) {
				return true;
            }
        }

        return false;
    }

    /**
     * Check if url is excluded as external link
     * @param string $url
     * @return boolean
     */
    protected function is_excluded_url( $url )
    {
        $exclude_urls_str = $this->opt( 'exclude_urls' );
        $exclude_urls_arr = explode( "\n", $exclude_urls_str );

        foreach ( $exclude_urls_arr as $exclude_url ) {
			if ( false !== strrpos( $url, $exclude_url ) ) {
				return true;
            }
        }

        return false;
    }

    /**
     * Check url is internal
     * @param string $url
     * @return boolean
     */
    protected function is_internal_url( $url )
    {
        // all relative url's are internal
        if ( substr( $url, 0, 7 ) !== 'http://'
                && substr( $url, 0, 8 ) !== 'https://'
                && substr( $url, 0, 6 ) !== 'ftp://'
                && substr( $url, 0, 2 ) !== '//' ) {
            return true;
        }

        // is internal
        if ( false !== strpos( $url, home_url() ) ) {
            return true;
        }

        return false;
    }

}

/*?>*/
