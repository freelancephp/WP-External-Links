<?php
include_once realpath(__DIR__ . '/../../../libs/wptest/class-wptest-unit-base.php');
include_once realpath(__DIR__ . '/../../../../wp-external-links/libs/wprun/class-wprun-base.php');
include_once realpath(__DIR__ . '/../../../../wp-external-links/includes/class-wpel-plugin.php');


class WPEL_PluginTest extends WPTest_Unit_Base
{

    private static $mockFuncs = array();

    public static function setUpBeforeClass()
    {
        self::$mockFuncs = array(
            'untrailingslashit'     => self::mockFunction('untrailingslashit'),
            'is_admin'              => self::mockFunction('is_admin'),
            'add_action'            => self::mockFunction('add_action'),
            'load_plugin_textdomain' => self::mockFunction('load_plugin_textdomain'),
            'get_file_data'         => self::mockFunction('get_file_data'),
        );

        self::$mockFuncs['untrailingslashit']->setReturnValue(__DIR__);
        self::$mockFuncs['is_admin']->setReturnValue(false);
        self::$mockFuncs['add_action']->setCallback(function ($key, $callback, $priority = 10, $accepted_args = 0) {
            call_user_func($callback);
        });
        self::$mockFuncs['get_file_data']->setReturnValue(array(
            'TextDomain'  => 'wp-external-links',
            'DomainPath'  => '/languages',
        ));

        WPEL_Plugin::create(__FILE__, __DIR__);
    }

    public function testCreate_Called_CallsUntrailingslashit()
    {
        $result = self::$mockFuncs['untrailingslashit']->calledWithArgs(array(__DIR__));
        $this->assertTrue($result);
    }

    public function testCreate_Called_CallsIsAdmin()
    {
        $result = self::$mockFuncs['is_admin']->calledOnce();
        $this->assertTrue($result);
    }

    public function testCreate_Called_CallsAddAction()
    {
        $result = self::$mockFuncs['add_action']->calledOnce();
        $this->assertTrue($result);
    }

    public function testCreate_Called_CallsPluginsLoaded()
    {
        $result = self::$mockFuncs['add_action']->calledWithArgValue('plugins_loaded', 0);
        $this->assertTrue($result);

        $result = self::$mockFuncs['add_action']->calledWithArgValue(10, 2);
        $this->assertTrue($result);
    }

    public function testCreate_Called_CallsLoadPluginTextdomain()
    {
        $result = self::$mockFuncs['load_plugin_textdomain']->calledOnce();
        $this->assertTrue($result);

        $result = self::$mockFuncs['load_plugin_textdomain']->calledWithArgs(array(
            'wp-external-links',
            false,
            __DIR__ .'/languages'
        ));
        $this->assertTrue($result);
    }

    public function testCreate_Called_CheckCreatedComponents()
    {
        $this->assertInstanceOf('WPEL_Register_Hooks', WPEL_Register_Hooks::get_instance());
        $this->assertInstanceOf('WPEL_Register_Scripts', WPEL_Register_Scripts::get_instance());
        $this->assertInstanceOf('WPEL_Network_Page', WPEL_Network_Page::get_instance());
        $this->assertInstanceOf('WPEL_Network_Fields', WPEL_Network_Fields::get_instance());
        $this->assertInstanceOf('WPEL_Network_Admin_Fields', WPEL_Network_Admin_Fields::get_instance());
        $this->assertInstanceOf('WPEL_Settings_Page', WPEL_Settings_Page::get_instance());
        $this->assertInstanceOf('WPEL_External_Link_Fields', WPEL_External_Link_Fields::get_instance());
        $this->assertInstanceOf('WPEL_Internal_Link_Fields', WPEL_Internal_Link_Fields::get_instance());
        $this->assertInstanceOf('WPEL_Excluded_Link_Fields', WPEL_Excluded_Link_Fields::get_instance());
        $this->assertInstanceOf('WPEL_Admin_Fields', WPEL_Admin_Fields::get_instance());
        $this->assertInstanceOf('WPEL_Exceptions_Fields', WPEL_Exceptions_Fields::get_instance());
        $this->assertInstanceOf('FWP_Final_Output_1x0x0', FWP_Final_Output_1x0x0::get_instance());
        $this->assertInstanceOf('FWP_Widget_Output_1x0x0', FWP_Widget_Output_1x0x0::get_instance());
        $this->assertInstanceOf('WPEL_Front', WPEL_Front::get_instance());
        $this->assertInstanceOf('WPEL_Front_Ignore', WPEL_Front_Ignore::get_instance());
        $this->assertInstanceOf('WPEL_Template_Tags', WPEL_Template_Tags::get_instance());
        $this->assertInstanceOf('WPEL_Update', WPEL_Update::get_instance());
    }

    public function testGetPluginFile_Called_ReturnsPluginFile()
    {
        $result = WPEL_Plugin::get_plugin_file();
        $this->assertSame(__FILE__, $result);
    }

    public function testGetPluginDir_CalledWithoutParams_ReturnsPluginDir()
    {
        $result = WPEL_Plugin::get_plugin_dir();
        $this->assertSame(__DIR__, $result);
    }

    public function testGetPluginDir_GivenSubPath_ReturnsPluginDir()
    {
        $result = WPEL_Plugin::get_plugin_dir('/some/sub/path/');
        $this->assertSame(__DIR__ .'/some/sub/path/', $result);
    }

}

/**
 * Mock classes
 */
class WPEL_Register_Hooks extends WPRun_Base_1x0x0 {}
class WPEL_Register_Scripts extends WPRun_Base_1x0x0 {}
class WPEL_Network_Page extends WPRun_Base_1x0x0 {}
class WPEL_Network_Fields extends WPRun_Base_1x0x0 {}
class WPEL_Network_Admin_Fields extends WPRun_Base_1x0x0 {}
class WPEL_Settings_Page extends WPRun_Base_1x0x0 {}
class WPEL_External_Link_Fields extends WPRun_Base_1x0x0 {}
class WPEL_Internal_Link_Fields extends WPRun_Base_1x0x0 {}
class WPEL_Excluded_Link_Fields extends WPRun_Base_1x0x0 {}
class WPEL_Admin_Fields extends WPRun_Base_1x0x0 {}
class WPEL_Exceptions_Fields extends WPRun_Base_1x0x0 {}
class FWP_Final_Output_1x0x0 extends WPRun_Base_1x0x0 {}
class FWP_Widget_Output_1x0x0 extends WPRun_Base_1x0x0 {}
class WPEL_Front extends WPRun_Base_1x0x0 {}
class WPEL_Front_Ignore extends WPRun_Base_1x0x0 {}
class WPEL_Template_Tags extends WPRun_Base_1x0x0 {}
class WPEL_Update extends WPRun_Base_1x0x0 {}

/*?>*/
