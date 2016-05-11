<?php
/**
 * Class WPEL_Textdomain
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class WPEL_Textdomain extends WPEL_Base
{

    /**
     * Action for "plugins_loaded"
     */
    protected function action_plugins_loaded()
    {
        load_plugin_textdomain( 'wpel', false, self::get_plugin_dir( '/languages/' )  );
    }

}

/*?>*/
