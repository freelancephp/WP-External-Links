<?php
/**
 * Tabs
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
 *      @option string "action"
 */
?>
<div class="wrap">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <?php settings_errors(); ?>

    <h2 class="nav-tab-wrapper">
        <a class="nav-tab<?php if ( '1' === $vars[ 'action' ] ) echo ' nav-tab-active'; ?>" href="<?php echo admin_url() ?>/admin.php?page=wprun-admin-tabs-page&action=1"><i class="dashicons-before dashicons-smiley"></i> Tab 1</a>
        <a class="nav-tab<?php if ( '2' === $vars[ 'action' ] ) echo ' nav-tab-active'; ?>" href="<?php echo admin_url() ?>/admin.php?page=wprun-admin-tabs-page&action=2"><i class="dashicons-before dashicons-smiley"></i> Tab 2</a>
        <a class="nav-tab<?php if ( '3' === $vars[ 'action' ] ) echo ' nav-tab-active'; ?>" href="<?php echo admin_url() ?>/admin.php?page=wprun-admin-tabs-page&action=3"><i class="dashicons-before dashicons-smiley"></i> Tab 3</a>
    </h2>

<form method="post" action="options.php">
<?php
    settings_fields( 'wprun-admin-options-section-'. $vars[ 'action' ] );
    do_settings_sections( 'section-'. $vars[ 'action' ] );

    submit_button();
?>
</form>

</div>
