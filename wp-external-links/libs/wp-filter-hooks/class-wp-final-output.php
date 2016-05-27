<?php
/**
 * Class WP_Final_Output_0x7x0
 *
 * @todo multiple versions - multiple filter applies
 *
 * @package  WP Filter Hooks
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class WP_Final_Output_0x7x0 extends WPRun_Base_0x7x0
{

    const FILTER_NAME = 'final_output';

    /**
     * Action for "init"
     */
    protected function action_init()
    {
        ob_start( $this->get_callback( 'apply' ) );
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
