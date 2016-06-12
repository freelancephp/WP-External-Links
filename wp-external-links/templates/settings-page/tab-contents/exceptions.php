<?php
/**
 * Tab Exception
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
?>

<p class="description"><?php _e( 'The data-attribute <a href="#" data-wpel-help="data-attributes"><code>data-wpel-link</code></a> can be set on individual links to treat them as internal, external or excluded, or to completely ignore links form being processed by this plugin.' ); ?></p>

<?php submit_button(); ?>
