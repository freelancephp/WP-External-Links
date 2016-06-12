<?php
/**
 * Help Tab: Data Attributes
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.1.1
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
?>
<h3><?php _e( 'Exclude or include by data-attribute', 'wp-external-links' ) ?></h3>
<p>
    <?php _e( 'The <code>data-wpel-link</code> attribute can be set on links and forces the plugin to treat those links that way.', 'wp-external-links' ); ?>
</p>
<ul>
    <li><?php _e( 'Links with <code>data-wpel-link="internal"</code> will be treated as internal links.', 'wp-external-links' ); ?></li>
    <li><?php _e( 'Links with <code>data-wpel-link="external"</code> will be treated as external links.', 'wp-external-links' ); ?></li>
    <li><?php _e( 'Links with <code>data-wpel-link="exclude"</code> will be treated as excluded links (which have their own settings or will be treated as internal links).', 'wp-external-links' ); ?></li>
    <li><?php _e( 'Links with <code>data-wpel-link="ignore"</code> will be completely ignored by this plugin.', 'wp-external-links' ); ?></li>
</ul>
