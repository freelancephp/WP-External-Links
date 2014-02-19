<?php defined( 'ABSPATH' ) OR die( 'No direct access.' );
if ( ! class_exists( 'WP_Meta_Box_Page_01' ) ):
/**
 * WP_Meta_Box_Page_01 Class
 *
 * Creating a dashboard-like admin page with meta boxes
 *
 * Requires WordPress 3.0+ and PHP 5.2+
 *
 * Custom Actions:
 * - load action params: (1) this object
 *     - meta_box_load
 *     - meta_box_load-[page_slug]
 *
 * Custom Filters:
 * - page_title filter params: (1) default content, (2) this object
 *     - meta_box_page_title
 *     - meta_box_page_title-[page_slug]
 *
 * - page_content filter params: (1) default content, (2) this object
 *     - meta_box_page_content
 *     - meta_box_page_content-[page_slug]
 *
 * - screen_settings filter params: (1) default content, (2) current screen object, (3) this object
 *     - meta_box_screen_settings
 *     - meta_box_screen_settings-[page_slug]
 *
 * - contextual_help filter params: (1) default content, (2) current screen id, (3) current screen object, (4) this object
 *     - meta_box_contextual_help
 *     - meta_box_contextual_help-[page_slug]
 *
 * @version  0.1
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @license  Dual licensed under the MIT and GPL licenses
 */
class WP_Meta_Box_Page_01 {

	/**
	 * Default settings
	 * @var array
	 */
	private static $default_settings = array(
		// Page title
		'page_title' => NULL, // Default will be set equal to $menu_title

		// Menu title
		'menu_title' => NULL, // Default will be set equal to $page_title

		// Page slug
		'page_slug' => NULL, // Default will be set to: sanitize_title_with_dashes( $menu_title )

		// Default number of columns
		'default_columns' => 2,

		// Column widths
		'column_widths' => array(
			1 => array( 99 ),
			2 => array( 49, 49 ),
			3 => array( 32.33, 32.33, 32.33 ),
			4 => array( 24, 24, 24, 24 ),
		),

		// Add page method
		'add_page_method' => 'add_options_page', // OR: add_menu_page, add_object_page, add_submenu_page

		// Extra params for the add_page_method

		// Capability
		'capability' => 'manage_options', // Optional for all methods

		// Parent slug
		'parent_slug' => NULL, // Nescessary when using "add_submenu_page"

		// Url to the icon to be used for the menu ( around 20 x 20 pixels )
		'icon_url' => NULL, // Only for "add_menu_page" or "add_object_page"

		// The position in the menu order this menu should appear
		'position' => NULL, // Only for "add_menu_page", default on bottom of the menu
	);

	/**
	 * Settings
	 * @var array
	 */
	private $settings = array();

	/**
	 * Meta boxes
	 * @var array
	 */
	private $meta_boxes = array();

	/**
	 * Pagehook ( will be set when page is created )
	 * @var string
	 */
	protected $pagehook = NULL;


	/**
	 * Initialize
	 * @param array $settings  Optional
	 */
	public function init( $settings = NULL, $load_callback = NULL ) {
		// get settings of child class
		$child_settings = $this->settings;

		// set settings in 3 steps...
		// (1) first set the default options
		$this->set_setting( self::$default_settings );

		// (2) set child settings
		if ( ! empty( $child_settings ) )
			$this->set_setting( $child_settings );

		// (3) set param settings
		if ( $settings !== NULL )
			$this->set_setting( $settings );

		// actions
		add_action( 'admin_menu', array( $this, 'call_admin_menu' ) );

		// add load action
		if ( $load_callback !== NULL )
			add_action( 'meta_box_load-'. $this->get_setting( 'page_slug' ), $load_callback );
	}

	/**
	 * Set default setting value
	 * @param mixed $key    Also possible to give an array of key/value pairs
	 * @param mixed $value  Optional
	 * @static
	 */
	public static function set_default_setting( $key, $value = NULL ) {
		if ( is_array( $key ) ) {
			foreach ( $key AS $k => $v )
				self::set_default_setting( $k, $v );
		} else {
			self::$default_settings[ $key ] = $value;
		}
	}

	/**
	 * Set setting value
	 * @param mixed $key    Also possible to give an array of key/value pairs
	 * @param mixed $value  Optional
	 * @return $this  For chaining
	 */
	public function set_setting( $key, $value = NULL ) {
		if ( is_array( $key ) ) {
			foreach ( $key AS $k => $v )
				$this->set_setting( $k, $v );
		} else {
			$this->settings[ $key ] = $value;
		}

		// auto-set related prop values
		if ( $value !== NULL ) {
			if ( $key == 'menu_title' AND $this->get_setting( 'page_title' ) === NULL )
				$this->set_setting( 'page_title', $value );

			if ( $key == 'page_title' AND $this->get_setting( 'menu_title' ) === NULL )
				$this->set_setting( 'menu_title', $value );

			if ( ( $key == 'menu_title' OR $key == 'page_title' ) AND $this->get_setting( 'page_slug' ) === NULL ) {
				$new_val = $value;
				$new_val = sanitize_title_with_dashes( $new_val );
				$new_val = strtolower( $new_val );

				// check for valid page_slug
				if ( ! preg_match( '/^[a-z_-]+$/', $new_val ) ) {
					$new_val = str_replace(
									array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 0 ),
									array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j' ),
									$new_val
								);

					$new_val = ereg_replace( '[^a-z_-]', '', $new_val );
				}

				$this->set_setting( 'page_slug', $new_val );
			}
		}

		return $this;
	}

	/**
	 * Get setting value
	 * @param string $key      Optional, when NULL will return array of all options
	 * @param mixed  $default  Optional, return default when key cannot be found OR is NULL
	 * @return mixed
	 */
	public function get_setting( $key = NULL, $default = NULL ) {
		if ( $key === NULL )
			return $this->settings;

		if ( key_exists( $key, $this->settings ) )
			return ( $this->settings[ $key ] === NULL ) ? $default : $this->settings[ $key ];

		return $default;
	}

	/**
	 * Helper for adding a meta box
	 * @param string $title     Title of the meta box
	 * @param string $callback  Callback for the meta box content
	 * @param mixed  $context   Optional, add meta box to this column (normal = 1, side = 2, column3 = 3, column4 = 4)
	 * @param string $priority  Optional, the priority within the context where the boxes should show ( 'high', 'core', 'default' or 'low' )
	 * @return $this  For chaining
	 */
	public function add_meta_box( $title, $callback, $context = 'normal', $id = NULL, $priority = 'default' ) {
		$this->meta_boxes[] = array(
			'id' => $id,
			'title' => $title,
			'callback' => $callback,
			'context' => $context,
			'priority' => $priority,
		);

		return $this;
	}

	/**
	 * Add callback to "meta_box_load-[page]" action, only applied for this page/object
	 * @param mixed $callback  Callback function
	 * @return $this
	 */
	public function add_load_action( $callback ) {
		add_filter( 'meta_box_load-' . $this->get_setting( 'page_slug' ), $callback );
		return $this;
	}

	/**
	 * Add callback to "meta_box_load" action, applied to all instances of this class
	 * @param mixed $callback  Callback function
	 * @static
	 */
	public static function add_global_load_action( $callback ) {
		add_filter( 'meta_box_load', $callback );
	}

	/**
	 * Add callback to "meta_box_screen_settings-[page]" filter, only applied for this page/object
	 * @param mixed $callback  Callback function
	 * @return $this
	 */
	public function add_screen_settings_filter( $callback ) {
		add_filter( 'meta_box_screen_settings-' . $this->get_setting( 'page_slug' ), $callback );
		return $this;
	}

	/**
	 * Add callback to "meta_box_contextual_help-[page]" filter, only applied for this page/object
	 * @param mixed $callback  Callback function
	 * @return $this
	 */
	public function add_contextual_help_filter( $callback ) {
		add_filter( 'meta_box_contextual_help-' . $this->get_setting( 'page_slug' ), $callback );
		return $this;
	}

	/**
	 * Add callback to "meta_box_page_title-[page]" filter, only applied for this page/object
	 * @param mixed $callback  Callback function
	 * @return $this
	 */
	public function add_title_filter( $callback ) {
		add_filter( 'meta_box_page_title-' . $this->get_setting( 'page_slug' ), $callback );
		return $this;
	}

	/**
	 * Add callback to "meta_box_page_content" filter, applied to all instances of this class
	 * @param mixed $callback  Callback function
	 * @static
	 */
	public static function add_global_title_filter( $callback ) {
		add_filter( 'meta_box_page_title', $callback );
	}

	/**
	 * Add callback to "meta_box_page_content-[page]" filter, only applied for this page/object
	 * @param mixed $callback  Callback function
	 * @return $this
	 */
	public function add_content_filter( $callback ) {
		add_filter( 'meta_box_page_title-' . $this->get_setting( 'page_slug' ), $callback );
		return $this;
	}

	/**
	 * Add callback to "meta_box_page_content" filter, applied to all instances of this class
	 * @param mixed $callback  Callback function
	 * @static
	 */
	public static function add_global_content_filter( $callback ) {
		add_filter( 'meta_box_page_title', $callback );
	}

	/**
	 * Admin menu callback
	 */
	public function call_admin_menu() {
		// add page
		switch ( $this->get_setting( 'add_page_method' ) ) {
			case 'add_menu_page':
				$this->pagehook = add_menu_page(
									$this->get_setting( 'page_title' ),
									$this->get_setting( 'menu_title' ),
									$this->get_setting( 'capability' ),
									$this->get_setting( 'page_slug' ),
									array( $this, 'call_page_content' ),
									$this->get_setting( 'icon_url' ),
									$this->get_setting( 'position' )
								);
				break;

			case 'add_object_page':
				$this->pagehook = add_object_page(
									$this->get_setting( 'page_title' ),
									$this->get_setting( 'menu_title' ),
									$this->get_setting( 'capability' ),
									$this->get_setting( 'page_slug' ),
									array( $this, 'call_page_content' ),
									$this->get_setting( 'icon_url' )
								);
				break;

			case 'add_submenu_page':
				$this->pagehook = add_submenu_page(
									$this->get_setting( 'parent_slug' ),
									$this->get_setting( 'page_title' ),
									$this->get_setting( 'menu_title' ),
									$this->get_setting( 'capability' ),
									$this->get_setting( 'page_slug' ),
									array( $this, 'call_page_content' )
								);
				break;

			case 'add_options_page':
			default:
				$this->pagehook = add_options_page(
									$this->get_setting( 'page_title' ),
									$this->get_setting( 'menu_title' ),
									$this->get_setting( 'capability' ),
									$this->get_setting( 'page_slug' ),
									array( $this, 'call_page_content' )
								);
				break;

		}

		// execute action
		do_action( 'meta_box_load', $this );

		// load page
		add_action( 'load-' . $this->pagehook, array( $this, 'call_load_page' ) );
	}

	/**
	 * Admin head callback
	 */
	public function call_admin_head() {
?>
<style type="text/css">
	.postbox-container { padding-right:1%; float:left ; /* for WP < 3.1 */ }
	.postbox-container .meta-box-sortables { min-height:200px; }
	.postbox-container .postbox { min-width:0; }
	.postbox-container .postbox .inside { margin:10px 0 ; padding:0 10px; } /* for WP < 3.2 */
</style>
<?php
	}

	/**
	 * Load page callback
	 */
	public function call_load_page() {
		// execute action
		do_action( 'meta_box_load-' . $this->get_setting( 'page_slug' ), $this );

		// add script for meta boxes
		wp_enqueue_script( 'postbox' );

		// add to admin head
		add_action( 'admin_head', array( $this, 'call_admin_head' ) );

		// add screen settings filter
		add_filter( 'screen_settings', array( $this, 'call_screen_settings') );

		// add help text
		add_filter( 'contextual_help', array( $this, 'call_contextual_help' ) );

		// columns
		if ( function_exists( 'add_screen_option' ) ) {
			$count = count( $this->get_setting( 'column_widths' ) );

			add_screen_option( 'layout_columns', array(
				'max' => $count,
				'default' => min( $count, $this->get_setting( 'default_columns' ) )
			));
		}

		// add meta boxes
		$nr = 0;
		foreach ( $this->meta_boxes AS $box ) {
			$title = $box[ 'title' ];
			$id = ( isset( $box[ 'id' ] ) ) ? $box[ 'id' ] : sanitize_title_with_dashes( $title .'-'. ++$nr, 'meta-box-' . $nr );
			$callback = ( is_string( $box[ 'callback' ] ) && method_exists( $this, $box[ 'callback' ] ) ) ? array( $this, $box[ 'callback' ] ) : $box[ 'callback' ];
			$context = $box[ 'context' ];
			$priority = $box[ 'priority' ];

			// set context
			if ( $context == 2 OR strtolower( $context ) == 'side' ) {
				$context = 'side';
			} elseif ( $context == 3 OR strtolower( $context ) == 'column3' ) {
				$context = 'column3';
			} elseif ( $context == 4 OR strtolower( $context ) == 'column4' ) {
				$context = 'column4';
			} else { // default
				$context = 'normal';
			}

			// add meta box
			add_meta_box( $id, $title, $callback, $this->pagehook, $context, $priority ); // $callback_args, doesn't seem to work
		}
	}

	/**
	 * Screen settings (callback)
	 * @param string $content
	 * @return string
	 */
	public function call_screen_settings( $content ) {
		if ( self::get_current_screen()->id == convert_to_screen( $this->pagehook )->id ) {
			// apply filters for this meta box page
			$content = apply_filters( 'meta_box_screen_settings-' . $this->get_setting( 'page_slug' ), $content, $this->get_current_screen(), $this );
		}

		return $content;
	}

	/**
	 * Contextual help (callback)
	 * @param string $content
	 * @return string
	 */
	public function call_contextual_help( $content ) {
		$current_screen = $this->get_current_screen();

		if ( $current_screen->id == convert_to_screen( $this->pagehook )->id ) {
			// apply filters for this meta box page
			$content = apply_filters( 'meta_box_contextual_help-' . $this->get_setting( 'page_slug' ), $content, $current_screen->id, $current_screen, $this );
		}

		return $content;
	}

	/**
	 * Display admin page content (callback)
	 */
	public function call_page_content() {
		echo '<div class="wrap">';

		// page title
		$meta_boxes_page_title = apply_filters( 'meta_box_page_title', $this->get_page_title(), $this );
		echo apply_filters( 'meta_box_page_title-' . $this->get_setting( 'page_slug' ), $meta_boxes_page_title, $this );

		// page content
		$meta_boxes_content = apply_filters( 'meta_box_page_content', $this->get_page_content(), $this );
		echo apply_filters( 'meta_box_page_content-' . $this->get_setting( 'page_slug' ), $meta_boxes_content, $this );

		echo '</div>';
	}

	/**
	 * Get page title
	 * Can be changed by using adding filters, see add_title_filter()
	 * @return string
	 */
	private function get_page_title() {
		return '<h2>' . get_admin_page_title() . '</h2>';
	}

	/**
	 * Get meta boxes content. Can be changed by adding filters, see add_content_filter()
	 * @return string
	 */
	private function get_page_content() {
		return self::get_ob_callback( array( $this, 'show_meta_boxes' ) );
	}

	/**
	 * Display meta boxes content
	 */
	private function show_meta_boxes() {
		$opt_column_widths = $this->get_setting( 'column_widths' );
		$hide2 = $hide3 = $hide4 = '';
		switch ( self::get_screen_layout_columns( $this->get_setting( 'default_columns' ) ) ) {
			case 4:
				$column_widths = $opt_column_widths[ 4 ];
				break;
			case 3:
				$column_widths = $opt_column_widths[ 3 ];
				$hide4 = 'display:none;';
				break;
			case 2:
				$column_widths = $opt_column_widths[ 2 ];
				$hide3 = $hide4 = 'display:none;';
				break;
			default:
				$column_widths = $opt_column_widths[ 1 ];
				$hide2 = $hide3 = $hide4 = 'display:none;';
		}

		$column_widths = array_pad( $column_widths, 4, 0 );
?>
		<div id='<?php echo $this->pagehook ?>-widgets' class='metabox-holder'>
			<div class='postbox-container' style='width:<?php echo $column_widths[0] ?>%'>
				<?php do_meta_boxes( $this->pagehook, 'normal', '' ); ?>
			</div>

			<div class='postbox-container' style='<?php echo $hide2 ?>width:<?php echo $column_widths[1] ?>%'>
				<?php do_meta_boxes( $this->pagehook, 'side', '' ); ?>
			</div>

			<div class='postbox-container' style='<?php echo $hide3 ?>width:<?php echo $column_widths[2] ?>%'>
				<?php do_meta_boxes( $this->pagehook, 'column3', '' ); ?>
			</div>

			<div class='postbox-container' style='<?php echo $hide4 ?>width:<?php echo $column_widths[3] ?>%'>
				<?php do_meta_boxes( $this->pagehook, 'column4', '' ); ?>
			</div>
		</div>

		<form style="display:none" method="get" action="">
			<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
		</form>

<script type="text/javascript">
//<![CDATA[
jQuery( document ).ready( function( $ ){
	var columnWidths = <?php echo json_encode( $opt_column_widths ) ?>,
		$boxes = $( '.postbox-container' ),
		setColumnWidths = function () {
			var c = $( 'input[name="screen_columns"]:checked' ).val();

			// first hide all boxes
			$boxes.hide();

			// set width and show boxes
			for ( var x = 0; x < columnWidths[ c ].length; x++ ) {
				$boxes.eq( x )
					.css( 'width', columnWidths[ c ][ x ]+ '%' )
					.show();
			}
		};

	// radio screen columns
	$( 'input[name="screen_columns"]' )
		.click(function(){
			if ( $( 'input[name="screen_columns"]:checked' ).val() == $( this ).val() ) {
				setTimeout(function(){
					setColumnWidths();
				}, 1 );
			}
		})
		.change(function( e ){
			setColumnWidths();

			// prevent
			e.stopImmediatePropagation();
		});

	// trigger change event of selected column
	$( 'input[name="screen_columns"]:checked' ).change();

<?php if ( self::wp_version( '3.2', '<' )  ): ?>
	// for WP < 3.2

	// close postboxes that should be closed
	$( '.if-js-closed' ).removeClass( 'if-js-closed' ).addClass( 'closed' );

	// Loading saved screen settings
	postboxes.add_postbox_toggles( '<?php echo $this->pagehook ?>' );
<?php endif; ?>
});
//]]>
</script>
<?php
	}

	/**
	 * Static helpers
	 */

	/**
	 * Get content displayed by given callback
	 * @param mixed $callback
	 * @return string
	 * @static
	 */
	public static function get_ob_callback( $callback ) {
		// start output buffer
		ob_start();

		// call callback
		call_user_func( $callback );

		// get the view content
		$content = ob_get_contents();

		// clean output buffer
		ob_end_clean();

		return $content;
	}

	/**
	 * Get current version of WP or compare versions
	 * @global string $wp_version
	 * @return mixed
	 * @static
	 */
	public static function wp_version( $compare_version = NULL, $operator = NULL ) {
		global $wp_version;
		$cur_wp_version = preg_replace( '/-.*$/', '', $wp_version );

		if ( $compare_version === NULL )
			return $cur_wp_version;

		// check comparison
		return version_compare( $cur_wp_version, $compare_version, $operator );
	}

	/**
	 * Return global WP value of $screen_layout_columns
	 * @global integer $screen_layout_columns
	 * @return integer
	 * @static
	 */
	public static function get_screen_layout_columns( $default = NULL ) {
		global $screen_layout_columns;
		return ( empty( $screen_layout_columns ) ) ? $default : $screen_layout_columns;
	}

	/**
	 * Return global WP value of $current_screen
	 * @global string $current_screen
	 * @return string
	 * @static
	 */
	public static function get_current_screen( $default = NULL ) {
		global $current_screen;
		return $current_screen;
	}

} // End WP_Meta_Box_Page_01 Class

endif;

/* ommit PHP closing tag, to prevent unwanted whitespace at the end of the parts generated by the included files */