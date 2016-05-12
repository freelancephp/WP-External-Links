<?php
/**
 * Class WPRun_Base_0x7x0
 *
 * Base class for concrete subclasses
 * All subclasses are singletons and can be instantiated with
 * the static "create()" factory method.
 *
 * @package  WPRun
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
abstract class WPRun_Base_0x7x0
{

    /**
     * @var string
     */
    protected $action_prefix = 'action_';

    /**
     * @var string
     */
    protected $filter_prefix = 'filter_';

    /**
     * Extract template vars array as separate variables
     * Discouraged by the WordPress Code guidelines
     * @link https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/#dont-extract
     * @var boolean
     */
    protected $extract_template_vars = false;

    /**
     * @var array
     */
    protected $default_settings = array();

    /**
     * @var string
     */
    protected $page_hook = null;

    /**
     * @var array
     */
    private $settings = array();

    /**
     * Only for internal use
     * @var string
     */
    private $internal_callback_prefix = '_cb_';

    /**
     * List of (singleton) instances
     * Only for internal use
     * @var array
     */
    private static $instances = array();

    /**
     * @var array
     */
    private $arguments = array();

    /**
     * Factory method
     * @param mixed $param1 Optional, will be passed on to the constructor and init() method
     * @param mixed $paramN Optional, will be passed on to the constructor and init() method
     * @return WPRun_Base_0x7x0|false
     */
    final public static function create()
    {
        $class_name = get_called_class();
        $arguments = func_get_args();

        // check if instance of this class already exists
        if ( key_exists( $class_name, self::$instances ) ) {
            return false;
        }

        // pass all arguments to constructor
        $instance = new $class_name( $arguments );

        return $instance;
    }

    /**
     * Constructor
     * @triggers E_USER_NOTICE
     */
    private function __construct( array $arguments )
    {
        $class_name = get_called_class();
        self::$instances[ $class_name ] = $this;

        $this->arguments = $arguments;

        $this->init_methods();

        // call init
        if ( method_exists( $this, 'init' ) ) {
            $method_reflection = new ReflectionMethod( get_called_class(), 'init' );

            if ( $method_reflection->isProtected() ) {
                call_user_func_array( array( $this, 'init' ), $arguments );
            } else {
                trigger_error( 'Method "init" should be made protected in class "'. get_called_class() .'".' );
            }
        }
    }

    /**
     * @return WPRun_Base_0x7x0
     */
    public static function get_instance()
    {
        $class_name = get_called_class();
        return self::$instances[ $class_name ];
    }

    /**
     * @param string $key
     * @return mixed
     */
    final public function get_setting( $key )
    {
        return $this->settings[ $key ];
    }

    /**
     * @param array $settings
     */
    final protected function set_settings( array $settings )
    {
        $this->settings = wp_parse_args( $settings, $this->default_settings );
    }

    /**
     * Get argument passed on to the constructor
     * @param integer $index Optional, when null return all arguments
     * @return mixed|null
     */
    final protected function get_argument( $index = null )
    {
        // return all arguments when no index given
        if ( null === $index ) {
            return $this->arguments;
        }

        if ( !isset( $this->arguments[ $index ] ) ) {
            return null;
        }

        return $this->arguments[ $index ];
    }

    /**
     * @param string $template_file_path
     * @param array $vars Optional
     * @triggers E_USER_NOTICE Template file not readable
     */
    final protected function show_template( $template_file_path, array $vars = array() )
    {
        if ( is_readable( $template_file_path ) ) {
            if ( true === $this->extract_template_vars ) {
                // create separate template variables
                extract( $vars );
            }

            // show file
            include $template_file_path;
        } else {
            trigger_error( 'Template file "' . $template_file_path . '" is not readable or may not exists.' );
        }
    }

    /**
     * @param string $template_file_path
     * @param array  $vars Optional
     * @triggers E_USER_NOTICE Template file not readable
     */
    final protected function render_template( $template_file_path, array $vars = array() )
    {
        // start output buffer
        ob_start();

        // output template
        $this->show_template( $template_file_path, $vars );

        // get the view content
        $content = ob_get_contents();

        // clean output buffer
        ob_end_clean();

        return $content;
    }

    /**
     * Get a callable to a method in current instance, when called will be
     * caught by __callStatic(), were the magic happens
     * @param string $method_name
     * @return callable
     */
    final protected function get_callback( $method_name )
    {
        return array( get_called_class(), $this->internal_callback_prefix . $method_name );
    }

    /**
     * @param string $method_name
     * @param array  $arguments
     * @return mixed|void
     */
    public function __call( $method_name, $arguments )
    {
        return self::magic_call( $method_name, $arguments );
    }

    /**
     * @param string $method_name
     * @param array  $arguments
     * @return mixed|void
     */
    public static function __callStatic( $method_name, $arguments )
    {
        return self::magic_call( $method_name, $arguments );
    }

    /**
     * @param string $method_name
     * @param array $arguments
     * @return mixed|void
     */
    final protected static function magic_call( $method_name, $arguments )
    {
        $class_name = get_called_class();
        $instance = self::$instances[ $class_name ];

        // catch callbacks set by get_callback() method
        // this way callbacks can also be implemented as protected
        $given_callback_name = self::fetch_name_containing_prefix( $instance->internal_callback_prefix, $method_name );

        // normal callback
        if ( null !== $given_callback_name ) {
            $real_args = $arguments;

            $given_method_name = $given_callback_name;

            $callable = array( $instance, $given_method_name );

            if ( is_callable( $callable ) ) {
                return call_user_func_array( $callable, $real_args );
            }
        }
    }

    /**
     * Check and auto-initialize methods for hooks, shortcodes and template tags
     */
    private function init_methods()
    {
        $methods = get_class_methods( $this );

        foreach ( $methods as $method_name ) {
            $action_name = self::fetch_name_containing_prefix( $this->action_prefix, $method_name );
            if ( null !== $action_name ) {
                self::add_to_hook( $this, 'action', $action_name, $method_name );
                continue;
            }

            $filter_name = self::fetch_name_containing_prefix( $this->filter_prefix, $method_name );
            if ( null !== $filter_name ) {
                self::add_to_hook( $this, 'filter', $filter_name, $method_name );
                continue;
            }
        }
    }

    /**
     * @param WPRun_Base_0x7x0 $self
     * @param string $hook_type "action" or "filter"
     * @param string $hook_name
     * @param string $method_name
     * @triggers E_USER_NOTICE
     */
    private static function add_to_hook( $self, $hook_type, $hook_name, $method_name )
    {
        // fetch priority outof method name
        $split_method_Name = explode( '_', $method_name );
        $last = end( $split_method_Name );

        if ( is_numeric( $last ) ) {
            $priority = (int) $last;
            $wp_hook_name = str_replace( '_' . $last, '', $hook_name );
        } else {
            $priority = 10;
            $wp_hook_name = $hook_name;
        }

        // get the method's number of params
        $method_reflection = new ReflectionMethod( get_called_class(), $method_name );
        $accepted_args = $method_reflection->getNumberOfParameters();

        // check if actions and filters are applied for page hook
        $callback = function () use ( $self, $method_name ) {
            if ( function_exists( 'get_current_screen' ) && null !== $self->page_hook ) {
                if ( get_current_screen()->id !== $self->page_hook ) {
                    return;
                }
            }

            return call_user_func_array( array( $self, $method_name), func_get_args() );
        };

        if ( 'action' === $hook_type ) {
            add_action( $wp_hook_name, $callback, $priority, $accepted_args );
        } elseif ('filter' === $hook_type) {
            add_filter( $wp_hook_name, $callback, $priority, $accepted_args );
        } else {
            trigger_error( '"' . $hook_type . '" is not a valid hookType.' );
        }
    }

    /**
     * @param string $prefix
     * @param string $name
     * @return string|null
     */
    private static function fetch_name_containing_prefix( $prefix, $name )
    {
        $prefix_length = strlen( $prefix );

        if ( $prefix !== substr( $name, 0, $prefix_length) ) {
            return null;
        }

        $fetchedName = substr( $name, $prefix_length );
        return $fetchedName;
    }

}

/*?>*/
