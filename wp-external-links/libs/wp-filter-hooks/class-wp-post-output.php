<?php
/**
 * Class WP_Post_Output_0x7x0
 *
 * @package  WP Filter Hooks
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class WP_Post_Output_0x7x0 extends WPRun_Base_0x7x0
{

    const FILTER_NAME = 'post_output';

    /**
     * Action for "init"
     */
    protected function action_init()
    {
        add_filter( 'the_title', $this->get_callback( 'apply' ), 1000000 );
        add_filter( 'the_content', $this->get_callback( 'apply' ), 1000000 );
        add_filter( 'the_excerpt', $this->get_callback( 'apply' ), 1000000 );
        add_filter( 'get_the_excerpt', $this->get_callback( 'apply' ), 1000000 );
    }

    /**
     * Apply filters
     * @param string $content
     * @return string
     */
    protected function apply( $content )
    {
        $filtered_content = apply_filters( self::FILTER_NAME, $content );

        // remove filters after applying to prevent multiple applies
        remove_all_filters( self::FILTER_NAME );

        return $filtered_content;
    }

}

/*?>*/
