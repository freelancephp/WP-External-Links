<?php
/**
 * Class WPEL_Textdomain
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.0.3
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class WPEL_Textdomain extends WPRun_Base_1x0x0
{

    /**
     * Action for "plugins_loaded"
     */
    protected function action_plugins_loaded()
    {
        load_plugin_textdomain( 'wpel', false, WPEL_Plugin::get_plugin_dir( '/languages/' )  );
    }

}

/*?>*/
