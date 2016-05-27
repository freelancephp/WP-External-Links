<?php
/**
 * Class Widget_Output_0x7x0
 *
 * This component was inspired by:
 *   @todo
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
class WP_Widget_Output_0x7x0 extends WPRun_Base_0x7x0
{

    const FILTER_NAME = 'widget_output';

    /**
     * Filter for "dynamic_sidebar_params"
     *
     * @global array $wp_registered_widgets
     * @param  array $sidebar_params
     * @return array
     */
    protected function filter_dynamic_sidebar_params( $sidebar_params )
    {
         global $wp_registered_widgets;

        if ( is_admin() ) {
            return $sidebar_params;
        }

        $widget_id = $sidebar_params[ 0 ][ 'widget_id' ];

        // prevent overwriting when already set by another version of the widget output class
        if ( isset( $wp_registered_widgets[ $widget_id ][ '_wo_original_callback' ] ) ) {
            return $sidebar_params;
        }

        $wp_registered_widgets[ $widget_id ][ '_wo_original_callback' ] = $wp_registered_widgets[ $widget_id ][ 'callback' ];
        $wp_registered_widgets[ $widget_id ][ 'callback' ] = $this->get_callback( 'widget_callback' );

        return $sidebar_params;
    }

    /**
     * Widget Callback
     * @global array $wp_registered_widgets
     * @return void
     */
    protected function widget_callback()
    {
        global $wp_registered_widgets;

        $original_callback_params = func_get_args();
        $widget_id = $original_callback_params[ 0 ][ 'widget_id' ];

        $original_callback = $wp_registered_widgets[ $widget_id ][ '_wo_original_callback' ];
        $wp_registered_widgets[ $widget_id ][ 'callback' ] = $original_callback;

        $widget_id_base = $wp_registered_widgets[ $widget_id ][ 'callback' ][ 0 ]->id_base;

        if ( !is_callable( $original_callback ) ) {
            return;
        }

        ob_start();

        call_user_func_array( $original_callback, $original_callback_params );
        $widget_output = ob_get_clean();

        echo apply_filters( self::FILTER_NAME, $widget_output, $widget_id_base, $widget_id );
    }

}

/*?>*/
