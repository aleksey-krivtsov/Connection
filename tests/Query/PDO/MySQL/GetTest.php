<?php

namespace Imhonet\Connection\Test\Query\PDO\MySQL;

use Imhonet\Connection\Query\PDO\MySQL\Get;

class GetTest extends \PHPUnit_Framework_TestCase
{
    private $statement = '
        SELECT user_id, name
        FROM user
        LIMIT %s, %s
    ';

    private $filter_offset = 10;
    private $filter_limit = 10;

    public function testCountFirst()
    {
        $stmt = $this->getMock('\\PDOStatement', array('bindValue', 'execute', 'fetchColumn'));
        $stmt
            ->expects($this->at(0))
            ->method('bindValue')
            ->with(1, $this->filter_offset, \PDO::PARAM_INT)
            ->will($this->returnValue(true))
        ;
        $stmt
            ->expects($this->at(1))
            ->method('bindValue')
            ->with(2, $this->filter_offset + $this->filter_limit, \PDO::PARAM_INT)
            ->will($this->returnValue(true))
        ;
        $stmt
            ->expects($this->at(2))
            ->method('execute')
            ->will($this->returnValue(true))
        ;
        $stmt
            ->expects($this->at(3))
            ->method('fetchColumn')
            ->will($this->returnValue(7))
        ;

        $pdo = $this->getMock('\\PDO', array('prepare'), array('sqlite::memory:'));
        $pdo
            ->expects($this->at(0))
            ->method('prepare')
            ->with(sprintf(Get::SQL_WRAP_COUNT, $this->getPreparedStatement()))
            ->will($this->returnValue($stmt))
        ;

        $resource = $this->getMock('Imhonet\Connection\Resource\PDO\MySQL', array('getHandle'));
        $resource
            ->expects($this->any())
            ->method('getHandle')
            ->will($this->returnValue($pdo))
        ;

        $query = (new Get())
            ->setResource($resource)
            ->addStatement($this->statement)
            ->addParams(
                $this->filter_offset,
                $this->filter_offset + $this->filter_limit
            )
        ;

        $this->assertEquals(7, $query->getCount());
    }

    public function testCountAfterExecute()
    {
        $stmt = $this->getMock('\\PDOStatement', array('bindValue', 'execute', 'fetchColumn'));
        $stmt
            ->expects($this->at(0))
            ->method('bindValue')
            ->with(1, $this->filter_offset, \PDO::PARAM_INT)
            ->will($this->returnValue(true))
        ;
        $stmt
            ->expects($this->at(1))
            ->method('bindValue')
            ->with(2, $this->filter_offset + $this->filter_limit, \PDO::PARAM_INT)
            ->will($this->returnValue(true))
        ;
        $stmt
            ->expects($this->at(2))
            ->method('execute')
            ->will($this->returnValue(true))
        ;

        $stmt_found_rows = $this->getMock('\\PDOStatement', array('fetchColumn'));
        $stmt_found_rows
            ->expects($this->at(0))
            ->method('fetchColumn')
            ->will($this->returnValue(13))
        ;

        $pdo = $this->getMock('\\PDO', array('prepare', 'query'), array('sqlite::memory:'));
        $pdo
            ->expects($this->at(0))
            ->method('prepare')
            ->with($this->getPreparedStatement())
            ->will($this->returnValue($stmt))
        ;
        $pdo
            ->expects($this->at(1))
            ->method('query')
            ->with('SELECT FOUND_ROWS()')
            ->will($this->returnValue($stmt_found_rows))
        ;

        $resource = $this->getMock('Imhonet\Connection\Resource\PDO\MySQL', array('getHandle'));
        $resource
            ->expects($this->any())
            ->method('getHandle')
            ->will($this->returnValue($pdo))
        ;

        $query = (new Get())
            ->setResource($resource)
            ->addStatement($this->statement)
            ->addParams(
                $this->filter_offset,
                $this->filter_offset + $this->filter_limit
            )
        ;
        $query->execute();

        $this->assertEquals(3, $query->getCount());
    }

    private function getPreparedStatement()
    {
        return sprintf($this->statement, '?', '?');
    }

}
