<?php
namespace excl_json_api;


class excl_utilityTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;
    protected $excl_utility;

    function __construct()
    {
        require_once(dirname(__FILE__) . '/../../../wp-content/plugins/excl_json_api/01/excl_utility.php');
        $this->excl_utility = new \api\v01\EXCL_Utility();
    }

    protected function _before()
    {
        
    }

    protected function _after()
    {
    }

    // tests
    public function testValueOr()
    {
        $this->assertEquals("OK", $this->excl_utility->valueOr("", "OK"));
        $this->assertEquals("OK", $this->excl_utility->valueOr(null, "OK"));
        $this->assertEquals("Diff", $this->excl_utility->valueOr("Diff", "OK"));
    }

}