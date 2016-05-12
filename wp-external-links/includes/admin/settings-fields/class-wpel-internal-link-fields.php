<?php
/**
 * Class WPEL_Internal_Link_Fields
 *
 * @package  DWP
 * @category WordPress Plugin
 * @version  0.7.0
 * @author   Victor Villaverde Laan
 * @link     http://www.freelancephp.net/
 * @link     https://github.com/freelancephp/WPRun-Plugin-Base
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
final class WPEL_Internal_Link_Fields extends WP_Settings_Section_Fields_0x7x0
{

    /**
     * Initialize
     */
    protected function init()
    {
        $this->set_settings( array(
            'section_id'        => 'wpel-internal-link-fields',
            'page_id'           => 'wpel-internal-link-fields',
            'option_name'       => 'wpel-internal-link-settings',
            'option_group'      => 'wpel-internal-link-settings',
            'title'             => __( 'Internal Links', 'wpel' ),
            'description'       => __( 'Lorem ipsum...', 'wpel' ),
            'fields'            => array(
                'use_settings_interal_links' => array(
                    'label'             => __( 'Settings for internal links:', 'wpel' ),
                    'label_for'         => 'wpel-internal-link-settings-use_settings_interal_links',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'target' => array(
                    'label'             => __( 'Open internal links in:', 'wpel' ),
                    'label_for'         => 'wpel-internal-link-settings-target',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'target_overwrite' => array(
                    'class'             => 'hide-all',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'rel_follow' => array(
                    'label'             => __( 'Set <code>nofollow</code>:', 'wpel' ),
                    'label_for'         => 'wpel-internal-link-settings-rel_follow',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'rel_follow_overwrite' => array(
                    'class'             => 'hide-all',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'title' => array(
                    'label'             => __( 'Set <code>title</code>:', 'wpel' ),
                    'label_for'         => 'wpel-internal-link-settings-title',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'class' => array(
                    'label'             => __( 'Add to <code>class</code>:', 'wpel' ),
                    'label_for'         => 'wpel-internal-link-settings-class',
                    'input_callback'    => $this->get_callback( 'show_field' ),
                ),
                'class_overwrite' => array(
                    'class'             => 'hide-all',
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
            , 'wpel-internal-link-settings-%s'
            , 'wpel-internal-link-settings[%s]'
        );

        switch ( $args[ 'key' ] ) {
            case 'use_settings_interal_links':
                echo '<label>';
                $html_fields->check( $args[ 'key' ], '' );
                echo __( ' Apply settings for internal links'
                        . ' <span class="description">(only enable when needed to prevent '
                        . 'using unnescessary resources)</span>', 'wpel' );
                echo '</label>';
            break;

            case 'target':
                $html_fields->select(
                    $args[ 'key' ]
                    , null
                    , array(
                        ''          => __( 'Keep as is (no changes)', 'wpel' ),
                        '_none'     => __( 'Same window or tab (<code>_none</code>)', 'wpel' ),
                        '_blank'    => __( 'New window or tab (<code>_blank</code>)', 'wpel' ),
                        '_top'      => __( 'Topmost frame (<code>_top</code>)', 'wpel' ),
                        '_new'      => __( 'Seperate window or tab (<code>_new</code>)', 'wpel' ),
                    )
                );

                echo '<p>';
                echo '<label>';
                $html_fields->check( $args[ 'key' ], '', '' );
                echo __( ' Overwrite existing values. '
                        . '<span class="description">(also use this setting for links already '
                        . 'containing <code>target</code> value)</span>', 'wpel' );
                echo '</label>';
                echo '</p>';
            break;

            case 'rel_follow':
                $html_fields->select(
                    $args[ 'key' ]
                    , null
                    , array(
                        ''          => __( 'Keep as is (no changes)', 'wpel' ),
                        'follow'    => __( 'follow', 'wpel' ),
                        'nofollow'  => __( 'nofollow', 'wpel' ),
                    )
                );

                echo '<p>';
                echo '<label>';
                $html_fields->check( $args[ 'key' ], '', '' );
                echo __( ' Overwrite existing values. '
                        . '<span class="description">(also use this setting for links already containing '
                        . '<code>follow</code> or <code>nofollow</code> value)</span>', 'wpel' );
                echo '</label>';
                echo '</p>';
            break;

            case 'title':
                $html_fields->text( $args[ 'key' ] );
                echo '<p class="description">';
                echo __( 'Use this <code>{title}</code> for the original title value '
                        . 'and <code>{text}</code> for the link text as shown on the page'
                        . '<br>Leave empty to unchange the title attributes', 'wpel' );
                echo '</p>';
            break;

            case 'class':
                $html_fields->text( $args[ 'key' ] );

                echo '<p>';
                echo '<label>';
                $html_fields->check( $args[ 'key' ], '', '' );
                echo __( ' Overwrite existing values. '
                        . '<span class="description">(remove existing classes in links)</span>', 'wpel' );
                echo '</label>';
                echo '</p>';
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
