<?php
/**
 * Tab Admin
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 *
 * @var array $vars
 *      @option array  "tabs"
 *      @option string "current_tab"
 */

$default_fields_file = WPEL_Plugin::get_plugin_dir( '/templates/partials/tab-contents/fields-default.php' );
WPEL_Plugin::show_template( $default_fields_file, $vars );

submit_button();
