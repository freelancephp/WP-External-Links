<?php
include_once realpath(__DIR__ . '/../../../libs/wptest/class-wptest-unit-base.php');
include_once realpath(__DIR__ . '/../../../../wp-external-links/libs/wprun/class-wprun-base.php');
include_once realpath(__DIR__ . '/../../../../wp-external-links/libs/fwp/component-bases/class-fwp-template-tag-base.php');
include_once realpath(__DIR__ . '/../../../../wp-external-links/includes/class-wpel-template-tags.php');


class WPEL_Template_TagsTest extends WPTest_Unit_Base
{

    public static function setUpBeforeClass()
    {
        WPEL_Template_Tags::create();
    }

    public function testCreate_Called_WpelFilterFunctionExists()
    {
        $result = function_exists('wpel_filter');
        $this->assertTrue($result);
    }

    public function testWpelFilterFunction_GivenContent_ReturnFilteredContent()
    {
        $result = wpel_filter('some content');
        $expected = '<filter>some content</filter>';
        $this->assertSame($expected, $result);
    }

}

/**
 * Mock WPEL_Front class
 */
class WPEL_Front
{
    public static function get_instance()
    {
        return new self;
    }

    public function scan($content)
    {
        return '<filter>'. $content .'</filter>';
    }

}

/*?>*/
