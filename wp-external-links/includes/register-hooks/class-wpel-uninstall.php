<?php
/**
 * Class WPEL_Uninstall
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.2.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Uninstall extends FWP_Register_Hook_Base_1x0x0
{

    /**
     * @var string
     */
    protected $hook_type = 'uninstall';

    /**
     * Activate network
     * @return void
     */
    protected function network_procedure()
    {
        // network settings
        delete_site_option( 'wpel-network-settings' );
        delete_site_option( 'wpel-network-admin-settings' );
    }

    /**
     * Activate site
     * @return void
     */
    protected function site_procedure()
    {
        // delete options
        delete_option( 'wpel-external-link-settings' );
        delete_option( 'wpel-internal-link-settings' );
        delete_option( 'wpel-excluded-link-settings' );
        delete_option( 'wpel-exceptions-settings' );
        delete_option( 'wpel-admin-settings' );

        delete_option( 'wpel-version' );
        delete_option( 'wpel-show-notice' );
    }

}

/*?>*/
