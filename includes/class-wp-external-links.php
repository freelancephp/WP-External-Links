<?php defined( 'ABSPATH' ) OR die( 'No direct access.' );
if ( ! class_exists( 'WP_External_Links' ) ):

/**
 * Class WP_External_Links
 * @package WordPress
 * @since
 * @category WordPress Plugins
 */
final class WP_External_Links {

	/**
	 * Admin object
	 * @var Admin_External_Links
	 */
	public $admin = NULL;

	/**
	 * Array of ignored links
	 * @var type
	 */
	private $ignored = array();


	/**
	 * Constructor
	 */
	public function __construct() {
		// set admin object
		$this->admin = new Admin_External_Links();

        // add actions
		add_action( 'wp', array( $this, 'call_wp' ) );
	}

    /**
     * Get domain name
     * @return string
     */
    private function get_domain() {
        if ( empty($this->_domain_name) ) {
            $url = get_bloginfo('wpurl');
            preg_match("/[a-z0-9\-]{1,63}\.[a-z\.]{2,6}$/", parse_url($url, PHP_URL_HOST), $domain_tld);
            $this->_domain_name = count($domain_tld) > 0 ? $domain_tld[0] : $_SERVER['SERVER_NAME'];
        }

        return $this->_domain_name;
    }

	/**
	 * Quick helper method for getting saved option values
	 * @param string $key
	 * @return mixed
	 */
	public function get_opt( $key ) {
		$lookup = $this->admin->save_options;

		foreach ( $lookup as $option_name => $values ) {
			$value = $this->admin->form->value( $key, '___NONE___', $option_name );

			if ($value !== '___NONE___')
				return $value;
		}

		throw new Exception('Option with key "' . $key . '" does not exist.');
	}

	/**
	 * wp callback
	 */
	public function call_wp() {
		if ( ! is_admin() && ! is_feed() ) {
			// add wp_head for setting js vars and css style
			add_action( 'wp_head', array( $this, 'call_wp_head' ) );

			// set js file
			if ( $this->get_opt( 'use_js' ) )
				wp_enqueue_script( 'wp-external-links', plugins_url( 'js/wp-external-links.js', WP_EXTERNAL_LINKS_FILE ), array('jquery'), WP_EXTERNAL_LINKS_VERSION, (bool) $this->get_opt( 'load_in_footer' ) );

            // set ignored
            $ignored = $this->get_opt( 'ignore' );
            $ignored = trim( $ignored );
            $ignored = explode( "\n", $ignored );
            $ignored = array_map( 'trim', $ignored );
            $ignored = array_map( 'strtolower', $ignored );
            $this->ignored = $ignored;

			// filters
			if ( $this->get_opt( 'filter_page' ) ) {
				// filter body
				ob_start( array( $this, 'call_filter_content' ) );

				// set ob flush
				add_action('wp_footer', array($this, 'callback_flush_buffer'), 10000);

			} else {
				// set filter priority
				$priority = 1000000000;

				// content
				if ( $this->get_opt( 'filter_posts' ) ) {
					add_filter( 'the_title', array( $this, 'call_filter_content' ), $priority );
					add_filter( 'the_content', array( $this, 'call_filter_content' ), $priority );
					add_filter( 'get_the_excerpt', array( $this, 'call_filter_content' ), $priority );
					// redundant:
					//add_filter( 'the_excerpt', array( $this, 'call_filter_content' ), $priority );
				}

				// comments
				if ( $this->get_opt( 'filter_comments' ) ) {
					add_filter( 'get_comment_text', array( $this, 'call_filter_content' ), $priority );
					// redundant:
					//add_filter( 'comment_text', array( $this, 'call_filter_content' ), $priority );

					add_filter( 'comment_excerpt', array( $this, 'call_filter_content' ), $priority );
					// redundant:
					//add_filter( 'get_comment_excerpt', array( $this, 'call_filter_content' ), $priority );

					add_filter( 'comment_url', array( $this, 'call_filter_content' ), $priority );
					add_filter( 'get_comment_author_url', array( $this, 'call_filter_content' ), $priority );
					add_filter( 'get_comment_author_link', array( $this, 'call_filter_content' ), $priority );
					add_filter( 'get_comment_author_url_link', array( $this, 'call_filter_content' ), $priority );
				}

				// widgets
				if ( $this->get_opt( 'filter_widgets' ) ) {
					if ( $this->admin->check_widget_content_filter() ) {
						// only if Widget Logic plugin is installed and 'widget_content' option is activated
						add_filter( 'widget_content', array( $this, 'call_filter_content' ), $priority );
					} else {
						// filter text widgets
						add_filter( 'widget_title', array( $this, 'call_filter_content' ), $priority );
						add_filter( 'widget_text', array( $this, 'call_filter_content' ), $priority );
					}
				}
			}
		}

		// hook
		do_action('wpel_ready', array($this, 'call_filter_content'), $this);
	}

	/**
	 * End output buffer
	 */
	public function callback_flush_buffer() {
		ob_end_flush();
	}

	/**
	 * wp_head callback
	 */
	public function call_wp_head() {
        $icon = $this->get_opt('icon');

        if ($icon) {
            $padding = ($icon < 20) ? 15 : 12;
?>
<style type="text/css" media="screen">
/* WP External Links Plugin */
.ext-icon-<?php echo $icon ?> { background:url(<?php echo plugins_url('/images/ext-icons/ext-icon-' . $icon . '.png', WP_EXTERNAL_LINKS_FILE) ?>) no-repeat 100% 50%; padding-right:<?php echo $padding ?>px; }';
</style>
<?php
        }
	}

	/**
	 * Filter content
	 * @param string $content
	 * @return string
	 */
	public function call_filter_content( $content ) {
		if ( $this->get_opt( 'fix_js' ) ) {
			// fix js problem by replacing </a> by <\/a>
			$content = preg_replace_callback( '/<script([^>]*)>(.*?)<\/script[^>]*>/is', array( $this, 'call_fix_js' ), $content );
		}

		if ( $this->get_opt( 'filter_page' ) && $this->get_opt( 'ignore_selectors' ) ) {
            $content = $this->set_ignored_by_selectors( $content );
        }

        return $this->filter( $content );
	}

	/**
	 * Fix </a> in JavaScript blocks (callback for regexp)
	 * @param array $matches Result of a preg call in filter_content()
	 * @return string Clean code
	 */
	public function call_fix_js( $matches ) {
		return str_replace( '</a>', '<\/a>', $matches[ 0 ] );
	}

	/**
	 * Check if link is external
	 * @param string $href
	 * @return boolean
	 */
	private function is_external( $href ) {
        $wpurl = strtolower( get_bloginfo( 'wpurl' ) );

        // relative url's are internal
        // so just check absolute url's starting with these protocols
        if ( substr( $href, 0, 7 ) !== 'http://'
                && substr( $href, 0, 8 ) !== 'https://'
                && substr( $href, 0, 6 ) !== 'ftp://'
                && substr( $href, 0, 2 ) !== '//' ) {
            return false;
        }

        if ( $this->get_opt( 'ignore_subdomains' ) ) {
            $is_external = ( strpos( $href, $this->get_domain() ) === FALSE );
        } else {
            $is_external = ( strpos( $href, $wpurl ) === FALSE );
        }

        return $is_external;
	}

    /**
     * Is an ignored link
     * @param string $href
     * @return boolean
     */
    private function is_ignored_by_url( $href ) {
		// check if this links should be ignored
		for ( $x = 0, $count = count($this->ignored); $x < $count; $x++ ) {
			if ( strrpos( $href, $this->ignored[ $x ] ) !== FALSE )
				return TRUE;
		}

        return FALSE;
    }

	/**
	 * Set ignored external links selections
	 * @param string $content
	 * @return string
	 */
	private function set_ignored_by_selectors( $content ) {
        // Include phpQuery
        if ( ! class_exists( 'phpQuery' ) ) {
            require_once( 'phpQuery.php' );
        }

        try {
            // set document
            //phpQuery::$debug = true;
            $doc = phpQuery::newDocument( $content );

            $ignore_selectors = $this->get_opt( 'ignore_selectors' );

            // set ignored by selectors
            if ( ! empty( $ignore_selectors ) ) {
                $excludes = $doc->find( $ignore_selectors );

                // links containing selector
                $excludes->filter( 'a' )->attr( 'data-wpel-ignored', 'true' );

                // links as descendant of element containing selector
                $excludes->find( 'a' )->attr( 'data-wpel-ignored', 'true' );
            }

            $doc = (string) $doc;
        } catch (Exception $e) {
            $doc = '';
        }

        if (empty($doc)) {
            return $content;
        }

		return $doc;
	}

	/**
	 * Filter content
	 * @param string $content
	 * @return string
	 */
	private function filter( $content ) {
		// replace links
		$content = preg_replace_callback( '/<a[^A-Za-z](.*?)>(.*?)<\/a[\s+]*>/is', array( $this, 'call_parse_link' ), $content );

		// remove style when no icon classes are found
		if ( strpos( $content, 'ext-icon-' ) === FALSE ) {
			// remove style with id wp-external-links-css
			$content = preg_replace( '/<link ([^>]*)wp-external-links-css([^>]*)\/>[\s+]*/i', '', $content );
		}

		return $content;
	}

    /**
     * Parse an attributes string into an array. If the string starts with a tag,
     * then the attributes on the first tag are parsed. This parses via a manual
     * loop and is designed to be safer than using DOMDocument.
     *
     * @param    string|*   $attrs
     * @return   array
     *
     * @example  parse_attrs( 'src="example.jpg" alt="example"' )
     * @example  parse_attrs( '<img src="example.jpg" alt="example">' )
     * @example  parse_attrs( '<a href="example"></a>' )
     * @example  parse_attrs( '<a href="example">' )
     *
     * @link http://dev.airve.com/demo/speed_tests/php/parse_attrs.php
     */
    private function parse_attrs ($attrs) {
        if ( ! is_scalar($attrs) )
            return (array) $attrs;

        $attrs = str_split( trim($attrs) );

        if ( '<' === $attrs[0] ) # looks like a tag so strip the tagname
            while ( $attrs && ! ctype_space($attrs[0]) && $attrs[0] !== '>' )
                array_shift($attrs);

        $arr = array(); # output
        $name = '';     # for the current attr being parsed
        $value = '';    # for the current attr being parsed
        $mode = 0;      # whether current char is part of the name (-), the value (+), or neither (0)
        $stop = false;  # delimiter for the current $value being parsed
        $space = ' ';   # a single space

        foreach ( $attrs as $j => $curr ) {
            if ( $mode < 0 ) {# name
                if ( '=' === $curr ) {
                    $mode = 1;
                    $stop = false;
                } elseif ( '>' === $curr ) {
                    '' === $name or $arr[ $name ] = $value;
                    break;
                } elseif ( ! ctype_space($curr) ) {
                    if ( ctype_space( $attrs[ $j - 1 ] ) ) { # previous char
                        '' === $name or $arr[ $name ] = '';   # previous name
                        $name = $curr;                        # initiate new
                    } else {
                        $name .= $curr;
                    }
                }
            } elseif ( $mode > 0 ) {# value

                if ( $stop === false ) {
                    if ( ! ctype_space($curr) ) {
                        if ( '"' === $curr || "'" === $curr ) {
                            $value = '';
                            $stop = $curr;
                        } else {
                            $value = $curr;
                            $stop = $space;
                        }
                    }
                } elseif ( $stop === $space ? ctype_space($curr) : $curr === $stop ) {
                    $arr[ $name ] = $value;
                    $mode = 0;
                    $name = $value = '';
                } else {
                    $value .= $curr;
                }
            } else {# neither

                if ( '>' === $curr )
                    break;
                if ( ! ctype_space( $curr ) ) {
                    # initiate
                    $name = $curr;
                    $mode = -1;
                }
            }
        }

        # incl the final pair if it was quoteless
        '' === $name or $arr[ $name ] = $value;

        return $arr;
    }

	/**
	 * Make a clean <a> code (callback for regexp)
	 * @param array $matches Result of a preg call in filter_content()
	 * @return string Clean <a> code
	 */
	public function call_parse_link( $matches ) {
        $link = $matches[ 0 ];
        $label = $matches[ 2 ];

        // parse attributes
		$original_attrs = $matches[ 1 ];
		$original_attrs = stripslashes( $original_attrs );
		$original_attrs = $this->parse_attrs( $original_attrs );
        $attrs = $original_attrs;

		$rel = ( isset( $attrs[ 'rel' ] ) ) ? strtolower( $attrs[ 'rel' ] ) : '';

		// href preperation
        if (isset( $attrs[ 'href' ])) {
            $href = $attrs[ 'href' ];
            $href = strtolower( $href );
            $href = trim( $href );
        } else {
            $href = '';
        }

		// is an internal link?
        // rel=external will be threaded as external link
        $is_external = $this->is_external( $href );
        $has_rel_external =  (strpos( $rel, 'external' ) !== FALSE);

		if ( ! $is_external && ! $has_rel_external) {
    		return apply_filters('wpel_internal_link', $link, $label, $attrs);
        }

        // is an ignored link?
        $is_ignored = $this->is_ignored_by_url( $href ) || isset($attrs['data-wpel-ignored']);

        if ( $is_ignored ) {
			self::add_attr_value( $attrs, 'data-wpel-ignored', 'true' );
            $created_link = self::create_link($label, $attrs);
    		return apply_filters('wpel_ignored_external_link', $created_link, $label, $attrs);
        }

		// set rel="external" (when not already set)
		if ( $this->get_opt( 'external' ) )
			self::add_attr_value( $attrs, 'rel', 'external' );

		// set rel="nofollow" 
		if ( $this->get_opt( 'nofollow' ) ) {
            $has_follow = (strpos( $rel, 'follow' ) !== FALSE);

            // when doesn't have "follow" (or already "nofollow")
            if (! $has_follow || $this->get_opt( 'overwrite_follow' )) {
                if ($has_follow) {
                    // remove "follow"
                    //$attrs[ 'rel' ] = ;
                }

    			self::add_attr_value( $attrs, 'rel', 'nofollow' );
            }
        }

		// set title
		$title_format = $this->get_opt( 'title' );
        $title = ( isset( $attrs[ 'title' ] ) ) ? $attrs[ 'title' ] : '';
		$attrs[ 'title' ] = str_replace( '%title%', $title, $title_format );

		// set user-defined class
		$class = $this->get_opt( 'class_name' );
		if ( $class )
			self::add_attr_value( $attrs, 'class', $class );

		// set icon class, unless no-icon class isset or another icon class ('ext-icon-...') is found or content contains image
		if ( $this->get_opt( 'icon' ) > 0
					AND ( ! $this->get_opt( 'no_icon_class' ) OR strpos( $attrs[ 'class' ], $this->get_opt( 'no_icon_class' ) ) === FALSE )
					AND strpos( $attrs[ 'class' ], 'ext-icon-' ) === FALSE
					AND !( $this->get_opt( 'image_no_icon' ) AND (bool) preg_match( '/<img([^>]*)>/is', $label )) ){
			$icon_class = 'ext-icon-'. $this->get_opt( 'icon', 'style' );
			self::add_attr_value( $attrs, 'class', $icon_class );
		}

        // set target
        $no_icon_class = $this->get_opt( 'no_icon_class' );
        $target = $this->get_opt( 'target' );

        // remove target
        unset($attrs[ 'target' ]);

        if ($this->get_opt( 'no_icon_same_window' )
					AND $no_icon_class AND strpos( $attrs[ 'class' ], $no_icon_class ) !== FALSE) {
            // open in same window
        } elseif ($target && $target !== '_none') {
            if ($this->get_opt( 'use_js' )) {
                // add data-attr for javascript
                $attrs['data-wpel-target'] = $target;
            } else {
                // set target value
                $attrs[ 'target' ] =  $target;
            }
        }

        // filter hook for changing attributes
		$attrs = apply_filters('wpel_external_link_attrs', $attrs, $original_attrs, $label);

		// create element code
        $created_link = self::create_link($label, $attrs);

		// filter
		$created_link = apply_filters('wpel_external_link', $created_link, $link, $label, $attrs, FALSE /* only used for backwards compatibility */);

		return $created_link;
	}

    /**
     * Create a HTML link <a>
     * @param string $label
     * @param array $attrs
     * @return string
     */
    public static function create_link($label, $attrs) {
		$created_link = '<a';

		foreach ( $attrs AS $key => $value ) {
			$created_link .= ' '. $key .'="'. $value .'"';
        }

		$created_link .= '>'. $label .'</a>';
        return $created_link;
    }

	/**
	 * Add value to attribute
	 * @param array  &$attrs
	 * @param string $attr_name
	 * @param string $value
	 * @param string $default  Optional, default NULL which means the attribute will be removed when (new) value is empty
	 * @return New value
	 */
	public static function add_attr_value( &$attrs, $attr_name, $value, $default = NULL ) {
		if ( key_exists( $attr_name, $attrs ) )
			$old_value = $attrs[ $attr_name ];

		if ( empty( $old_value ) )
			$old_value = '';

		$split = explode( ' ', strtolower( $old_value ) );

		if ( in_array( $value, $split ) ) {
			$value = $old_value;
		} else {
			$value = ( empty( $old_value ) )
								? $value
								: $old_value .' '. $value;
		}

		if ( empty( $value ) AND $default === NULL ) {
			unset( $attrs[ $attr_name ] );
		} else {
			$attrs[ $attr_name ] = $value;
		}

		return $value;
	}

} // End WP_External_Links Class

endif;

/* ommit PHP closing tag, to prevent unwanted whitespace at the end of the parts generated by the included files */
