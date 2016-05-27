<?php
/**
 * Tab Default Content
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */

$current_tab = $vars [ 'current_tab' ];
$tab_values = $vars[ 'tabs' ][ $current_tab ];
$fields = $tab_values[ 'fields' ];

settings_fields( $fields->get_setting( 'option_group' ) );
do_settings_sections( $fields->get_setting( 'page_id' ) );

submit_button();
?>
