<?php
/**
 * Help Tab: Data Attributes
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.0.4
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
?>
<h3><?php _e( 'Exclude or include by data-attribute', 'wpel' ) ?></h3>
<p>
    <?php _e( 'The <code>data-wpel-link</code> attribute can be set on links and forces the plugin to treat those links that way.', 'wpel' ); ?>
</p>
<ul>
    <li><?php _e( 'Links with <code>data-wpel-link="internal"</code> will be treated as internal links.', 'wpel' ); ?></li>
    <li><?php _e( 'Links with <code>data-wpel-link="external"</code> will be treated as external links.', 'wpel' ); ?></li>
    <li><?php _e( 'Links with <code>data-wpel-link="exclude"</code> will be treated as excluded links (which optionally can be treated as internal links).', 'wpel' ); ?></li>
    <li><?php _e( 'Links with <code>data-wpel-link="ignore"</code> will be completely ignored by this plugin.', 'wpel' ); ?></li>
</ul>
