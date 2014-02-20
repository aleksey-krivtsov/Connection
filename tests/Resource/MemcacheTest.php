<?php

namespace Imhonet\Connection\Test\Resource;

use Imhonet\Connection\Resource\IResource;
use Imhonet\Connection\Resource\Memcache;

class MemcacheTest extends \PHPUnit_Framework_TestCase
{
    const HOST = 'localhost';
    const PORT = 11211;
    const PORT_ALT = 11311;

    /**
     * @var IResource
     */
    private $resource;

    protected function setUp()
    {
        $this->resource = new Memcache();
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Imhonet\Connection\Resource\IResource', $this->resource);
    }

    public function testHandler()
    {
        $this->assertInstanceOf('\Memcache', $this->resource->getHandle());
    }

    public function testAddServer()
    {
        $this->resource
            ->setHost(self::HOST)
            ->setPort(self::PORT)
        ;
        $this->assertArrayHasKey(
            self::HOST . ':' . self::PORT,
            $this->resource->getHandle()->getExtendedStats()
        );
    }

    public function testAddServers()
    {
        $this->resource
            ->setHost(self::HOST . Memcache::DELIMITER . self::HOST)
            ->setPort(self::PORT . Memcache::DELIMITER . self::PORT_ALT)
        ;
        $this->assertEquals(
            array(self::HOST . ':' . self::PORT, self::HOST . ':' . self::PORT_ALT),
            array_keys($this->resource->getHandle()->getExtendedStats())
        );
    }

    protected function tearDown()
    {
        $this->resource = null;
    }

}