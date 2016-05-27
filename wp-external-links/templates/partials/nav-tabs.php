<?php
/**
 * Tab Nav
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
 *      @option array  "tabs"
 *      @option string "current_tab"
 *      @option string "page_url"
 */
$set_tab_active_class = function ( $tab ) use ( $vars ) {
    if ( $tab === $vars[ 'current_tab' ] ) {
        echo ' nav-tab-active';
    }
};
?>
<h2 class="nav-tab-wrapper">
    <?php foreach ( $vars[ 'tabs' ] as $tab_key => $tab_values ): ?>
        <a class="nav-tab<?php $set_tab_active_class( $tab_key ); ?>" href="<?php echo $vars[ 'page_url' ]; ?>&tab=<?php echo $tab_key; ?>">
            <?php echo $tab_values[ 'icon' ]; ?> <?php echo $tab_values[ 'title' ]; ?>
        </a>
    <?php endforeach; ?>
</h2>
