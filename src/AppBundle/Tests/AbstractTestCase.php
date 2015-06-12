<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 12.06.15
 * Time: 10:49
 */

namespace AppBundle\Tests;

class AbstractTestCase  extends \PHPUnit_Framework_TestCase{

    public function setUp(){

    }

    public function tearDown()
    {
        \Mockery::close();
    }

    public function getAbsoluteUploadFixturePath($relativePath){
        $result = __DIR__.'/fixtures/uploads/'.$relativePath;
        $this->assertTrue(is_file($result), 'Fixture file does not exists: '.$result);
        return $result;
    }


}