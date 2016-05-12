<?php
/**
 * Class WPEL_Exclusions_Fields
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Exclusions_Fields extends WP_Settings_Section_Fields_0x7x0
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
            'description'       => __( 'Lorem ipsum...', 'wpel' ),
            'fields'            => array(
                'subdomain_as_internals' => array(
                    'label'             => __( 'Subdomains:', 'wpel' ),
                    'label_for'         => 'wpel-exceptions-settings-subdomain_as_internals',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'mark_as_external' => array(
                    'label'             => __( 'Treat internal links as external with URL\'s containing...:', 'wpel' ),
                    'label_for'         => 'wpel-exceptions-settings-mark_as_external',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'exclude_urls' => array(
                    'label'             => __( 'Exclude links with URL\'s containing...:', 'wpel' ),
                    'label_for'         => 'wpel-exceptions-settings-exclude_urls',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'exclusions_as_internal_links' => array(
                    'label'             => __( 'Excluded links:', 'wpel' ),
                    'label_for'         => 'wpel-exceptions-settings-exclusions_as_internal_links',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
            ),
        ) );
    }

    /**
     * Show field
     * @param array $args
     */
    protected function show_field( array $args )
    {
        $html_fields = new WP_HTML_Fields_0x7x0(
            $args[ 'values' ]
            , 'wpel-exceptions-settings-%s'
            , 'wpel-exceptions-settings[%s]'
        );

        switch ( $args[ 'key' ] ) {
            case 'subdomain_as_internals':
                echo '<label>';
                $html_fields->check( $args[ 'key' ], '' );
                echo ' Treat links to subdomains as internal links';
                echo '</label>';
            break;

            case 'mark_as_external':
                $html_fields->text_area( $args[ 'key' ] );
            break;

            case 'exclude_urls':
                $html_fields->text_area( $args[ 'key' ] );
            break;

            case 'exclusions_as_internal_links':
                echo '<label>';
                $html_fields->check( $args[ 'key' ], '' );
                echo ' Treat excluded links as internal links';
                echo '</label>';
            break;
        }
    }

    /**
     * Validate and sanitize user input before saving to databse
     * @param array $new_values
     * @param array $old_values
     * @return array
     */
    protected function update( array $new_values, array $old_values )
    {
        $values = $new_values;
        
        if ( '' === trim ( $new_values[ 'some-key' ] ) ) {
            $values = $old_values;
            $this->add_error( __( 'Some key is required.', 'wprun-demo' ) );
        }

        return $values;
    }

}

/*?>*/
