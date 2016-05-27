<?php
/**
 * Tab Support
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
?>
<h2>Support</h2>

<h3>Documentation</h3>
<p>Take a look at the help section for documentation (see "help" on top-left of the page)</p>

<h3>FAQ</h3>
<p>On the <a href="https://wordpress.org/plugins/wp-external-links/faq/" target="_blank">FAQ page</a> you can find some additional tips & trics.</p>

<h3>Reported issues</h3>
<p>When you experience problems using this plugin please look if your problem was <a href="https://wordpress.org/support/plugin/wp-external-links" target="_blank">already reported</a>.</p>

<h3>Send your issue</h3>
<p>If not then you can report it <a href="https://wordpress.org/support/plugin/wp-external-links#postform" target="_blank">here</a>.
    Please include your plugin settings within your post:</p>

<p>
    <label for="plugin-settings"><?php _e( 'Your WPEL Plugin Settings:', 'wpel' ); ?></label>
<textarea id="plugin-settings" class="large-text js-wpel-copy-target" rows="8" readonly="readonly">
WP url:  <?php bloginfo( 'wpurl' ); ?>

WP version:  <?php bloginfo( 'version' ); ?>

PHP version:  <?php echo phpversion(); ?>


WPEL plugin settings:
array(
<?php
foreach ( $vars[ 'tabs' ] as $tab_key => $values ) {
    if ( ! isset( $values[ 'fields' ] ) ) {
        continue;
    }

    $option_values = $values[ 'fields' ]->get_option_values();
    $option_name = $values[ 'fields' ]->get_setting( 'option_name' );

    echo "'$option_name' => ";
    var_export( $option_values );
    echo ",\n";
}
?>
);
</textarea>
    <button class="button js-wpel-copy"><?php _e( 'Copy Plugin Settings' ); ?></button>
</p>
