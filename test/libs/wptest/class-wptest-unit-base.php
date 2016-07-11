<?php
/**
 * Class WPTest_Unit_Base
 *
 * Parent for all Unit Test
 *
 * @package  WPTest
 * @category WordPress Library
 * @version  1.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class WPTest_Unit_Base extends PHPUnit_Framework_TestCase
{

    /*
    public static function setUpBeforeClass() {}
    public static function tearDownAfterClass() {}

    protected function setUp() {}
    protected function tearDown() {}

    public function testIncomplete ()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
     */

    /**
     * Helper to get a clear mocked function
     * @param string $funcName
     * @return \WPTest_Mock_Function
     */
    public static function mockFunction($funcName)
    {
        if (!class_exists('WPTest_Mock_Function')) {
            require_once 'class-wptest-mock-function.php';
        }

        return WPTest_Mock_Function::getMock($funcName);
    }

}

/*?>*/
