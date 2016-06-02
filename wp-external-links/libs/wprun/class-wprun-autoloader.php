<?php
/**
 * Class WPRun_Autoloader_1x0x0
 *
 * @package  WPRun
 * @category WordPress Library
 * @version  1.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WPRun-WordPress-Development
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class WPRun_Autoloader_1x0x0
{

    /**
     * @var array
     */
    private $settings = array(
        'file_name_prefix'      => 'class-',
        'replace_dashes_with'   => '-',
    );

    /**
     * @var array
     */
    private $paths = array();

    /**
     * Constructor
     * @param array $settings Optional
     */
    final public function __construct( array $settings = array() )
    {
        $this->settings = wp_parse_args( $settings, $this->settings );

        spl_autoload_register( array( $this, 'load_class' ) );
    }

    /**
     * Add path to folder containing classes
     * @param string $path
     * @param boolean $include_subfolders Optional
     * @return void
     */
    final public function add_path( $path, $include_subfolders = false )
    {
        if ( in_array( $path, $this->paths ) ) {
            return;
        }

        $this->paths[] = $path;

        // include subfolders
        if ( true === $include_subfolders ) {
            $entries = scandir( $path );

            foreach ( $entries as $entry ) {
                if ( '.' === $entry || '..' === $entry ) {
                    continue;
                }

                $item = $path . DIRECTORY_SEPARATOR . $entry;

                if ( is_dir( $item ) ) {
                    $this->add_path( $item, true );
                }
            }
        }
     }

    /**
     * Get all paths
     * @return array
     */
    final public function get_paths()
    {
        return $this->paths;
    }

    /**
     * Loads a class file
     * @param string $class_name
     * @return void
     */
    public function load_class( $class_name )
    {
        // remove version postfix
        $pure_class_name = preg_replace( '/_\d+x\d+x\d+/', '', $class_name );

        $file_name = '';
        $file_name .= $this->settings['file_name_prefix'];
        $file_name .= str_replace( '_', $this->settings['replace_dashes_with'], $pure_class_name );
        $file_name .= '.php';

        $lower_file_name = strtolower( $file_name );

        foreach ( $this->paths as $path ) {
            $file_path = $path . DIRECTORY_SEPARATOR . $lower_file_name;

            if ( file_exists( $file_path ) ) {
                require_once $file_path;

                // return if class is available else it was probably
                // the wrong version (postfix) and should continue looking
                if ( class_exists( $class_name ) ) {
                    return;
                }
            }
        }
    }

}

/*?>*/
