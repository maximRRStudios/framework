<?php
/**
 * Created by PhpStorm.
 * User: RiDeR
 * Date: 27.02.14
 * Time: 8:01
 */
require_once __DIR__ . "/../classes/main/Core.php";

class CoreTest extends PHPUnit_Framework_TestCase {

    protected function SetUp()
    {
        $config = array("components" => array());
        Core::init($config);
    }

    public function testInit(){
        $app = Core::getInstance();
        $this->assertInstanceOf("Core", $app);
    }
}