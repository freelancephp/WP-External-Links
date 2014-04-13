<?php defined( 'ABSPATH' ) OR die( 'No direct access.' );
if ( ! class_exists( 'WP_Option_Forms_01' ) ):
/**
 * WP_Option_Forms_01 Class
 *
 * Simple class for creating option forms of a the same option_group.
 * Also with Ajax save support.
 *
 * Requires WordPress 3.0+ and PHP 5.2+
 *
 * @version  0.1
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @license  Dual licensed under the MIT and GPL licenses
 */
class WP_Option_Forms_01 {

	/**
	 * Name used as prefix for saving option names
	 * @var string
	 */
	protected $name = NULL;

	/**
	 * Option names and values
	 * @var string
	 */
	protected $options = array();

	/**
	 * Current option name
	 * @var string
	 */
	protected $current_option = NULL;


	/**
	 * Constructor
	 * @param array $name
	 * @param array $options  Optional
	 */
	public function __construct( $name, $options = array() ) {
		$this->name = sanitize_title_with_dashes( $name );

		// set option names
		foreach ( $options AS $option_name => $values ) {
			$this->add_option( $option_name, $values );
		}

		// actions
		add_action( 'wp_ajax_wpof_update_options', array( $this, 'call_wp_ajax' ) );
		add_action( 'admin_menu', array( $this, 'call_admin_menu' ) );
	}

	/**
	 * Admin menu callback
	 */
	public function call_admin_menu() {
		// Register settings
		foreach ( $this->options AS $option_name => $values ) {
			register_setting( $option_name, $option_name );
		}

		// script
//		wp_enqueue_script( 'option-forms', plugins_url( 'js/wp-option-forms.js', WP_EXTERNAL_LINKS_FILE ), array( 'jquery' ), '1.0' );
	}

	/**
	 * Ajax call for saving option values
	 */
	public function call_wp_ajax() {
		check_ajax_referer( 'wpof_update_options', 'wpof-nonce' );

		$option_name = $_POST[ 'ajax_option_name' ];
		$value = NULL;

		if ( isset( $_POST[ $option_name ] ) )
			$value = $_POST[ $option_name ];

		if ( ! is_array( $value ) )
			$value = trim( $value );

		$value = stripslashes_deep( $value );

		update_option( $option_name, $value );

		die( '1' );
	}

	/**
	 * Add option (or reset option when already exists)
	 * @param string $option_name
	 * @param array  $default_values  Optional
	 * @return this
	 */
	public function add_option( $option_name, $default_values = array() ) {
		// set values
		$saved_values = get_option( $this->name .'-'. $option_name );

		if ( empty( $saved_values ) ) {
			foreach ( $default_values AS $key => $value )
				$values[ $key ] = $value;
		} else {
			foreach ( $default_values AS $key => $value )
				$values[ $key ] = '';

			foreach ( $saved_values AS $key => $value )
				$values[ $key ] = $value;
		}

		// option and values
		$this->options[ $this->name .'-'. $option_name ] = $values;
		return $this;
	}

	/**
	 * Set current option to use
	 * @param string $option_name
	 * @return this
	 */
	public function set_current_option( $option_name ) {
		$this->current_option = $this->name .'-'. $option_name;
		return $this;
	}

	/**
	 * Get opening form with all nescessary WP fields
	 * @param boolean $ajaxSave  Optional
	 * @param array   $attrs     Optional
	 * @return string
	 */
	public function open_form( $ajaxSave = TRUE, $attrs = array() ) {
		// set class for ajax or non-ajax form
		$attrs[ 'class' ] = ( ( $ajaxSave ) ? 'ajax-form' : 'no-ajax-form' )
							. ( ( key_exists( 'class', $attrs ) ) ? ' '. $attrs[ 'class' ] : '' );

		// show start form
		$html = '';
		$html .= '<form method="post" action="options.php" '. $this->attrs( $attrs ) .'>';

		if ( $ajaxSave ) {
			$html .= wp_nonce_field( 'wpof_update_options', 'wpof-nonce', FALSE, FALSE );
			$html .= '<input type="hidden" name="action" value="wpof_update_options" />';
			$html .= '<input type="hidden" name="ajax_option_name" value="'. $this->current_option .'" />';

			// instead of using settings_fields();
			$html .= '<input type="hidden" name="option_page" value="' . esc_attr( $this->current_option ) . '" />';
			$html .= wp_nonce_field( $this->current_option . '-options', '_wpnonce', TRUE, FALSE );
		} else {
			// instead of using settings_fields();
			$html .= '<input type="hidden" name="option_page" value="' . esc_attr( $this->current_option ) . '" />';
			$html .= '<input type="hidden" name="action" value="update" />';
			$html .= wp_nonce_field( $this->current_option . '-options', '_wpnonce', TRUE, FALSE );
		}

		return $html;
	}

	/**
	 * Get script for saving screen option
	 * @param string $option_name
	 * @param string $key
	 * @return string
	 */
	public function open_screen_option( $option_name, $key ) {
		$this->set_current_option( $option_name );

		$html = '';
		$html .= '<script type="text/javascript">' . "\n";
		$html .= '//<![CDATA[' . "\n";
		$html .= 'jQuery( document ).ready( function( $ ){' . "\n";
		$html .= "\t" .  '// save screen option' . "\n";
		$html .= "\t" .  '$( "#screen-meta #'. $key .'" )' . "\n";
		$html .= "\t\t" .  '.change(function(){' . "\n";
		$html .= "\t\t\t" .  'var self = this;' . "\n";
		$html .= "\t\t\t" .  '$.post( ajaxurl, {' . "\n";
		$html .= "\t\t\t\t" .  'action: "wpof_update_options",' . "\n";
		$html .= "\t\t\t\t" .  '"wpof-nonce": "'. wp_create_nonce( 'wpof_update_options' ) .'",' . "\n";
		$html .= "\t\t\t\t" .  'ajax_option_name: "'. $this->current_option .'",' . "\n";
		$html .= "\t\t\t\t" .  '"'. $this->field_name( $key ) .'": $( this ).val()' . "\n";
		$html .= "\t\t\t" .  '}, function () {' . "\n";
		$html .= "\t\t\t\t" .  '$( self ).trigger( "ajax_updated" );' . "\n";
		$html .= "\t\t\t" .  '});' . "\n";
		$html .= "\t\t" .  '});' . "\n";
		$html .= '});' . "\n";
		$html .= '//]]>' . "\n";
		$html .= '</script>' . "\n";

		return $html;
	}

	/**
	 * Get closing form
	 * @return string
	 */
	public function close_form() {
		return '</form>';
	}

	/**
	 * Text field
	 * @param string $key
	 * @param array  $attrs  Optional
	 * @return string
	 */
	public function text( $key, $attrs = array() ) {
		if ( ! key_exists( 'class', $attrs ) )
			$attrs[ 'class' ] = 'regular-text';

		return '<input type="text" '. $this->attrs( $attrs, $key, $this->value( $key ) ) .' />';
	}

	/**
	 * Text field
	 * @param string $key
	 * @param array  $attrs  Optional
	 * @return string
	 */
	public function textarea( $key, $attrs = array() ) {
		if ( ! key_exists( 'class', $attrs ) )
			$attrs[ 'class' ] = 'large-text';

		return '<textarea '. $this->attrs( $attrs, $key ) .'>'. $this->value( $key ) .'</textarea>';
	}

	/**
	 * Radio field
	 * @param string $key
	 * @param mixed  $value
	 * @param array  $attrs  Optional
	 * @return string
	 */
	public function radio( $key, $value, $attrs = array() ) {
		$checked = ( $value == $this->value( $key ) ) ? ' checked="checked"' : '';
		return '<input type="radio" '. $this->attrs( $attrs, $key, $value )
					. $checked . ' />';
	}

	/**
	 * Checkbox field
	 * @param string $key
	 * @param mixed  $value
	 * @param array  $attrs  Optional
	 * @return string
	 */
	public function checkbox( $key, $value, $attrs = array() ) {
		$checked = ( $value == $this->value( $key ) ) ? ' checked="checked"' : '';
		return '<input type="checkbox" '. $this->attrs( $attrs, $key, $value )
					. $checked . ' />';
	}

	/**
	 * Select field
	 * @param string $key
	 * @param array  $options  Optional
	 * @param array  $attrs    Optional
	 * @return string
	 */
	public function select( $key, $options = array(), $attrs = array() ) {
		$html = '<select '. $this->attrs( $attrs, $key ) .'>';

		foreach ( $options AS $value => $label ) {
			$selected = ( $value == $this->value( $key ) ) ? ' selected="selected"' : '';
			$html .= '<option value="'. $value .'"'. $selected .'>'. $label .'</option>';
		}

		$html .= '</select>';
		return $html;
	}

	/**
	 * Submit button
	 * @param array $attrs  Optional
	 * @return string
	 */
	public function submit( $attrs = array() ) {
		// set class attr
		$attrs[ 'class' ] = 'button-primary'. ( ( key_exists( 'class', $attrs ) ) ? ' '. $attrs[ 'class' ] : '' );

		// show submit
		$html = '';
		$html .= '<p class="button-controls" style="text-align:right;">';
		$html .= '<img alt="" title="" class="ajax-feedback" src="'. get_bloginfo( 'url' ) .'/wp-admin/images/wpspin_light.gif" style="visibility: hidden;" />';
		$html .= '<input type="submit" '. $this->attrs( $attrs, '', __( 'Save Changes' ) ) .' />';
		$html .= '</p>';
		return $html;
	}

	/**
	 * Get field name of given key
	 * @param string $key
	 * @return string
	 */
	public function field_name( $key ) {
		return $this->current_option . '[' . $key . ']';
	}

	/**
	 * Get value of given option key
	 * @param string  $key
	 * @param mixed   $default_value  Optional
	 * @param boolean $option_name    Optional, search in given option_name instead of the current option
	 * @return mixed
	 */
	public function value( $key, $default_value = NULL, $option_name = NULL ) {
		if ( $option_name === NULL ) {
			$option = $this->current_option;
		} else {
			$option = $this->name . '-' . $option_name;
		}

		if (!isset($this->options[ $option ])) {
			return $default_value;
		}

		$values = $this->options[ $option ];

		return ( is_array( $values ) AND key_exists( $key, $values ) AND $values[ $key ] !== NULL ) ? $values[ $key ] : $default_value;
	}

	/**
	 * Delete and unregister option
	 */
	public function delete_options() {
		foreach ( $this->options AS $option_name => $values ) {
			delete_option( $option_name );
		}
	}

	/**
	 * Get string of given attributes
	 * @param array  $attrs
	 * @param string $key    Optional
	 * @param mixed  $value  Optional
	 * @return string
	 */
	protected function attrs( $attrs, $key = NULL, $value = NULL ) {
		$str = '';

		// set name, id, value attr
		if ( $key !== NULL ) {
			$str .= 'name="' . $this->field_name( $key ) .'" ';
			if ( ! key_exists( 'id', $attrs ) )
				$str .= 'id="' . $key .'" ';
		}

		if ( $value !== NULL )
			$str .= 'value="' . $value .'" ';

		foreach ( $attrs AS $attr => $value )
			$str .= $attr .'="'. $value .'" ';

		return $str;
	}

} // End WP_Option_Forms_01

endif;

/* ommit PHP closing tag, to prevent unwanted whitespace at the end of the parts generated by the included files */