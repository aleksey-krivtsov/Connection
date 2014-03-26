<?php

namespace Imhonet\Connection\Test\DataFormat\Scalar\PDO;

use Imhonet\Connection\DataFormat\IScalar;
use Imhonet\Connection\DataFormat\Scalar\PDO\Get;

class GetTest extends \PHPUnit_Framework_TestCase
{
    const VALUE = 42;

    /**
     * @var IScalar
     */
    private $formater;

    protected function setUp()
    {
        $this->formater = new Get();
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Imhonet\Connection\DataFormat\IScalar', $this->formater);
    }

    public function testData()
    {
        $this->formater->setData($this->getStmt());
        $this->assertEquals(array(), $this->formater->formatData());
    }

    public function testValue()
    {
        $this->formater->setData($this->getStmt());
        $this->assertEquals(self::VALUE, $this->formater->formatValue());
    }

    public function testFailure()
    {
        $this->formater->setData(false);
        $this->assertNull($this->formater->formatValue());
    }

    public function getStmt()
    {
        $stmt = $this->getMock('\\PDOStatement', array('fetchColumn'));
        $stmt
            ->expects($this->any())
            ->method('fetchColumn')
            ->will($this->returnValue(self::VALUE))
        ;

        return $stmt;
    }

    protected function tearDown()
    {
        $this->formater = null;
    }

}
