<?php

namespace Imhonet\Connection\Test\DataFormat\Hash\PDO;

use Imhonet\Connection\DataFormat\IHash;
use Imhonet\Connection\DataFormat\Hash\PDO\Get;

class GetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IHash
     */
    private $formater;

    protected function setUp()
    {
        $this->formater = new Get();
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Imhonet\Connection\DataFormat\IHash', $this->formater);
    }

    public function testData()
    {
        $this->formater->setData($this->getStmt());
        $this->assertEquals(null, $this->formater->formatData());
    }

    public function testValue()
    {
        $this->formater->setData($this->getStmt());
        $this->assertNull($this->formater->formatValue());
    }

    public function testFailure()
    {
        $this->formater->setData(false);
        $this->assertEquals(null, $this->formater->formatData());
    }

    public function getStmt()
    {
        return $this->getMock('\\PDOStatement');
    }

    protected function tearDown()
    {
        $this->formater = null;
    }

}
