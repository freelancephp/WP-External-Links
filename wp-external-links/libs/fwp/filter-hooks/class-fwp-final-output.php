<?php
/**
 * Class FWP_Final_Output_1x0x0
 *
 * @package  FWP
 * @category WordPress Library
 * @version  1.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WPRun-WordPress-Development
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class FWP_Final_Output_1x0x0 extends WPRun_Base_1x0x0
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
