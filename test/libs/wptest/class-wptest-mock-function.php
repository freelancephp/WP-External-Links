<?php
/**
 * Class WPTest_Mock_Function
 *
 * Create mocks of WP functions
 * Only works when WP functions are NOT available in tests
 *
 * @package  WPTest
 * @category WordPress Library
 * @version  1.0.0
 * @author   Victor Villaverde Laan
 * @link     http://www.finewebdev.com
 * @license  Dual licensed under the MIT and GPLv2+ licenses
 */
class WPTest_Mock_Function
{

    /**
     * Instances of mocked functions
     * @var array
     */
    protected static $functionMocks = array();

    /**
     * Applied return value
     * @var mixed
     */
    protected $returnValue = null;

    /**
     * Callback triggered when function is called
     * @var callable
     */
    protected $callback = null;

    /**
     * Expected calls on this function
     * @var array
     */
    protected $expectedCalls = array();

    /**
     * Made calls on this function
     * @var array
     */
    protected $calls = array();

    /**
     * Get the cleared mock of given function name (mock will be created if not yet exists)
     * Be carefull, using this method multiple times for same function, will return and clear same instance
     * @param string  $funcName
     * @return \WPTest_Mock_Function
     * @throws Exception
     */
    public static function getMock($funcName)
    {
        if (function_exists($funcName)) {
        // mock already exists
            if (!key_exists($funcName, self::$functionMocks)) {
                throw new Exception('Function already exists and therefore could not be mocked.');
            }
        } else {
        // create mock
            eval('function ' . $funcName . '(){ return ' . __CLASS__ . '::_registerCall(__FUNCTION__, func_get_args()); }');
        }

        $mock = new self($funcName);
        self::$functionMocks[$funcName] = $mock;

        return $mock;
    }

    /**
     * Register when mock function is called (should NOT be called directly!)
     * @param array $args
     * @return mixed  Functions return value
     */
    public static function _registerCall($funcName, array $args = array())
    {
        $mock = self::$functionMocks[$funcName];
        $mock->calls[] = $args;

        // trigger callback when set
        $callback = $mock->getCallback();
        if (is_callable($callback)) {
            return call_user_func_array($callback, $args);
        }

        return $mock->returnValue;
    }

    /**
     * Get all created mocks
     */
    public static function getAllMocks()
    {
        return self::$functionMocks;
    }

    /**
     * No direct instantiation, should be done by self::getMock()
     */
    protected function __construct()
    {
    }

    /**
     * Set the return value for this function
     * @param mixed $returnValue
     */
    public function setReturnValue($returnValue)
    {
        $this->returnValue = $returnValue;
    }

    /**
     * Get the return value for this function
     * @return mixed
     */
    public function getReturnValue()
    {
        return $this->returnValue;
    }

    /**
     * Set callback for this mocked function
     * @param mixed $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * Get callback for this mocked function
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Check called just one time
     * @return boolean
     */
    public function calledOnce()
    {
        return 1 === count($this->calls);
    }

    /**
     * Check how many times this function has been called
     * @param type $numberOfCalls
     * @return boolean
     */
    public function calledMore($numberOfCalls)
    {
        return $numberOfCalls === count($this->calls);
    }

    /**
     * Check if function has been called with the given arguments
     * @param array    $args
     * @param integer  $callNumber  Optional
     * @return boolean
     */
    public function calledWithArgs(array $args, $callNumber = 1)
    {
        $index = $callNumber - 1;

        if (!isset($this->calls[$index])) {
            return false;
        }

        return $this->calls[$index] === $args;
    }

    /**
     * Check if function has been called with the given argument value (only check one argument)
     * @param mixed    $argValue
     * @param integer  $argIndex    Optional, default first argument
     * @param integer  $callNumber  Optional
     * @return boolean
     */
    public function calledWithArgValue($argValue, $argIndex = 0, $callNumber = 1)
    {
        $index = $callNumber - 1;

        if (!isset($this->calls[$index])) {
            return false;
        }

        $args = $this->calls[$index];

        if (!isset($args[$argIndex])) {
            return false;
        }

        return $args[$argIndex] === $argValue;
    }

}

/*?>*/
