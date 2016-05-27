<?php
/**
 * Class WP_Debug_0x7x0
 *
 * @package  WPEL_Base
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class WP_Debug_0x7x0 extends WPRun_Base_0x7x0
{

    /**
     * @var array
     */
    private $settings = array(
        'debug_func_name' => 'debug',
        'log_hooks'       => false,
    );

    /**
     * @var array
     */
    private static $benchmarks = array();

    /**
     * Initialize
     * @param array $settings Optional
     */
    protected function init( array $settings = array() )
    {
        $this->settings = wp_parse_args( $settings, $this->settings );

        $this->create_func();

        if ($this->settings[ 'log_hooks' ] ) {
            register_shutdown_function( $this->get_callback( 'log_hooks' ) );
        }
    }

    /**
     * Create logbal debug function
     * @return void
     */
    private function create_func()
    {
        $func = $this->settings[ 'debug_func_name' ];

        if ( function_exists( $func ) || !is_callable( $func, true ) ) {
            return;
        }

        eval( 'function '. $func .'( $entry, $title = "" ) { WP_Debug_0x7x0::log( $entry, $title ); }' );
    }

    /**
     * @param mixed $entry
     */
    public static function log( $entry, $title = '' )
    {
        $content = '';

        if ( !empty($title) ) {
            $content = $title . ': ';
        }

        $content .= var_export( $entry, true );

        error_log( $content );
    }

    /**
     * Log all hooks being applied
     * @global type $merged_filters
     */
    protected function log_hooks()
    {
        global $merged_filters;

        self::log( $merged_filters, 'WP Hooks' );
    }

    /**
     * 
     */
    public static function start_benchmark( $label = 'benchmark' )
    {
        self::$benchmarks[ $label ][ 'start' ] = microtime( true );
    }

    /**
     * 
     */
    public static function end_benchmark( $label = 'benchmark' )
    {
        $end_time = microtime( true );
        self::$benchmarks[ $label ][ 'end' ] = $end_time;
        $start_time = self::$benchmarks[ $label ][ 'start' ];

        $total_time = $end_time - $start_time;

        self::log( $total_time, $label );

        return $total_time;
    }

}

/*?>*/
