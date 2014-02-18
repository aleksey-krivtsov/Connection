<?php

namespace Imhonet\Connection\Test\Query\Memcached;

use Imhonet\Connection\Query\Memcached\Set;
use Imhonet\Connection\Resource\IResource;

class SetTest extends \PHPUnit_Framework_TestCase
{
    private $data = array(
        'false' => false,
        'true' => true,
        'null' => null,
        'int' => 42,
        'string' => 'forty two',
        'arr' => array(42),
    );

    /**
     * @var Set
     */
    private $query;

    protected function setUp()
    {
        $this->query = new Set();
        $this->query->setData($this->data);
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Imhonet\Connection\Query\Base', $this->query);
    }

    public function testExecute()
    {
        $this->query->setResource($this->getResource());
        $this->assertEquals($this->query->execute(), true);
        $this->assertEquals($this->query->getErrorCode(), 0);
    }

    public function testFailure()
    {
        $this->query->setResource($this->getResourceFailed());
        $this->assertEquals($this->query->execute(), false);
        $this->assertNotEquals($this->query->getErrorCode(), 0);
    }

    /**
     * @return IResource
     */
    private function getResource()
    {
        $memcached = $this->getMock('\Memcached', array('setMulti'));
        $memcached
            ->expects($this->any())
            ->method('setMulti')
            ->with($this->data)
            ->will($this->returnValue(true))
        ;

        $resource = $this->getMock('Imhonet\Connection\Resource\Memcached', array('getHandle'));
        $resource
            ->expects($this->any())
            ->method('getHandle')
            ->will($this->returnValue($memcached))
        ;

        return $resource;
    }

    /**
     * @return IResource
     */
    private function getResourceFailed()
    {
        $memcached = $this->getMock('\Memcached', array('setMulti', 'getResultCode'));
        $memcached
            ->expects($this->any())
            ->method('setMulti')
            ->withAnyParameters()
            ->will($this->returnValue(false))
        ;
        $memcached
            ->expects($this->any())
            ->method('getResultCode')
            ->will($this->returnValue(\Memcached::RES_FAILURE))
        ;

        $resource = $this->getMock('Imhonet\Connection\Resource\Memcached', array('getHandle'));
        $resource
            ->expects($this->any())
            ->method('getHandle')
            ->will($this->returnValue($memcached))
        ;

        return $resource;
    }

    protected function tearDown()
    {
        $this->query = null;
    }

}