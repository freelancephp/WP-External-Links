<?php
/**
 * Class WPEL_Base
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
abstract class WPEL_Base extends WPRun_Base_0x7x0
{

    /**
     * @var string
     */
    private static $plugin_file = null;

    /**
     * @var string
     */
    private static $plugin_dir = null;

    /**
     * @param string $plugin_file
     */
    public static function set_plugin_file( $plugin_file )
    {
        self::$plugin_file = $plugin_file;
    }

    /**
     * @return string
     */
    public static function get_plugin_file()
    {
        return self::$plugin_file;
    }

    /**
     * @param string $plugin_dir
     */
    public static function set_plugin_dir( $plugin_dir )
    {
        self::$plugin_dir = untrailingslashit( $plugin_dir );
    }

    /**
     * @param string $path Optional
     * @return string
     */
    public static function get_plugin_dir( $path = '' )
    {
        return self::$plugin_dir . $path;
    }

}

/*?>*/
