<?php
/**
 * Class WPEL_Exceptions_Fields
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Exceptions_Fields extends WP_Settings_Section_Fields_0x7x0
{

    /**
     * Initialize
     */
    protected function init()
    {
        delete_option('wpel-apply-settings');
        $this->set_settings( array(
            'section_id'        => 'wpel-exceptions-fields',
            'page_id'           => 'wpel-exceptions-fields',
            'option_name'       => 'wpel-exceptions-settings',
            'option_group'      => 'wpel-exceptions-settings',
            'title'             => __( 'Exceptions', 'wpel' ),
            'description'       => __( 'Lorem', 'wpel' ),
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
                'include_urls' => array(
                    'label' => __( 'Include external links by URL:', 'wpel' ),
                ),
                'exclude_urls' => array(
                    'label' => __( 'Exclude external links by URL:', 'wpel' ),
                ),
                'excludes_as_internal_links' => array(
                    'label' => __( 'Excludes as internal links:', 'wpel' ),
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

    protected function show_include_urls( array $args )
    {
        $this->get_html_fields()->text_area( $args[ 'key' ], array(
            'class' => 'large-text',
            'rows'  => 6,
            'placeholder' => 'Put each url on a separate row. Be as specific as you want, for example:'. "\n". "\n"
                            .'sub.domain.com'. "\n"
                            .'somedomain.org'. "\n"
                            .'//otherdomain.net/some-slug'
                            . "\n" .'http://otherdomain.net',
        ) );
    }

    protected function show_exclude_urls( array $args )
    {
        $this->get_html_fields()->text_area( $args[ 'key' ], array(
            'class' => 'large-text',
            'rows'  => 6,
            'placeholder' => 'Put each url on a separate row. Be as specific as you want, for example:'. "\n". "\n"
                            .'sub.domain.com'. "\n"
                            .'somedomain.org'. "\n"
                            .'//otherdomain.net/some-slug'
                            . "\n" .'http://otherdomain.net',
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
     * @param array $values
     * @return array
     */
    protected function prepare_field_values( array $values )
    {
        $prepared_values = $values;

        if ( ! empty( $values[ 'include_urls' ] ) ) {
            $prepared_values[ 'include_urls' ] = implode( "\n", $values[ 'include_urls' ] );
        }

        if ( ! empty( $values[ 'exclude_urls' ] ) ) {
            $prepared_values[ 'exclude_urls' ] = implode( "\n", $values[ 'exclude_urls' ] );
        }

        return $prepared_values;
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

        $is_valid_check = function ( $value ) {
            $valid_vals = array( '', '1' );
            return in_array( $value, $valid_vals );
        };

        if ( ! $is_valid_check( $new_values[ 'apply_post_content' ] )
                || ! $is_valid_check( $new_values[ 'apply_comments' ] )
                || ! $is_valid_check( $new_values[ 'apply_widgets' ] )
                || ! $is_valid_check( $new_values[ 'apply_all' ] ) ) {
            $update_values = $old_values;
            $this->add_error( __( 'Wrong values!', 'wprun-demo' ) );
        }

        if ( '' !== trim( $new_values[ 'include_urls' ] ) ) {
            $urls = explode( "\n", $new_values[ 'include_urls' ] );
            $update_values[ 'include_urls' ] = $urls;
        }

        if ( '' !== trim( $new_values[ 'exclude_urls' ] ) ) {
            $urls = explode( "\n", $new_values[ 'exclude_urls' ] );
            $update_values[ 'exclude_urls' ] = $urls;
        }

        if ( ! in_array( $new_values[ 'excludes_as_internal_links' ], array( '', '1' ) ) ) {
            $update_values = $old_values;
            $this->add_error( __( '"Exclude as internal links" contains wrong value.', 'wprun-demo' ) );
        }

        return $update_values;
    }

}

/*?>*/
