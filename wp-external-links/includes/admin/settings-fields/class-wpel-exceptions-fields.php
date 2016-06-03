<?php
/**
 * Class WPEL_Exceptions_Fields
 *
 * @package  WPEL
 * @category WordPress Plugin
 * @version  2.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @link     https://github.com/freelancephp/WP-External-Links
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Exceptions_Fields extends FWP_Settings_Section_Fields_1x0x0
{

    /**
     * Initialize
     */
    protected function init()
    {
        $this->set_settings( array(
            'section_id'        => 'wpel-exceptions-fields',
            'page_id'           => 'wpel-exceptions-fields',
            'option_name'       => 'wpel-exceptions-settings',
            'option_group'      => 'wpel-exceptions-settings',
            'title'             => __( 'Exceptions', 'wpel' ),
            'fields'            => array(
                'apply_all' => array(
                    'label'         => __( 'Apply settings on:', 'wpel' ),
                    'class'         => 'js-wpel-apply',
                    'default_value' => '1',
                ),
                'apply_post_content' => array(
                    'class'         => 'js-wpel-apply-child wpel-hidden wpel-no-label ',
                    'default_value' => '1',
                ),
                'apply_comments' => array(
                    'class'         => 'js-wpel-apply-child wpel-hidden wpel-no-label',
                    'default_value' => '1',
                ),
                'apply_widgets' => array(
                    'class'         => 'js-wpel-apply-child wpel-hidden wpel-no-label',
                    'default_value' => '1',
                ),
                'ignore_script_tags' => array(
                    'label'         => __( 'Skip <code>&lt;script&gt;</code>:', 'wpel' ),
                    'default_value' => '1',
                ),
                'subdomains_as_internal_links' => array(
                    'label'         => __( 'Make subdomains internal:', 'wpel' ),
                ),
                'include_urls' => array(
                    'label' => __( 'Include external links by URL:', 'wpel' ),
                ),
                'exclude_urls' => array(
                    'label' => __( 'Exclude external links by URL:', 'wpel' ),
                ),
                'excludes_as_internal_links' => array(
                    'label'         => '',
                    'class'         => 'wpel-no-label',
                ),
            ),
        ) );
    }

    /**
     * Show field methods
     */

    protected function show_apply_all( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'All contents (the whole page)', 'wpel' )
            , '1'
            , ''
        );
    }

    protected function show_apply_post_content( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Post content', 'wpel' )
            , '1'
            , ''
        );
    }

    protected function show_apply_comments( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Comments', 'wpel' )
            , '1'
            , ''
        );
    }

    protected function show_apply_widgets( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'All widgets', 'wpel' )
            , '1'
            , ''
        );
    }

    protected function show_ignore_script_tags( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Ignore all links in <code>&lt;script&gt;</code> blocks', 'wpel' )
            , '1'
            , ''
        );
    }

    protected function show_subdomains_as_internal_links( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Threat all links to the site\'s domain and subdomains as internal links', 'wpel' )
            , '1'
            , ''
        );
    }

    protected function show_include_urls( array $args )
    {
        $this->get_html_fields()->text_area( $args[ 'key' ], array(
            'class' => 'large-text',
            'rows'  => 6,
            'placeholder' => __( 'Put each url on a separate row. Be as specific as you want, for example:'. "\n". "\n"
                            .'sub.domain.com'. "\n"
                            .'somedomain.org'. "\n"
                            .'//otherdomain.net/some-slug'
                            . "\n" .'http://otherdomain.net', 'wpel' ),
        ) );
    }

    protected function show_exclude_urls( array $args )
    {
        $this->get_html_fields()->text_area( $args[ 'key' ], array(
            'class' => 'large-text',
            'rows'  => 6,
            'placeholder' => __( 'Put each url on a separate row. Be as specific as you want, for example:'. "\n". "\n"
                            .'sub.domain.com'. "\n"
                            .'somedomain.org'. "\n"
                            .'//otherdomain.net/some-slug'
                            . "\n" .'http://otherdomain.net', 'wpel' ),
        ) );
    }

    protected function show_excludes_as_internal_links( array $args )
    {
        $this->get_html_fields()->check_with_label(
            $args[ 'key' ]
            , __( 'Treat excluded links as internal links', 'wpel' )
            , '1'
            , ''
        );
    }

    /**
     * Validate and sanitize user input before saving to databse
     * @param array $new_values
     * @param array $old_values
     * @return array
     */
    protected function before_update( array $new_values, array $old_values )
    {
        $update_values = $new_values;
        $is_valid = true;

        $is_valid = $is_valid && in_array( $new_values[ 'apply_post_content' ], array( '', '1' ) );
        $is_valid = $is_valid && in_array( $new_values[ 'apply_comments' ], array( '', '1' ) );
        $is_valid = $is_valid && in_array( $new_values[ 'apply_widgets' ], array( '', '1' ) );
        $is_valid = $is_valid && in_array( $new_values[ 'apply_all' ], array( '', '1' ) );
        $is_valid = $is_valid && in_array( $new_values[ 'ignore_script_tags' ], array( '', '1' ) );
        $is_valid = $is_valid && in_array( $new_values[ 'subdomains_as_internal_links' ], array( '', '1' ) );
        $is_valid = $is_valid && in_array( $new_values[ 'excludes_as_internal_links' ], array( '', '1' ) );

        if ( false === $is_valid ) {
            // error when user input is not valid conform the UI, probably tried to "hack"
            $this->add_error( __( 'Something went wrong. One or more values were invalid.', 'wpel' ) );
            return $old_values;
        }

        if ( '' !== trim( $new_values[ 'include_urls' ] ) ) {
            $update_values[ 'include_urls' ] = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $new_values[ 'include_urls' ] ) ) );
        }

        if ( '' !== trim( $new_values[ 'exclude_urls' ] ) ) {
            $update_values[ 'exclude_urls' ] = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $new_values[ 'exclude_urls' ] ) ) );
        }

        return $update_values;
    }

}

/*?>*/
