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

    protected function init()
    {
    }

    protected function action_wp_head()
    {
        global $post;
        debug( $post->ID );
//        debug( $post );
$t = wp_get_post_categories ($post->ID);
debug($t);

    }

    /**
     * @param string $content
     * @return string
     */
    protected function filter_final_output( $content )
    {
        // skip post id's

		$content = preg_replace_callback( '/<a[^A-Za-z](.*?)>(.*?)<\/a[\s+]*>/is', $this->get_callback( 'match_link' ), $content );
        return $content;
    }

    /**
     *
     * @param array $matches  [ 0 ] => link, [ 1 ] => atts_string, [ 2 ] => label
     * @return string
     */
    protected function match_link( $matches )
    {
        $atts = shortcode_parse_atts( $matches[ 1 ] );
        $label = $matches[ 2 ];

        return $this->get_created_link( $label, $atts );
    }

    /**
     *
     * @param string $label
     * @param array $atts
     * @return string
     */
    protected function get_created_link( $label, array $atts )
    {
        $link = new WP_Link( $atts, $label );

        // check external
        $url = $link->get_attr( 'href' );

        // excluded url's
        // internal url's as external

        // subdomain as internal
        // excluded as internal
        

        // set target
        $link->set_attr( 'target', '_blank' );

        // add nofollow
        $link->add_to_attr( 'rel', 'nofollow' );

        // add external
        $link->add_to_attr( 'rel', 'external' );

        // add classes
        $link->add_to_attr( 'class', 'wpel-link' );

        // set title
        $link->set_attr( 'title', 'Whatever' );

        // add icon


        $link_html = $link->get_html();
        
       // apply custom filter
        $link_html = apply_filters( 'wpel_external_link', $link_html, $link );

        return $link_html;
    }

}


/*?>*/
