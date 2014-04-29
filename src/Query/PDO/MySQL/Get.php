<?php

namespace Imhonet\Connection\Query\PDO\MySQL;


use Imhonet\Connection\Query\PDO;

/**
 * @todo check last query
 */
class Get extends PDO\Get
{
    const SQL_WRAP_COUNT = 'SELECT COUNT(*) FROM (%s) as temp';

    private $count;
    private $count_total;

    /**
     * @var \PDOException|null
     */
    private $err_count_total;
    /**
     * @var \PDOException|null
     */
    private $err_count;

    /**
     * @inheritdoc
     */
    public function getCountTotal()
    {
        $result = null;

        if ($this->isSCFRRequired()) {
            if (!$this->isSCFR()) {
                //@todo change statement
            }

            //@todo check isExecuted
            $this->getResponse();
            $result = $this->getFoundRows();
        } elseif ($this->isExecuted()) {
            $result = $this->getFoundRows();
        } elseif ($this->isGroupBy() && !$this->isLimit()) {
            $result = $this->getSelectCount();
        } elseif ($this->isLimit()) { //@todo check offset
            //@todo cut offset
        } else {
            $result = $this->getCount();
        }

        return $result;
    }

    private function hasCountTotal()
    {
        return $this->count_total !== null || $this->err_count_total !== null;
    }

    /**
     * @inheritdoc
     */
    public function getCount()
    {
        if (!$this->hasCount()) {
            if ($this->isExecuted() && !$this->isSCFR()) {
                $this->count = $this->getCountAfterExecute();
            } elseif ($this->hasCountTotal()) {
                $this->count = $this->getCountAfterCountTotal();
            } else {
                $this->count = $this->getCountWrapped();
            }
        }

        return $this->count;
    }

    private function getCountAfterExecute()
    {
        try {
            $found_rows = $this->getFoundRows();
        } catch (\PDOException $e) {
            $this->err_count = $e;
        }

        return isset($found_rows) ? $found_rows - $this->getOffsetWithLimit()['offset'] : $this->count;
    }

    private function getCountAfterCountTotal()
    {
        list($offset, $limit) = $this->getOffsetWithLimit();
        $diff = $this->count_total - $offset;
        return $diff > $limit ? $limit : $diff;
    }

    private function getCountWrapped()
    {
        $result = null;

        try {
            $result = $this->getSelectCount();
        } catch (\PDOException $e) {
            $this->err_count = $e;
        }

        return $result;
    }

    private function hasCount()
    {
        return $this->count !== null || $this->err_count !== null;
    }

    /**
     * @return string
     * @throws \PDOException
     */
    private function getFoundRows()
    {
        return $this->getResource()->query('SELECT FOUND_ROWS()')->fetchColumn();
    }

    /**
     * @return string
     * @throws \PDOException
     */
    private function getSelectCount()
    {
        $stmt = $this->getStmt($this->getStatementCount(), $this->getParams());
        $stmt->execute();
        $result = $stmt->fetchColumn();

        return $result;
    }

    public function getOffsetWithLimit()
    {
        $result = array('offset' => null, 'limit' => null);

        $cnt = preg_match(
            '/(LIMIT\s+(?P<limit_or_offset>\d+|\?)\s*?(,\s*?(?P<limit>\d+|\?))?)?\s*?(?(limit)|OFFSET\s+(?P<offset>\d+|\?))/',
            $this->getStatement(),
            $match
        );

        if ($cnt) {
            $params = $this->getParams();
            end($params);

            if (isset($match['offset'])) {
                $result['offset'] = $match['offset'] == '?' ? current($params) : (int) $match['offset'];

                if (isset($match['limit_or_offset'])) {
                    $result['limit'] = $match['limit_or_offset'] == '?' ? prev($params) : (int) $match['limit_or_offset'];
                }
            } else {
                $result['limit'] = $match['limit'] == '?' ? current($params) : (int) $match['limit'];
                $result['offset'] = $match['limit_or_offset'] == '?' ? prev($params) : (int) $match['limit_or_offset'];
            }
        }

        return $result;
    }

    /**
     * @todo performance tests
     * @return string
     */
    private function getStatementCount()
    {
        return sprintf(self::SQL_WRAP_COUNT, $this->getStatement());
    }

    /**
     * SQL_CALC_FOUND_ROWS check
     * @return bool
     */
    private function isSCFR()
    {
        return stripos($this->getStatement(), 'SQL_CALC_FOUND_ROWS') !== false;
    }

    private function isSCFRRequired()
    {
        return $this->isGroupBy() && $this->isLimit();
    }

    private function isGroupBy()
    {
        return stripos($this->getStatement(), 'GROUP ');
    }

    private function isLimit()
    {
        return stripos($this->getStatement(), 'LIMIT ');
    }

    //@todo isOffset

    /*private function getStatementCount()
    {
        $stmt = $this->getStatement();
        $start = stripos($stmt, 'SELECT ') + 7;
        $end = stripos($stmt, 'FROM ') - 1;

        return substr_replace($stmt, 'COUNT(*)', $start, $end - $start);
    }*/

    /**
     * @todo check last executed query
     * @return bool
     */
    private function isExecuted()
    {
        return $this->hasResponse();
    }

    /**
     * @inheritdoc
     */
    public function getLastId()
    {
    }

}
