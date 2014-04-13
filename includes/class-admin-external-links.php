<?php defined( 'ABSPATH' ) OR die( 'No direct access.' );
if ( ! class_exists( 'Admin_External_Links' ) ):

/**
 * Class Admin_External_Links
 * @category WordPress Plugins
 */
final class Admin_External_Links {

	/**
	 * Options to be saved and their default values
	 * @var array
	 */
	public $save_options = array(
		'meta' => array(
			'version' => NULL,
		),
		'main' => array(
			'target' => '_none',
			'filter_page' => 1,
			'filter_posts' => 1,
			'filter_comments' => 1,
			'filter_widgets' => 1,
			'ignore' => '//twitter.com/share',
		),
		'seo' => array(
			'external' => 1,
			'nofollow' => 1,
			'title' => '%title%',
			'use_js' => 1,
			'load_in_footer' => 1,
		),
		'style' => array(
			'class_name' => 'ext-link',
			'icon' => 0,
			'image_no_icon' => 1,
			'no_icon_class' => 'no-ext-icon',
			'no_icon_same_window' => 0,
		),
		'extra' => array(
			'fix_js' => 0,
			'phpquery' => 0,
			'filter_excl_sel' => '.excl-ext-link',
		),
		'screen' => array(
			'menu_position' => NULL,
		),
	);

	/**
	 * Meta box page object
	 * @var WP_Meta_Box_Page
	 */
	public $meta_box_page = NULL;

	/**
	 * Ajax form object
	 * @var WP_Ajax_Option_Form
	 */
	public $form = NULL;
	static public $staticForm = NULL;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this->check_version_update();

		// set meta box page
		$this->meta_box_page = new WP_Meta_Box_Page_01();

		// set ajax forms (also used by front-end)
		$this->form = new WP_Option_Forms_01( WP_EXTERNAL_LINKS_KEY, $this->save_options );
		self::$staticForm = $this->form;

		// init admin
		add_action( 'admin_init', array( $this, 'admin_init' ) );

		if ( is_admin() ) {
			// set options for add_page_method
			$menu_pos = $this->form->set_current_option( 'screen' )->value( 'menu_position' );

			// init meta box page
			$this->meta_box_page->init(
				// settings
				array(
					'page_title' => $this->__( 'WP External Links' ),
					'menu_title' => $this->__( 'External Links' ),
					'page_slug' => strtolower( WP_EXTERNAL_LINKS_KEY ),
					'add_page_method' => ( ! empty( $menu_pos ) AND $menu_pos != 'admin.php' ) ? 'add_submenu_page' : 'add_menu_page',
					'parent_slug' => ( ! empty( $menu_pos ) AND $menu_pos != 'admin.php' ) ? $menu_pos : NULL,
					'column_widths' => array(
						1 => array( 99 ),
						2 => array( 69, 29 ),
					),
					'icon_url' => plugins_url( 'images/icon-wp-external-links-16.png', WP_EXTERNAL_LINKS_FILE ),
				),
				// load callback
				array( $this, 'call_load_meta_box' )
			);
		}
	}

	/**
	 * Initialize Admin
	 */
	public function admin_init() {
		// set uninstall hook
		register_uninstall_hook( WP_EXTERNAL_LINKS_FILE, array( 'Admin_External_Links', 'call_uninstall' ) );

		// load text domain for translations
		load_plugin_textdomain( WP_EXTERNAL_LINKS_KEY, FALSE, dirname( plugin_basename( WP_EXTERNAL_LINKS_FILE ) ) . '/lang/' );
	}

	/**
	 * Add to head of Admin page
	 */
	public function admin_head() {
            echo <<< style
<style type="text/css">
/* WP External Links */
.postbox-container { margin-left:1%; }
.tooltip-help { text-decoration: none; }
.tipsy { padding: 5px; }
.tipsy-inner { padding: 5px 8px 4px 8px; color: white; max-width: 200px; text-align: center; text-shadow: 0 -1px 0 #333;
	border-top:1px solid #808080; border-botom:1px solid #6d6d6d; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;
	background-color:#777; background-image:-ms-linear-gradient(bottom,#6d6d6d,#808080); background-image:-moz-linear-gradient(bottom,#6d6d6d,#808080); background-image:-o-linear-gradient(bottom,#6d6d6d,#808080); background-image:-webkit-gradient(linear,left bottom,left top,from(#6d6d6d),to(#808080)); background-image:-webkit-linear-gradient(bottom,#6d6d6d,#808080); background-image:linear-gradient(bottom,#6d6d6d,#808080);
}
.tipsy-north { background-position: top center; }
.tipsy-south { background-position: bottom center; }
.tipsy-east { background-position: right center; }
.tipsy-west { background-position: center bottom; }
<style>
style;
	}

	/**
	 * Translate text in current domain
	 * @param string $text
	 * @return string
	 */
	public function __( $text ) {
		return translate( $text, WP_EXTERNAL_LINKS_KEY );
	}

	/**
	 * Translate text in current domain
	 * @param string $text
	 * @return string
	 */
	public function _e( $text ) {
		echo translate( $text, WP_EXTERNAL_LINKS_KEY );
	}

	/**
	 * Load meta box action
	 */
	public function call_load_meta_box( $meta_box ) {
        add_action( 'admin_head', array($this, 'admin_head') );

		// add filters
		$meta_box->add_title_filter( array( $this, 'call_page_title' ) )
							->add_contextual_help_filter( array( $this, 'call_contextual_help' ) );

		// add meta boxes
		// add_meta_box( $title, $callback, $context = 'normal', $id = NULL, $priority = 'default', $callback_args = NULL )
		$meta_box->add_meta_box( $this->__( 'General Settings' ), array( $this, 'call_box_general_settings' ), 1 )
							->add_meta_box( $this->__( 'SEO Settings' ), array( $this, 'call_box_seo_settings' ), 1 )
							->add_meta_box( $this->__( 'Style Settings' ), array( $this, 'call_box_style_settings' ), 1 )
							->add_meta_box( $this->__( 'Extra Settings' ), array( $this, 'call_box_extra_settings' ), 1 )
							->add_meta_box( $this->__( 'Admin Settings' ), array( $this, 'call_box_admin_settings' ), 1 )
							//->add_meta_box( $this->__( 'About this Plugin' ), array( $this, 'call_box_about' ), 2 )
							->add_meta_box( $this->__( 'Other Plugins' ), array( $this, 'call_box_other_plugins' ), 2 );

		// scripts
		wp_enqueue_script( 'admin-wp-external-links', plugins_url( '/js/admin-wp-external-links.js', WP_EXTERNAL_LINKS_FILE ), array( 'postbox' ), WP_EXTERNAL_LINKS_VERSION );
	}

	/**
	 * Contextual_help (callback)
	 * @param string $content
	 * @return string
	 */
	public function call_contextual_help( $content ) {
		$help = '';
		$help .= $this->meta_box_page->get_ob_callback( array( $this, 'call_box_about' ) );
		return $help . $content;
	}

	/**
	 * Add icon to page title
	 * @return string
	 */
	public function call_page_title( $title ) {
		// when updated set the update message
		if ( isset($_GET[ 'settings-updated' ]) && $_GET[ 'settings-updated' ] == 'true' ) {
			$title .= '<div class="updated settings-error" id="setting-error-settings_updated">'
				. '<p><strong>' . __( 'Settings saved.' ) .'</strong></p>'
				. '</div>';
		}

		$title = '<div class="icon32" id="icon-options-custom" style="background:url( '. plugins_url( 'images/icon-wp-external-links-32.png', WP_EXTERNAL_LINKS_FILE ) .' ) no-repeat 50% 50%"><br></div>'
				. $title;

		return $title;
	}

	/**
	 * Meta Box: General Settings
	 */
	public function call_box_general_settings() {
		echo $this->form->set_current_option( 'main' )->open_form();
?>
		<fieldset class="options">
			<table class="form-table">
			<tr>
				<th style="width:250px;"><?php $this->_e( 'Open external links in...' ) ?>
						<?php echo $this->tooltip_help( 'Specify the target (window or tab) for opening external links.' ) ?></th>
				<td class="target_external_links">
					<label><?php echo $this->form->radio( 'target', '_none', array( 'class' => 'field_target' ) ); ?>
						<span><?php $this->_e( 'Same window or tab (<code>_none</code>)' ) ?></span></label>
						<?php echo $this->tooltip_help( 'Open in current window or tab, when framed in the same frame.' ) ?>
					<br/>
					<label><?php echo $this->form->radio( 'target', '_blank', array( 'class' => 'field_target' ) ); ?>
						<span><?php $this->_e( 'New window or tab (<code>_blank</code>)' ) ?></span></label>
						<?php echo $this->tooltip_help( 'Open every external link in a new window or tab.' ) ?>
					<br  style="margin-bottom:15px;" />
					<label><?php echo $this->form->radio( 'target', '_top', array( 'class' => 'field_target' ) ); ?>
						<span><?php $this->_e( 'Topmost frame (<code>_top</code>)' ) ?></span></label>
						<?php echo $this->tooltip_help( 'Open in current window or tab, when framed in the topmost frame.' ) ?>
					<br/>
					<label><?php echo $this->form->radio( 'target', '_new', array( 'class' => 'field_target' ) ); ?>
						<span><?php $this->_e( 'Seperate window or tab (<code>_new</code>)' ) ?></span></label>
						<?php echo $this->tooltip_help( 'Open new window the first time and use this window for each external link.' ) ?>
				</td>
			</tr>
			<tr>
				<th style="width:250px;"><?php $this->_e( 'Apply plugin settings on...' ) ?>
						<?php echo $this->tooltip_help( 'Choose contents for applying settings to external links.' ) ?></th>
				<td>
					<label><?php echo $this->form->checkbox( 'filter_page', 1 ); ?>
					<span><?php $this->_e( 'All contents' ) ?></span> <span class="description"><?php $this->_e('(the whole <code>&lt;body&gt;</code>)') ?></span></label>
					<br/>&nbsp;&nbsp;<label><?php echo $this->form->checkbox( 'filter_posts', 1 ); ?>
							<span><?php $this->_e( 'Post contents' ) ?></span></label>
					<br/>&nbsp;&nbsp;<label><?php echo $this->form->checkbox( 'filter_comments', 1 ); ?>
							<span><?php $this->_e( 'Comments' ) ?></span></label>
					<br/>&nbsp;&nbsp;<label><?php echo $this->form->checkbox( 'filter_widgets', 1 ); ?>
							<span><?php
								if ( self::check_widget_content_filter() ):
									$this->_e( 'All widgets' );
									echo $this->tooltip_help( 'Applied to all widgets by using the "widget_content" filter of the Widget Logic plugin' );
								else:
									$this->_e( 'All text widgets' );
									echo $this->tooltip_help( 'Only the text widget will be applied. To apply to all widget you should select "All contents" option.' );
								endif;
							?></span></label>
				</td>
			</tr>
			<tr>
				<th><?php $this->_e( 'Ignore links (URL) containing...' ) ?>
					<?php echo $this->tooltip_help( 'This plugin will completely ignore links that contain one of the given texts in the URL. Use enter to seperate each text. This check is not case sensitive.' ) ?></th>
				<td><label><?php echo $this->form->textarea( 'ignore' ); ?>
						<span class="description"><?php _e( 'Be as specific as you want, f.e.: <code>twitter.com</code> or <code>https://twitter.com</code>. Seperate each by an enter.' ) ?></span></label>
				</td>
			</tr>
			</table>
		</fieldset>

<?php
		echo $this->form->submit();
		echo $this->form->close_form();
	}

	/**
	 * Meta Box: SEO Settings
	 */
	public function call_box_seo_settings() {
		echo $this->form->set_current_option( 'seo' )->open_form();
?>
		<fieldset class="options">
			<table class="form-table">
			<tr>
				<th style="width:250px;"><?php $this->_e( 'Add to <code>rel</code>-attribute' ) ?>
						<?php echo $this->tooltip_help( 'Set values for the "rel"-atribute of external links.' ) ?></th>
				<td><label><?php echo $this->form->checkbox( 'nofollow', 1 ); ?>
						<span><?php $this->_e( 'Add <code>"nofollow"</code>' ) ?></span></label>
						<?php echo $this->tooltip_help( 'Add "nofollow" to the "rel"-attribute of external links (unless link already has "follow").' ) ?>
					<br/>
					<label><?php echo $this->form->checkbox( 'external', 1 ); ?>
						<span><?php $this->_e( 'Add <code>"external"</code>' ) ?></span></label>
						<?php echo $this->tooltip_help( 'Add "external" to the "rel"-attribute of external links.' ) ?>
				</td>
			</tr>
			<tr>
				<th><?php $this->_e( 'Set <code>title</code>-attribute' ) ?>
						<?php echo $this->tooltip_help( 'Set title attribute for external links. Use %title% for the original title value.' ) ?></th>
				<td><label><?php echo $this->form->text( 'title' ); ?>
					<br/><span class="description"><?php _e( 'Use <code>%title%</code> for the original title value.' ) ?></span></label></td>
			</tr>
			<tr>
				<th><?php $this->_e( 'Use JavaScript method' ) ?>
					<?php echo $this->tooltip_help( 'Enable this option to use the JavaScript method for opening links, which prevents adding target attribute in the HTML code.' ) ?></label>
				</th>
				<td>
					<label><?php echo $this->form->checkbox( 'use_js', 1, array( 'class' => 'field_use_js' ) ); ?>
					<span><?php $this->_e( 'Use JavaScript for opening links' ) ?></span> <span class="description"><?php $this->_e( '(valid xhtml strict)' ) ?></span>
                    <br/>
					&nbsp;&nbsp;<label><?php echo $this->form->checkbox( 'load_in_footer', 1, array( 'class' => 'load_in_footer' ) ); ?>
					<span><?php $this->_e( 'Load JS file in footer' ) ?></span>
				</td>
			</tr>
			</table>
		</fieldset>
<?php
		echo $this->form->submit();
		echo $this->form->close_form();
	}

	/**
	 * Meta Box: Style Settings
	 */
	public function call_box_style_settings() {
		echo $this->form->set_current_option( 'style' )->open_form();
?>
		<fieldset class="options">
			<table class="form-table">
			<tr>
				<th style="width:250px;"><?php $this->_e( 'Set icon for external link' ) ?>
						<?php echo $this->tooltip_help( 'Set an icon that wll be shown for external links. See example on the right side.' ) ?></th>
				<td>
					<div>
						<div style="width:15%;float:left">
							<label><?php echo $this->form->radio( 'icon', 0 ); ?>
							<span><?php $this->_e( 'No icon' ) ?></span></label>
						<?php for ( $x = 1; $x <= 20; $x++ ): ?>
							<br/>
							<label title="<?php echo sprintf( $this->__( 'Icon %1$s: choose this icon to show for all external links or add the class \'ext-icon-%1$s\' to a specific link.' ), $x ) ?>">
							<?php echo $this->form->radio( 'icon', $x ); ?>
                            <img src="<?php echo plugins_url('images/ext-icons/ext-icon-'. $x .'.png', WP_EXTERNAL_LINKS_FILE)  ?>" /></label>
							<?php if ( $x % 5 == 0 ): ?>
						</div>
						<div style="width:15%;float:left">
							<?php endif; ?>
						<?php endfor; ?>
						</div>
						<div style="width:29%;float:left;"><span class="description"><?php $this->_e( 'Example:' ) ?></span>
							<br/><img src="<?php echo plugins_url( 'images/link-icon-example.png', WP_EXTERNAL_LINKS_FILE ) ?>"	/>
						</div>
						<br style="clear:both" />
					</div>
				</td>
			</tr>
			<tr>
				<th><?php $this->_e( 'Skip images' ) ?>
						<?php echo $this->tooltip_help( 'Don\'t show icon for external links containing images.' ) ?></th>
				<td><label><?php echo $this->form->checkbox( 'image_no_icon', 1 ); ?>
					<span><?php $this->_e( 'No icon for extenal links with images' ) ?></span></label>
				</td>
			</tr>
			<tr>
				<th style="width:250px;"><?php $this->_e( 'Set no-icon class' ) ?>
						<?php echo $this->tooltip_help( 'Set this class for links, that should not have the external link icon.' ) ?></th>
				<td><label><?php echo $this->form->text( 'no_icon_class', array( 'class' => '' ) ); ?></label>
					<br/><label><?php echo $this->form->checkbox( 'no_icon_same_window', 1 ); ?>
					<span><?php $this->_e( 'Always open links with no-icon class in same window or tab' ) ?></span></label>
						<?php echo $this->tooltip_help( 'When enabled external links containing the no-icon class will always be opened in the current window or tab. No matter which target is set.' ) ?>
				</td>
			</tr>
			<tr>
				<th><?php $this->_e( 'Add to <code>class</code>-attribute' ) ?>
						<?php echo $this->tooltip_help( 'Add one or more extra classes to the external links, seperated by a space. It is optional, else just leave field blank.' ) ?></th>
				<td><label><?php echo $this->form->text( 'class_name' ); ?></label></td>
			</tr>
			</table>
		</fieldset>
<?php
		echo $this->form->submit();
		echo $this->form->close_form();
	}

	/**
	 * Meta Box: Extra Settings
	 */
	public function call_box_extra_settings() {
		echo $this->form->set_current_option( 'extra' )->open_form();
?>
		<fieldset class="options">
			<table class="form-table">
			<tr>
				<th style="width:250px;"><?php $this->_e( 'Solving problems' ) ?>
						<?php echo $this->tooltip_help( 'Some options to try when a problem occurs. These options can also cause other problems, so be carefull.' ) ?></th>
				<td><label><?php echo $this->form->checkbox( 'fix_js', 1 ); ?>
						<span><?php $this->_e( 'Replacing <code>&lt;/a&gt;</code> with <code>&lt;\/a&gt;</code> in JavaScript code.' ) ?></span></label>
						<?php echo $this->tooltip_help( 'By replacing </a> with <\/a> in JavaScript code these tags will not be processed by the plugin.' ) ?>
				</td>
			</tr>
			<tr>
				<th style="width:250px;"><?php $this->_e( 'Use phpQuery library' ) ?>
						<?php echo $this->tooltip_help( 'Using phpQuery library for manipulating links. This option is experimental.' ) ?></th>
				<td><label><?php echo $this->form->checkbox( 'phpquery', 1 ); ?>
						<span><?php $this->_e( 'Use phpQuery for parsing document.' ) ?></span>
						<span class="description">(Test it first!)</span></label>
				</td>
			</tr>
			<tr class="filter_excl_sel" <?php echo ( $this->form->value( 'phpquery' ) ) ? '' : 'style="display:none;"'; ?>>
				<th><?php $this->_e( 'Do NOT apply settings on...' ) ?>
					<?php echo $this->tooltip_help( 'The external links of these selection will be excluded for the settings of this plugin. Define the selection by using CSS selectors.' ) ?></th>
				<td><label><?php echo $this->form->textarea( 'filter_excl_sel' ); ?>
						<span class="description"><?php _e( 'Define selection by using CSS selectors, f.e.: <code>.excl-ext-link, .entry-title, #comments-title</code> (look <a href="http://code.google.com/p/phpquery/wiki/Selectors" target="_blank">here</a> for available selectors).' ) ?></span></label>
				</td>
			</tr>
			</table>
		</fieldset>
<?php
		echo $this->form->submit();
		echo $this->form->close_form();
	}

	/**
	 * Meta Box: Extra Settings
	 */
	public function call_box_admin_settings() {
		echo $this->form->set_current_option( 'screen' )->open_form();
?>
		<fieldset class="options">
			<table class="form-table">
				<tr>
					<th><?php $this->_e('Admin menu position') ?>
						<?php echo $this->tooltip_help( 'Change the menu position of this plugin in "Screen Options".' ) ?></th>
					<td><label>
					<?php
						echo $this->form->select( 'menu_position', array(
							'admin.php' => 'Main menu',
							'index.php' => $this->__( 'Subitem of Dashboard' ),
							'edit.php' => $this->__( 'Subitem of Posts' ),
							'upload.php' => $this->__( 'Subitem of Media' ),
							'link-manager.php' => $this->__( 'Subitem of Links' ),
							'edit.php?post_type=page' => $this->__( 'Subitem of Pages' ),
							'edit-comments.php' => $this->__( 'Subitem of Comments' ),
							'themes.php' => $this->__( 'Subitem of Appearance' ),
							'plugins.php' => $this->__( 'Subitem of Plugins' ),
							'users.php' => $this->__( 'Subitem of Users' ),
							'tools.php' => $this->__( 'Subitem of Tools' ),
							'options-general.php' => $this->__( 'Subitem of Settings' ),
						));
					?>
					</label></td>
				</tr>
			</table>
		</fieldset>
<?php
		echo $this->form->submit();
		echo $this->form->close_form();
	}

	/**
	 * Meta Box: About...
	 */
	public function call_box_about() {
?>
		<h4><img src="<?php echo plugins_url( 'images/icon-wp-external-links-16.png', WP_EXTERNAL_LINKS_FILE ) ?>" width="16" height="16" /> <?php $this->_e( 'WP External Links' ) ?></h4>
		<div>
			<p><?php printf( $this->__( 'Current version: <strong>%1$s</strong>' ), WP_EXTERNAL_LINKS_VERSION ) ?></p>
			<p><?php $this->_e( 'Manage external links on your site: open in new window/tab, set link icon, add "external", add "nofollow" and more.' ) ?></p>
			<p><a href="http://www.freelancephp.net/contact/" target="_blank"><?php $this->_e( 'Questions or suggestions?' ) ?></a></p>
			<p><?php $this->_e( 'If you like this plugin please send your rating at WordPress.org.' ) ?></p>
			<p><?php _e( 'More info' ) ?>: <a href="http://wordpress.org/extend/plugins/wp-external-links/" target="_blank">WordPress.org</a> | <a href="http://www.freelancephp.net/wp-external-links-plugin/" target="_blank">FreelancePHP.net</a></p>
		</div>
<?php
	}

	/**
	 * Meta Box: Other Plugins
	 */
	public function call_box_other_plugins() {
?>
		<h4><img src="<?php echo plugins_url( 'images/icon-email-encoder-bundle-16.png', WP_EXTERNAL_LINKS_FILE ); ?>" width="16" height="16" /> Email Encoder Bundle</h4>
		<div>
			<?php if ( is_plugin_active( 'email-encoder-bundle/email-encoder-bundle.php' ) ): ?>
				<p><?php $this->_e( 'This plugin is already activated.' ) ?> <a href="<?php echo get_bloginfo( 'url' ) ?>/wp-admin/options-general.php?page=email-encoder-bundle/email-encoder-bundle.php"><?php $this->_e( 'Settings' ) ?></a></p>
			<?php elseif( file_exists( WP_PLUGIN_DIR . '/email-encoder-bundle/email-encoder-bundle.php' ) ): ?>
				<p><a href="<?php echo get_bloginfo( 'url' ) ?>/wp-admin/plugins.php?plugin_status=inactive"><?php $this->_e( 'Activate this plugin.' ) ?></a></p>
			<?php else: ?>
				<p><a href="<?php echo get_bloginfo( 'url' ) ?>/wp-admin/plugin-install.php?tab=search&type=term&s=Email+Encoder+Bundle+freelancephp&plugin-search-input=Search+Plugins"><?php $this->_e( 'Get this plugin now' ) ?></a></p>
			<?php endif; ?>

			<p><?php $this->_e( 'Protect email addresses on your site from spambots and being used for spamming by using one of the encoding methods.' ) ?></p>
			<p><?php _e( 'More info' ) ?>: <a href="http://wordpress.org/extend/plugins/email-encoder-bundle/" target="_blank">WordPress.org</a> | <a href="http://www.freelancephp.net/email-encoder-php-class-wp-plugin/" target="_blank">FreelancePHP.net</a></p>
		</div>

		<?php echo $this->hr(); ?>

		<h4><img src="<?php echo plugins_url( 'images/icon-wp-mailto-links-16.png', WP_EXTERNAL_LINKS_FILE ); ?>" width="16" height="16" /> WP Mailto Links</h4>
		<div>
			<?php if ( is_plugin_active( 'wp-mailto-links/wp-mailto-links.php' ) ): ?>
				<p><?php $this->_e( 'This plugin is already activated.' ) ?> <a href="<?php echo get_bloginfo( 'url' ) ?>/wp-admin/options-general.php?page=wp-mailto-links/wp-mailto-links.php"><?php $this->_e( 'Settings' ) ?></a></p>
			<?php elseif( file_exists( WP_PLUGIN_DIR . '/wp-mailto-links/wp-mailto-links.php' ) ): ?>
				<p><a href="<?php echo get_bloginfo( 'url' ) ?>/wp-admin/plugins.php?plugin_status=inactive"><?php $this->_e( 'Activate this plugin.' ) ?></a></p>
			<?php else: ?>
				<p><a href="<?php echo get_bloginfo( 'url' ) ?>/wp-admin/plugin-install.php?tab=search&type=term&s=WP+Mailto+Links+freelancephp&plugin-search-input=Search+Plugins"><?php $this->_e( 'Get this plugin now' ) ?></a></p>
			<?php endif; ?>

			<p><?php $this->_e( 'Manage mailto links on your site and protect emails from spambots, set mail icon and more.' ) ?></p>
			<p><?php _e( 'More info' ) ?>: <a href="http://wordpress.org/extend/plugins/wp-mailto-links/" target="_blank">WordPress.org</a> | <a href="http://www.freelancephp.net/wp-mailto-links-plugin/" target="_blank">FreelancePHP.net</a></p>
		</div>
<?php
	}

	/**
	 * Activation callback
	 */
	public function check_version_update() {
		// check for version
		$meta = get_option( 'wp_external_links-meta' );
		if ( $meta[ 'version' ] == WP_EXTERNAL_LINKS_VERSION )
			return;

		// set new version
		$meta[ 'version' ] = WP_EXTERNAL_LINKS_VERSION;
		update_option( 'wp_external_links-meta', $meta );

		// check for upgrading saved options to v1.00
		$old_options = get_option( 'WP_External_Links_options' );

		if ( ! empty( $old_options ) ) {
			$new_options = $this->save_options;

			$new_options[ 'main' ][ 'target' ] = $old_options[ 'target' ];
			$new_options[ 'main' ][ 'filter_page' ] = $old_options[ 'filter_whole_page' ];
			$new_options[ 'main' ][ 'filter_posts' ] = $old_options[ 'filter_posts' ];
			$new_options[ 'main' ][ 'filter_comments' ] = $old_options[ 'filter_comments' ];
			$new_options[ 'main' ][ 'filter_widgets' ] = $old_options[ 'filter_widgets' ];
			$new_options[ 'seo' ][ 'external' ] = $old_options[ 'external' ];
			$new_options[ 'seo' ][ 'nofollow' ] = $old_options[ 'nofollow' ];
			$new_options[ 'seo' ][ 'use_js' ] = $old_options[ 'use_js' ];
			$new_options[ 'style' ][ 'class_name' ] = $old_options[ 'class_name' ];
			$new_options[ 'style' ][ 'icon' ] = $old_options[ 'icon' ];
			$new_options[ 'style' ][ 'no_icon_class' ] = $old_options[ 'no_icon_class' ];
			$new_options[ 'style' ][ 'no_icon_same_window' ] = $old_options[ 'no_icon_same_window' ];

			// save new format option values
			update_option( 'wp_external_links-main', $new_options[ 'main' ] );
			update_option( 'wp_external_links-seo', $new_options[ 'seo' ] );
			update_option( 'wp_external_links-style', $new_options[ 'style' ] );

			// delete old format option values
			delete_option( 'WP_External_Links_options' );
		}

		// upgrade to v1.20
		$upgrade_main = get_option( 'wp_external_links-main' );

		if ( ! isset( $upgrade_main[ 'ignore' ] ) ) {
			$upgrade_main[ 'ignore' ] = $this->save_options[ 'main' ][ 'ignore' ];
			update_option( 'wp_external_links-main', $upgrade_main );
		}

		// upgrade to v1.30
		if ( WP_EXTERNAL_LINKS_VERSION == '1.30' ) {
			$new_options = $this->save_options;
			$general = get_option( 'wp_external_links-general' );
			$style = get_option( 'wp_external_links-style' );

			if ( isset( $general[ 'target' ] ) ) $new_options[ 'main' ][ 'target' ] = $general[ 'target' ];
			$new_options[ 'main' ][ 'filter_page' ] = ( isset( $general[ 'filter_page' ] ) ) ? $general[ 'filter_page' ] : 0;
			$new_options[ 'main' ][ 'filter_posts' ] = ( isset( $general[ 'filter_posts' ] ) ) ? $general[ 'filter_posts' ] : 0;
			$new_options[ 'main' ][ 'filter_comments' ] = ( isset( $general[ 'filter_comments' ] ) ) ? $general[ 'filter_comments' ] : 0;
			$new_options[ 'main' ][ 'filter_widgets' ] = ( isset( $general[ 'filter_widgets' ] ) ) ? $general[ 'filter_widgets' ] : 0;
			if ( isset( $general[ 'ignore' ] ) ) $new_options[ 'main' ][ 'ignore' ] = $general[ 'ignore' ];

			$new_options[ 'seo' ][ 'external' ] = ( isset( $general[ 'external' ] ) ) ? $general[ 'external' ] : 0;
			$new_options[ 'seo' ][ 'nofollow' ] = ( isset( $general[ 'nofollow' ] ) ) ? $general[ 'nofollow' ] : 0;
			$new_options[ 'seo' ][ 'use_js' ] = ( isset( $general[ 'use_js' ] ) ) ? $general[ 'use_js' ] : 0;
			if ( isset( $general[ 'title' ] ) ) $new_options[ 'seo' ][ 'title' ] = $general[ 'title' ];

			if ( isset( $general[ 'class_name' ] ) ) $new_options[ 'style' ][ 'class_name' ] = $general[ 'class_name' ];

			if ( isset( $style[ 'icon' ] ) ) $new_options[ 'style' ][ 'icon' ] = $style[ 'icon' ];
			if ( isset( $style[ 'no_icon_class' ] ) ) $new_options[ 'style' ][ 'no_icon_class' ] = $style[ 'no_icon_class' ];
			$new_options[ 'style' ][ 'no_icon_same_window' ] = ( isset( $style[ 'no_icon_same_window' ] ) ) ? $style[ 'no_icon_same_window' ] : 0;

			$new_options[ 'extra' ][ 'fix_js' ] = ( isset( $general[ 'fix_js' ] ) ) ? $general[ 'fix_js' ] : 0;
			$new_options[ 'extra' ][ 'phpquery' ] = ( isset( $general[ 'phpquery' ] ) ) ? $general[ 'phpquery' ] : 0;
			if ( isset( $general[ 'filter_excl_sel' ] ) ) $new_options[ 'extra' ][ 'filter_excl_sel' ] = $general[ 'filter_excl_sel' ];

			// save new format option values
			update_option( 'wp_external_links-main', $new_options[ 'main' ] );
			update_option( 'wp_external_links-seo', $new_options[ 'seo' ] );
			update_option( 'wp_external_links-style', $new_options[ 'style' ] );
			update_option( 'wp_external_links-extra', $new_options[ 'extra' ] );

			// delete old format
			delete_option( 'wp_external_links-general' );
		}
	}

	/**
	 * Method for test purpuses
	 */
	public function __options($values = null) {
		if (class_exists('Test_WP_Mailto_Links') && constant('WP_DEBUG') === true) {
			if ($values !== null) {
				$this->set_options($values);
			}

			return $this->options;
		}
	}

	/**
	 * Uninstall callback
	 */
	static public function call_uninstall() {
		self::$staticForm->delete_options();
	}

	/**
	 * Set tooltip help
	 * @param string $text
	 * @return string
	 */
	public function tooltip_help( $text ) {
		$text = $this->__( $text );
		$text = htmlentities( $text );

		$html = '<a href="#" class="tooltip-help" title="'. $text .'"><sup>(?)</sup></a>';
		return $html;
	}

	/**
	 * Get html seperator
	 * @return string
	 */
	protected function hr() {
		return '<hr style="border:1px solid #FFF; border-top:1px solid #EEE;" />';
	}


	/**
	 * Check if widget_content filter is available (Widget Logic Plugin)
	 * @return boolean
	 * @static
	 */
	public static function check_widget_content_filter() {
		// set widget_content filter of Widget Logic plugin
		$widget_logic_opts = get_option( 'widget_logic' );

		if ( function_exists( 'widget_logic_expand_control' ) AND is_array( $widget_logic_opts ) AND key_exists( 'widget_logic-options-filter', $widget_logic_opts ) )
			return ( $widget_logic_opts[ 'widget_logic-options-filter' ] == 'checked' );

		return FALSE;
	}

} // End Admin_External_Links Class

endif;

/* ommit PHP closing tag, to prevent unwanted whitespace at the end of the parts generated by the included files */