<?php

namespace Imhonet\Connection\Test\Resource;

use Imhonet\Connection\Resource\IResource;
use Imhonet\Connection\Resource\Memcached;

class MemcachedTest extends \PHPUnit_Framework_TestCase
{
    const HOST = 'localhost';
    const PORT = 11211;

    /**
     * @var IResource
     */
    private $resource;

    protected function setUp()
    {
        $this->resource = new Memcached();
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Imhonet\Connection\Resource\IResource', $this->resource);
    }

    public function testHandler()
    {
        $this->assertInstanceOf('\Memcached', $this->resource->getHandle());
    }

    public function testAddServer()
    {
        $this->resource
            ->setHost(self::HOST)
            ->setPort(self::PORT)
        ;
        $this->assertEquals(
            array(array('host' => self::HOST, 'port' => self::PORT)),
            $this->resource->getHandle()->getServerList()
        );
    }

    public function testAddServers()
    {
        $this->resource
            ->setHost(self::HOST . Memcached::DELIMITER . self::HOST)
            ->setPort(self::PORT . Memcached::DELIMITER . self::PORT)
        ;
        $this->assertEquals(
            array(
                array('host' => self::HOST, 'port' => self::PORT),
                array('host' => self::HOST, 'port' => self::PORT),
            ),
            $this->resource->getHandle()->getServerList()
        );
    }

    public function testOption()
    {
        define('MEMCACHED_TCP_NODELAY', true);
        $this->assertEquals(1, $this->resource->getHandle()->getOption(\Memcached::OPT_TCP_NODELAY));
    }

    protected function tearDown()
    {
        $this->resource = null;
    }

}