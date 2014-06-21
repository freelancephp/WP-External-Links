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
				wp_enqueue_script( 'wp-external-links', plugins_url( 'js/wp-external-links.js', WP_EXTERNAL_LINKS_FILE ), array(), WP_EXTERNAL_LINKS_VERSION, (bool) $this->get_opt( 'load_in_footer' ) );

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

		if ( $this->get_opt( 'phpquery' ) ) {
			// Include phpQuery
			if ( ! class_exists( 'phpQuery' ) ) {
				require_once( 'phpQuery.php' );
			}

			return $this->filter_phpquery( $content );
		} else {
			return $this->filter( $content );
		}
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
	 * @param string $rel
	 * @return boolean
	 */
	private function is_external( $href, $rel ) {
		return ( isset( $href ) AND ( ( strpos( $href, strtolower( get_bloginfo( 'wpurl' ) ) ) === FALSE )
                                            AND ( substr( $href, 0, 7 ) == 'http://'
                                                    OR substr( $href, 0, 8 ) == 'https://'
                                                    OR substr( $href, 0, 6 ) == 'ftp://'
                                                    OR substr( $href, 0, 2 ) == '//' ) ) );
	}

    /**
     * Is an ignored link
     * @param string $href
     * @return boolean
     */
    private function is_ignored( $href ) {
		// check if this links should be ignored
		for ( $x = 0, $count = count($this->ignored); $x < $count; $x++ ) {
			if ( strrpos( $href, $this->ignored[ $x ] ) !== FALSE )
				return TRUE;
		}

        return FALSE;
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
        $created_link = $link;

        // parse attributes
		$attrs = $matches[ 1 ];
		$attrs = stripslashes( $attrs );
		$attrs = $this->parse_attrs( $attrs );

		$rel = ( isset( $attrs[ 'rel' ] ) ) ? strtolower( $attrs[ 'rel' ] ) : '';

		// href preperation
		$href = $attrs[ 'href' ];
		$href = strtolower( $href );
		$href = trim( $href );

        // checks
        $is_external = $this->is_external( $href, $rel );
        $is_ignored = $this->is_ignored( $href );
        $has_rel_external =  (strpos( $rel, 'external' ) !== FALSE);

		// is an internal link?
        // rel=external will be threaded as external link
		if ( ! $is_external && ! $has_rel_external) {
    		return apply_filters('wpel_internal_link', $created_link, $label, $attrs);
        }

        // is an ignored link?
        // rel=external will be threaded as external link
        if ( $is_ignored && ! $has_rel_external ) {
    		return apply_filters('wpel_external_link', $created_link, $link, $label, $attrs, TRUE);
        }

		// set rel="external" (when not already set)
		if ( $this->get_opt( 'external' ) )
			$this->add_attr_value( $attrs, 'rel', 'external' );

		// set rel="nofollow" when doesn't have "follow" (or already "nofollow")
		if ( $this->get_opt( 'nofollow' ) AND strpos( $rel, 'follow' ) === FALSE )
			$this->add_attr_value( $attrs, 'rel', 'nofollow' );

		// set title
		$title_format = $this->get_opt( 'title' );
        $title = ( isset( $attrs[ 'title' ] ) ) ? $attrs[ 'title' ] : '';
		$attrs[ 'title' ] = str_replace( '%title%', $title, $title_format );

		// set user-defined class
		$class = $this->get_opt( 'class_name' );
		if ( $class )
			$this->add_attr_value( $attrs, 'class', $class );

		// set icon class, unless no-icon class isset or another icon class ('ext-icon-...') is found or content contains image
		if ( $this->get_opt( 'icon' ) > 0
					AND ( ! $this->get_opt( 'no_icon_class' ) OR strpos( $attrs[ 'class' ], $this->get_opt( 'no_icon_class' ) ) === FALSE )
					AND strpos( $attrs[ 'class' ], 'ext-icon-' ) === FALSE
					AND !( $this->get_opt( 'image_no_icon' ) AND (bool) preg_match( '/<img([^>]*)>/is', $label )) ){
			$icon_class = 'ext-icon-'. $this->get_opt( 'icon', 'style' );
			$this->add_attr_value( $attrs, 'class', $icon_class );
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
                $attrs[ 'target' ] =  $this->get_opt( 'target' );
            }
        }

		// create element code
		$created_link = '<a';

		foreach ( $attrs AS $key => $value ) {
			$created_link .= ' '. $key .'="'. $value .'"';
        }

		$created_link .= '>'. $label .'</a>';

		// filter
		$created_link = apply_filters('wpel_external_link', $created_link, $link, $label, $attrs, FALSE);

		return $created_link;
	}

	/**
	 * Add value to attribute
	 * @param array  $attrs
	 * @param string $attr
	 * @param string $value
	 * @param string $default  Optional, default NULL which means tje attribute will be removed when (new) value is empty
	 * @return New value
	 */
	public function add_attr_value( &$attrs, $attr_name, $value, $default = NULL ) {
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

	/**
	 * Experimental phpQuery...
	 */

	/**
	 * Filter content
	 * @param string $content
	 * @return string
	 */
	private function filter_phpquery( $content ) {
		// Workaround: remove <head>-attributes before using phpQuery
		$regexp_head = '/<head(>|\s(.*?)>)>/is';
		$clean_head = '<head>';

		// set simple <head> without attributes
		preg_match( $regexp_head, $content, $matches );

		if( count( $matches ) > 0 ) {
			$original_head = $matches[ 0 ];
			$content = str_replace( $original_head, $clean_head, $content );
		}

		//phpQuery::$debug = true;

		// set document
		$doc = phpQuery::newDocument( $content );

		$excl_sel = $this->get_opt( 'filter_excl_sel' );

		// set excludes
		if ( ! empty( $excl_sel ) ) {
			$excludes = $doc->find( $excl_sel );
			$excludes->filter( 'a' )->attr( 'excluded', true );
			$excludes->find( 'a' )->attr( 'excluded', true );
		}

		// get <a>-tags
		$links = $doc->find( 'a' );

		// set links
		$count = count( $links );

		for( $x = 0; $x < $count; $x++ ) {
			$a = $links->eq( $x );

			if ( ! $a->attr( 'excluded' ) )
				$this->set_link_phpquery( $links->eq( $x ) );
		}

		// remove excluded
		if ( ! empty( $excl_sel ) ) {
			$excludes = $doc->find( $excl_sel );
			$excludes->filter( 'a' )->removeAttr( 'excluded' );
			$excludes->find( 'a' )->removeAttr( 'excluded' );
		}

		// remove style when no icon classes are found
		if ( strpos( $doc, 'ext-icon-' ) === FALSE ) {
			// remove icon css
			$css = $doc->find( 'link#wp-external-links-css' )->eq(0);
			$css->remove();
		}

		// get document content
		$content = (string) $doc;

		if( isset( $original_head ) ) {
			// recover original <head> with attributes
			$content = str_replace( $clean_head, $original_head, $content );
		}

		return $content;
	}

	/**
	 * Set link...
	 * @param Node $a
	 * @return Node
	 */
	public function set_link_phpquery( $a ) {
		$href = strtolower( $a->attr( 'href' ) . '' );
		$rel = strtolower( $a->attr( 'rel' ) . '' );

		// check if it is an external link and not excluded
		if ( ! $this->is_external( $href, $rel ) || $this->is_ignored( $href ) )
			return $a;

		// add "external" to rel-attribute
		if ( $this->get_opt( 'external' ) ){
			$this->add_attr_value_phpquery( $a, 'rel', 'external' );
		}

		// add "nofollow" to rel-attribute, when doesn't have "follow"
		if ( $this->get_opt( 'nofollow' ) AND strpos( $rel, 'follow' ) === FALSE ){
			$this->add_attr_value_phpquery( $a, 'rel', 'nofollow' );
		}

		// set title
		$title = str_replace( '%title%', $a->attr( 'title' ), $this->get_opt( 'title' ) );
		$a->attr( 'title', $title );

		// add icon class, unless no-icon class isset or another icon class ('ext-icon-...') is found
		if ( $this->get_opt( 'icon' ) > 0 AND ( ! $this->get_opt( 'no_icon_class' ) OR strpos( $a->attr( 'class' ), $this->get_opt( 'no_icon_class' ) ) === FALSE ) AND strpos( $a->attr( 'class' ), 'ext-icon-' ) === FALSE  ){
			$icon_class = 'ext-icon-'. $this->get_opt( 'icon' );
			$a->addClass( $icon_class );
		}

		// add user-defined class
		if ( $this->get_opt( 'class_name' ) ){
			$a->addClass( $this->get_opt( 'class_name' ) );
		}

		// set target
		if ( $this->get_opt( 'target' ) != '_none' AND ! $this->get_opt( 'use_js' ) AND ( ! $this->get_opt( 'no_icon_same_window' ) OR ! $this->get_opt( 'no_icon_class' ) OR strpos( $a->attr( 'class' ), $this->get_opt( 'no_icon_class' ) ) === FALSE ) )
			$a->attr( 'target', $this->get_opt( 'target' ) );

		return $a;
	}

	/**
	 * Add value to attribute
	 * @param Node   $node
	 * @param string $attr
	 * @param string $value
	 * @return New value
	 */
	private function add_attr_value_phpquery( $node, $attr, $value ) {
		$old_value = $node->attr( $attr );

		if ( empty( $old_value ) )
			$old_value = '';

		$split = split( ' ', strtolower( $old_value ) );

		if ( in_array( $value, $split ) ) {
			$value = $old_value;
		} else {
			$value = ( empty( $old_value ) )
								? $value
								: $old_value .' '. $value;
		}

		$node->attr( $attr, $value );

		return $value;
	}

} // End WP_External_Links Class

endif;

/* ommit PHP closing tag, to prevent unwanted whitespace at the end of the parts generated by the included files */