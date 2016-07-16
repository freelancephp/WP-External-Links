<?php
/**
 * Class FWP_Template_Tag_Base_1x0x0
 *
 * Public methods implemented in concrete subclasses will be automatically
 * created as template tags.
 *
 * @package  FWP
 * @category WordPress Library
 * @version  1.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WPRun-WordPress-Development
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
abstract class FWP_Template_Tag_Base_1x0x0 extends WPRun_Base_1x0x0
{

    /**
     * Action for "wp"
     * Create template tags
     */
    protected function action_wp()
    {
        // get public methods of parent class
        $parent_class = get_parent_class( $this );
        $reflection_parent_class = new ReflectionClass( $parent_class );
        $parent_methods = $reflection_parent_class->getMethods( ReflectionMethod::IS_PUBLIC  );

        // get public methods of current class
        $reflection_class = new ReflectionClass( get_called_class() );
        $class_methods = $reflection_class->getMethods( ReflectionMethod::IS_PUBLIC );

        // get only the public methods implemented in concrete class
        // these are the template tags
        $template_tag_refl_methods = array_diff( $class_methods, $parent_methods );

        foreach ( $template_tag_refl_methods as $refl_method ) {
            $this->create_template_tag( $refl_method->name );
        }
    }

    /**
     * Create template tag
     * @return void
     */
    protected function create_template_tag( $template_tag )
    {
        if ( function_exists( $template_tag ) ) {
            return;
        }

        // create global function
        $func_code = '';
        $func_code .= 'function '. $template_tag .'()';
        $func_code .= '{';
        $func_code .= '    $callable = array( '. get_called_class() .'::get_instance(), "'. $template_tag .'" );';
        $func_code .= '    return call_user_func_array( $callable, func_get_args() );';
        $func_code .= '}';

        eval( $func_code );
    }

}

/*?>*/
