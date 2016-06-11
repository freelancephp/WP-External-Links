<?php
/**
 * Class WPRun_Base_1x0x0
 *
 * Base class for concrete subclasses
 * All subclasses are singletons and can be instantiated with
 * the static "create()" factory method.
 *
 * @package  WPRun
 * @category WordPress Library
 * @version  1.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WPRun-WordPress-Development
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
abstract class WPRun_Base_1x0x0
{

    const RETURN_VOID = '__VOID__';

    /**
     * Page hook
     * If page hook isset, will only apply hook methods when page hook is the current screen id
     * @var string
     */
    protected $page_hook = null;

    /**
     * Automatically set action and filter methods
     * @var boolean
     */
    protected $autoset_hook_methods = true;

    /**
     * @var string
     */
    protected $action_prefix = 'action_';

    /**
     * @var string
     */
    protected $filter_prefix = 'filter_';

    /**
     * @var array
     */
    protected $default_settings = array();

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
     * @return WPRun_Base_1x0x0
     * @triggers E_USER_NOTICE  Class already created
     */
    final public static function create()
    {
        $class_name = get_called_class();
        $arguments = func_get_args();

        // check if instance of this class already exists
        if ( key_exists( $class_name, self::$instances ) ) {
            trigger_error( 'Class "'. $class_name .'" was already created.' );
            return;
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

        if ( true === $this->autoset_hook_methods ) {
            $this->set_hook_methods();
        }

        // call init method
        $method_name = 'init';

        if ( method_exists( $this, $method_name ) ) {
            $method_reflection = new ReflectionMethod( get_called_class(), $method_name );

            if ( $method_reflection->isProtected() ) {
                call_user_func_array( array( $this, $method_name ), $this->arguments );
            } else {
                trigger_error( 'Method "'. $method_name .'" should be made protected in class "'. get_called_class() .'".' );
            }
        }
    }

    /**
     * @return WPRun_Base_1x0x0
     */
    final public static function get_instance()
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
        if ( empty( $this->settings ) ) {
            $this->settings = wp_parse_args( $settings, $this->default_settings );
        } else {
            $this->settings = wp_parse_args( $settings, $this->settings );
        }
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
    final public static function show_template( $template_file_path, array $vars = array() )
    {
        if ( is_readable( $template_file_path ) ) {
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
    final public static function render_template( $template_file_path, array $vars = array() )
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
     * @triggers E_USER_NOTICE Method name not exists/callable
     */
    public function __call( $method_name, $arguments )
    {
        $return_value = self::magic_call( $method_name, $arguments );

        if ( self::RETURN_VOID === $return_value ) {
            trigger_error( 'Method name "'. $method_name .'" does not exists or cannot be called.' );
        }

        return $return_value;
    }

    /**
     * @param string $method_name
     * @param array  $arguments
     * @return mixed|void
     * @triggers E_USER_NOTICE Method name not exists/callable
     */
    public static function __callStatic( $method_name, $arguments )
    {
        $return_value = self::magic_call( $method_name, $arguments );

        if ( self::RETURN_VOID === $return_value ) {
            trigger_error( 'Method name "'. $method_name .'" does not exists or cannot be called.' );
        }

        return $return_value;
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

        return self::RETURN_VOID;
    }

    /**
     * Check and auto-initialize methods for hooks
     */
    final protected function set_hook_methods()
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
     * Must be public but for internal use only
     * to support PHP 5.3 which doesn't recognize being used within class scope
     * @param string $method_name
     * @return array
     */
    final public function _get_callback( $method_name )
    {
        return $this->get_callback( $method_name );
    }

    /**
     * Must be public but for internal use only
     * to support PHP 5.3 which doesn't recognize being used within class scope
     * @return string
     */
    final public function _get_page_hook()
    {
        return $this->page_hook;
    }

    /**
     * @param WPRun_Base_1x0x0 $self
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
            if ( function_exists( 'get_current_screen' ) && null !== $self->_get_page_hook() ) {
                if ( is_network_admin() ) {
                    $page_hook = $self->_get_page_hook() .'-network';
                } else {
                    $page_hook = $self->_get_page_hook();
                }

                if ( get_current_screen()->id !== $page_hook ) {
                    return;
                }
            }

            return call_user_func_array( $self->_get_callback( $method_name ), func_get_args() );
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
