<?php
/**
 * Class WPEL_RegisterHooks
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Registerhooks extends WPEL_Base
{

    /**
     * Initialize
     */
    protected function init()
    {
        register_activation_hook(
            self::get_plugin_file()
            , $this->get_callback( 'activate' )
        );

        register_deactivation_hook(
            self::get_plugin_file()
            , $this->get_callback( 'deactivate' )
        );

        register_uninstall_hook(
            self::get_plugin_file()
            , $this->get_callback( 'uninstall' )
        );
    }

    /**
     * Plugin activation procedure
     */
    protected function activate()
    {
        error_log( 'WPEL Demo Activate!' );
    }

    /**
     * Plugin deactivation procedure
     */
    protected function deactivate()
    {
        error_log( 'WPEL Demo Deactivate!' );
    }

    /**
     * Plugin uninstall procedure
     */
    protected function uninstall()
    {
        error_log( 'WPEL Demo Uninstall!' );
    }

}

/*?>*/
