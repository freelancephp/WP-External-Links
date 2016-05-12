<?php
/**
 * Admin Settings
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 *
 * @var array $vars
 *      @option string "current_tab"
 */

$page_url = admin_url() .'/admin.php?page='. $vars[ 'menu_slug' ];
$set_tab_active_class = function ( $tab ) use ( $vars ) {
    if ( $tab === $vars[ 'current_tab' ] ) {
        echo ' nav-tab-active';
    }
};
?>
<div class="wrap">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <?php settings_errors(); ?>

    <h2 class="nav-tab-wrapper">
        <a class="nav-tab<?php $set_tab_active_class( '1' ); ?>" href="<?php echo $page_url; ?>&tab=1">
            <i class="dashicons-before dashicons-external"></i> <?php _e( 'External Links', 'wpel' ); ?>
        </a>
        <a class="nav-tab<?php $set_tab_active_class( '2' ); ?>" href="<?php echo $page_url; ?>&tab=2">
            <i class="dashicons-before dashicons-format-image"></i> <?php _e( 'Icon Settings', 'wpel' ); ?>
        </a>
        <a class="nav-tab<?php $set_tab_active_class( '3' ); ?>" href="<?php echo $page_url; ?>&tab=3">
            <i class="dashicons-before dashicons-dismiss"></i> <?php _e( 'Exceptions', 'wpel' ); ?>
        </a>
        <a class="nav-tab<?php $set_tab_active_class( '4' ); ?>" href="<?php echo $page_url; ?>&tab=4">
            <i class="dashicons-before dashicons-image-rotate"></i> <?php _e( 'Internal Links', 'wpel' ); ?>
        </a>
    </h2>

    <form method="post" action="options.php">
        <?php
            settings_fields( $vars[ 'option_group' ] );
            do_settings_sections( $vars[ 'settings_page' ] );
        ?>

        <?php if ( $vars[ 'current_tab' ] === '3' ): ?>
        <p class="description">
            <?php _e( 'Note: All links containing the <code>data</code>-attribute <code>data-wpel-exclude</code> will also be excluded.', 'wpel' ); ?>
        </p>
        <?php endif; ?>

        <?php submit_button(); ?>
    </form>
</div>
