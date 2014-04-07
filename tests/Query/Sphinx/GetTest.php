<?php

namespace Imhonet\Connection\Test\Query\Sphinx;

use Imhonet\Connection\Query\Sphinx\Get;
use Imhonet\Connection\Resource\IResource;

/**
 * Class GetTest
 * @covers \Imhonet\Connection\Query\Sphinx\Get::
 * @coversDefaultClass \Imhonet\Connection\Query\Sphinx\Get
 * @package Imhonet\Connection\Test\Query
 */
class GetTest extends \PHPUnit_Framework_TestCase
{
    private $index = 'index_test';
    private $sort = 'sort_field';
    private $limit = 3;
    private $offset = 6;
    private $response = '[{
        "error":"",
        "warning":"",
        "status":0,
        "fields":["title", "description"],
        "attrs":{"attr_id":1, "id":1, "sort_field":1},
        "matches":{
            "1148685":{
                "weight":"1",
                "attrs":{"attr_id":1148685, "id":1148685, "sort_field":3}
            },
            "9764681":{
                "weight":"1",
                "attrs":{"attr_id":9764681, "id":9764681, "sort_field":50}
            },
            "9782242":{
                "weight":"1",
                "attrs":{"attr_id":9782242, "id":9782242, "sort_field":51}
            }
        },
        "total":"1000",
        "total_found":"79082",
        "time":"0.015"
    }]';

    /**
     * @var Get
     */
    private $query;

    protected function setUp()
    {
        $this->query = new Get();
        $this->query->addIndex($this->index);
    }

    /**
     * @covers ::__construct
     */
    public function testCreate()
    {
        $this->assertInstanceOf('Imhonet\\Connection\\Query\\Query', $this->query);
    }

    /**
     * @covers ::execute
     */
    public function testExecute()
    {
        $this->query->setResource($this->getResource());
        $this->assertEquals(json_decode($this->response, true), $this->query->execute());
    }

    /**
     * @covers ::setLimit
     */
    public function testExecuteLimit()
    {
        $this->query
            ->setResource($this->getResource(true))
            ->setLimit($this->limit, $this->offset)
        ;
        $this->query->execute();
    }

    /**
     * @covers ::setOrder
     */
    public function testExecuteSort()
    {
        $this->query
            ->setResource($this->getResource(true, true))
            ->setLimit($this->limit, $this->offset)
            ->setOrder($this->sort)
        ;
        $this->query->execute();
    }

    /**
     * @covers ::getErrorCode
     */
    public function testErrorCode()
    {
        $this->query->setResource($this->getResource());
        $this->assertEquals(0, $this->query->getErrorCode());
    }

    /**
     * @covers ::getCount
     * @covers ::getCountTotal
     */
    public function testCount()
    {
        $this->query->setResource($this->getResource());
        $this->assertEquals($this->limit, $this->query->getCount());
        $this->assertEquals(1000, $this->query->getCountTotal());
    }

    public function testFailure()
    {
        $this->query->setResource($this->getResourceFailed());
        $this->assertEquals(false, $this->query->execute());
    }

    public function testFailureErrorCode()
    {
        $this->query->setResource($this->getResourceFailed());
        $this->assertNotEquals(0, $this->query->getErrorCode());
    }

    /**
     * @param bool $use_limit
     * @param bool $use_sort
     * @return IResource
     */
    private function getResource($use_limit = false, $use_sort = false)
    {
        $sph = $this->getMock('\\SphinxClient', array('runQueries', 'addQuery', 'SetSortMode', 'SetLimits'));

        if ($use_limit) {
            $sph->expects($this->once())
                ->method('SetLimits')
                ->with($this->offset, $this->limit)
            ;
        }

        if ($use_sort) {
            $sph->expects($this->once())
                ->method('SetSortMode')
                ->with(\SPH_SORT_ATTR_ASC, $this->sort)
            ;
        }

        $sph
            ->expects($this->at(0 + $use_limit + $use_sort))
            ->method('addQuery')
            ->with('', $this->index)
        ;
        $sph
            ->expects($this->at(1 + $use_limit + $use_sort))
            ->method('runQueries')
            ->will($this->returnValue(json_decode($this->response, true)))
        ;

        $resource = $this->getMock('Imhonet\\Connection\\Resource\\Sphinx', array('getHandle'));
        $resource
            ->expects($this->any())
            ->method('getHandle')
            ->will($this->returnValue($sph))
        ;

        return $resource;
    }

    /**
     * @return IResource
     */
    private function getResourceFailed()
    {
        $sph = $this->getMock('\\SphinxClient', array('runQueries'));

        $sph
            ->expects($this->once())
            ->method('runQueries')
            ->will($this->returnValue(false))
        ;

        $resource = $this->getMock('Imhonet\\Connection\\Resource\\Sphinx', array('getHandle'));
        $resource
            ->expects($this->any())
            ->method('getHandle')
            ->will($this->returnValue($sph))
        ;

        return $resource;
    }

    protected function tearDown()
    {
        $this->query = null;
    }

}
